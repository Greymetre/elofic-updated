<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CheckIn;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class UserMonthlyAnalysisService
{
    public function build(string $startDate, string $endDate, ?int $userId = null): array
    {
        $today = now()->format('Y-m-d');
        $effectiveEndDate = min($endDate, $today);
        $dates = $startDate <= $effectiveEndDate
            ? collect(CarbonPeriod::create($startDate, $effectiveEndDate))
                ->map(fn ($date) => $date->format('Y-m-d'))
                ->values()
            : collect();

        $allowedUserIds = getUsersReportingToAuth();
        $users = User::query()
            ->whereDoesntHave('roles', fn ($query) => $query->whereIn('id', config('constants.customer_roles')))
            ->whereIn('id', $allowedUserIds)
            ->when($userId, fn ($query) => $query->where('id', $userId))
            ->with('getdesignation:id,designation_name')
            ->select('id', 'employee_codes', 'name', 'designation_id')
            ->orderBy('name')
            ->get();

        $userIds = $users->pluck('id');
        $attendance = Attendance::query()
            ->whereIn('user_id', $userIds)
            ->when($dates->isNotEmpty(), fn ($query) => $query->whereBetween('punchin_date', [$startDate, $effectiveEndDate]))
            ->when($dates->isEmpty(), fn ($query) => $query->whereRaw('1 = 0'))
            ->get(['user_id', 'punchin_date', 'working_type'])
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->keyBy(fn ($row) => Carbon::parse($row->punchin_date)->format('Y-m-d')));

        $visits = CheckIn::query()
            ->with(['customer.customertypes:id,customertype_name', 'secondaryCustomer:id,type'])
            ->whereIn('user_id', $userIds)
            ->when($dates->isNotEmpty(), fn ($query) => $query->whereBetween('checkin_date', [$startDate, $effectiveEndDate]))
            ->when($dates->isEmpty(), fn ($query) => $query->whereRaw('1 = 0'))
            ->whereNull('deleted_at')
            ->get(['id', 'user_id', 'customer_id', 'entity_type', 'entity_id', 'checkin_date'])
            ->groupBy(fn ($visit) => $visit->user_id . '|' . Carbon::parse($visit->checkin_date)->format('Y-m-d'));

        $rows = $users->map(function ($user) use ($dates, $attendance, $visits) {
            $totals = ['mechanic' => 0, 'retailer' => 0, 'distributor' => 0];
            $workingDays = 0;
            $days = [];

            foreach ($dates as $date) {
                $attendanceRow = $attendance->get($user->id, collect())->get($date);
                $onLeave = $attendanceRow && str_contains(strtolower((string) $attendanceRow->working_type), 'leave');
                $hasAttendance = (bool) $attendanceRow;

                if ($onLeave) {
                    $days[$date] = ['status' => 'L', 'mechanic' => null, 'retailer' => null, 'distributor' => null];
                    continue;
                }

                if (!$hasAttendance) {
                    $days[$date] = ['status' => 'A', 'mechanic' => null, 'retailer' => null, 'distributor' => null];
                    continue;
                }

                $counts = ['mechanic' => 0, 'retailer' => 0, 'distributor' => 0];
                foreach ($visits->get($user->id . '|' . $date, collect()) as $visit) {
                    $type = $this->visitType($visit);
                    if ($type) {
                        $counts[$type]++;
                        $totals[$type]++;
                    }
                }
                $workingDays++;
                $days[$date] = ['status' => 'P'] + $counts;
            }

            return [
                'employee_code' => $user->employee_codes ?: '-',
                'name' => $user->name,
                'designation' => optional($user->getdesignation)->designation_name ?: '-',
                'days' => $days,
                'totals' => $totals,
                'working_days' => $workingDays,
                'averages' => collect($totals)->map(fn ($total) => $workingDays ? round($total / $workingDays, 2) : 0)->all(),
            ];
        })->all();

        return ['dates' => $dates->all(), 'rows' => $rows];
    }

    private function visitType(CheckIn $visit): ?string
    {
        if ($visit->entity_type === 'distributor') {
            return 'distributor';
        }

        $type = $visit->entity_type === 'secondary_customer'
            ? optional($visit->secondaryCustomer)->type
            : optional(optional($visit->customer)->customertypes)->customertype_name;
        $type = strtoupper((string) $type);

        if (str_contains($type, 'MECHANIC')) {
            return 'mechanic';
        }
        if (str_contains($type, 'RETAIL')) {
            return 'retailer';
        }
        if (str_contains($type, 'DISTRIBUT')) {
            return 'distributor';
        }

        return null;
    }
}
