<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Daily attendance push alerts for field employees.
 *
 *  attendance:reminders punchin   -> employees who have not punched in today        (scheduled 09:30)
 *  attendance:reminders punchout  -> employees punched in today but not punched out (scheduled 20:30)
 *
 * Skipped: inactive users, customer-role users (distributors as users), users without an FCM token,
 * users on leave today (applied / approved, not rejected), branch / company holidays, configured week-offs.
 * A user gets each reminder at most once per day (checked against the notifications table).
 */
class SendAttendanceReminders extends Command
{
    protected $signature = 'attendance:reminders
                            {type : punchin or punchout}
                            {--dry-run : List the users that would be notified without sending}';

    protected $description = 'Push a reminder to employees who have not punched in / punched out today';

    private const TITLES = [
        'punchin' => 'Punch In Reminder',
        'punchout' => 'Punch Out Reminder',
    ];

    private const MESSAGES = [
        'punchin' => "You haven't punched in yet today. Please mark your attendance in the app.",
        'punchout' => "You haven't punched out yet today. Please punch out to complete your attendance.",
    ];

    /** Days on which no reminder is sent (no week-off concept exists in the CRM). */
    private const SKIP_DAYS = [Carbon::SUNDAY];

    public function handle(): int
    {
        $type = strtolower((string) $this->argument('type'));
        if (!isset(self::TITLES[$type])) {
            $this->error('Type must be "punchin" or "punchout".');
            return Command::INVALID;
        }

        $now = Carbon::now('Asia/Kolkata');
        $today = $now->toDateString();
        $dryRun = (bool) $this->option('dry-run');

        if (in_array($now->dayOfWeek, self::SKIP_DAYS, true)) {
            $this->info("No {$type} reminders on {$now->format('l')}.");
            return Command::SUCCESS;
        }

        $users = $this->candidateUsers();
        if ($users->isEmpty()) {
            $this->info('No active employees with a device token.');
            return Command::SUCCESS;
        }

        $ids = $users->pluck('id')->all();
        $skip = $this->usersOnLeave($today, $ids)
            ->merge($this->usersOnHoliday($today, $users))
            ->merge($this->alreadyReminded($today, $type, $ids))
            ->unique()
            ->flip();

        // user_id => attendance id (null for punch-in reminders)
        $targets = $type === 'punchin'
            ? $this->notPunchedIn($today, $ids)
            : $this->notPunchedOut($today, $ids);
        $targets = $targets->reject(fn ($attendanceId, $userId) => isset($skip[$userId]));

        $sent = 0;
        $failed = 0;
        foreach ($targets as $userId => $attendanceId) {
            if ($dryRun) {
                $this->line("would notify user #{$userId}");
                continue;
            }
            $ok = SendPushNotification($userId, self::MESSAGES[$type], 'attendance', $attendanceId, self::TITLES[$type]);
            $ok ? $sent++ : $failed++;
        }

        $summary = [
            'type' => $type,
            'date' => $today,
            'employees' => $users->count(),
            'skipped_leave_holiday_or_already_sent' => $skip->count(),
            'targets' => $targets->count(),
            'sent' => $sent,
            'failed' => $failed,
            'dry_run' => $dryRun,
        ];
        Log::info('Attendance reminder run', $summary);
        $this->info(json_encode($summary));

        return Command::SUCCESS;
    }

    /** Active employees (not customer-role users) that have an FCM token. */
    private function candidateUsers()
    {
        $customerRoleIds = (array) config('constants.customer_roles', []);

        return DB::table('users')
            ->where('active', 'Y')
            ->whereNull('deleted_at')
            ->whereNotNull('notification_id')
            ->where('notification_id', '!=', '')
            ->when(!empty($customerRoleIds), fn ($query) => $query->whereNotIn('id', function ($sub) use ($customerRoleIds) {
                $sub->select('model_id')
                    ->from(config('permission.table_names.model_has_roles', 'model_has_roles'))
                    ->where('model_type', (new User)->getMorphClass())
                    ->whereIn('role_id', $customerRoleIds);
            }))
            ->select('id', 'branch_id')
            ->get();
    }

    /** Users with no punched-in attendance row today: [user_id => null]. */
    private function notPunchedIn(string $today, array $ids)
    {
        $punched = DB::table('attendances')
            ->whereDate('punchin_date', $today)
            ->whereIn('user_id', $ids)
            ->whereNotNull('punchin_time')
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('user_id')
            ->flip();

        return collect($ids)
            ->reject(fn ($id) => isset($punched[$id]))
            ->mapWithKeys(fn ($id) => [$id => null]);
    }

    /** Users punched in today (not a leave entry) and not yet punched out: [user_id => attendance_id]. */
    private function notPunchedOut(string $today, array $ids)
    {
        return DB::table('attendances')
            ->whereDate('punchin_date', $today)
            ->whereIn('user_id', $ids)
            ->whereNotNull('punchin_time')
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->whereNull('punchout_time')->orWhereNull('punchout_date');
            })
            ->where(function ($query) {
                $query->whereNull('working_type')->orWhere('working_type', 'not like', '%leave%');
            })
            ->orderByDesc('id')
            ->get(['id', 'user_id'])
            ->unique('user_id')
            ->mapWithKeys(fn ($row) => [$row->user_id => $row->id]);
    }

    /** Leave covering today that is applied or approved (status 2 = rejected). */
    private function usersOnLeave(string $today, array $ids)
    {
        return DB::table('leaves')
            ->whereIn('user_id', $ids)
            ->whereDate('from_date', '<=', $today)
            ->whereDate('to_date', '>=', $today)
            ->when(Schema::hasColumn('leaves', 'status'), fn ($query) => $query->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 2);
            }))
            ->where(function ($query) {
                $query->whereNull('active')->orWhere('active', 'Y');
            })
            ->pluck('user_id');
    }

    /** holidays.holiday_date is a comma separated list; branch NULL = applies to everyone. */
    private function usersOnHoliday(string $today, $users)
    {
        $branches = collect();
        $allBranches = false;
        $holidays = DB::table('holidays')
            ->where('active', 'Y')
            ->whereNull('deleted_at')
            ->get(['holiday_date', 'branch']);

        foreach ($holidays as $holiday) {
            foreach (explode(',', (string) $holiday->holiday_date) as $date) {
                $date = trim($date);
                if ($date === '') {
                    continue;
                }
                try {
                    $isToday = Carbon::parse(str_replace('/', '-', $date))->toDateString() === $today;
                } catch (\Throwable $e) {
                    continue;
                }
                if ($isToday) {
                    if (empty($holiday->branch)) {
                        $allBranches = true;
                    } else {
                        $branches->push((int) $holiday->branch);
                    }
                }
            }
        }

        if ($allBranches) {
            return $users->pluck('id');
        }
        $branches = $branches->unique()->flip();

        return $users->filter(fn ($user) => isset($branches[(int) $user->branch_id]))->pluck('id');
    }

    /** Users who already got this reminder today. */
    private function alreadyReminded(string $today, string $type, array $ids)
    {
        return DB::table('notifications')
            ->whereIn('user_id', $ids)
            ->where('type', self::TITLES[$type])
            ->whereDate('created_at', $today)
            ->pluck('user_id');
    }
}
