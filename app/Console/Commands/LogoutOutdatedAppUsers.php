<?php

namespace App\Console\Commands;

use App\Models\FieldKonnectAppSetting;
use App\Models\MobileUserLoginDetails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * mobile_user_login_details.app_version is only written at login, so users
 * who updated the app without logging out still show their old version.
 * Revoking their tokens sends them back to the login screen, and the next
 * login records the version they are actually running.
 */
class LogoutOutdatedAppUsers extends Command
{
    protected $signature = 'mobile-users:logout-outdated
                            {--min-version= : Minimum version (default: live Android version from app settings)}
                            {--ios-version= : Minimum iOS version (default: live iOS version from app settings)}
                            {--force : Actually log users out (without it, only lists them)}';

    protected $description = 'Log out field app users whose recorded app version is older than the live version';

    public function handle(): int
    {
        $setting = FieldKonnectAppSetting::first();
        $android = trim((string) ($this->option('min-version') ?: $setting?->app_version));
        $ios = trim((string) ($this->option('ios-version') ?: $setting?->ios_app_version));

        if ($android === '') {
            $this->error('No live app version found. Pass --min-version=5.9');
            return self::FAILURE;
        }

        $rows = MobileUserLoginDetails::with('user:id,name')
            ->where('app', '2')
            ->where('login_status', '1')
            ->whereNotNull('user_id')
            ->get()
            ->filter(function ($row) use ($android, $ios) {
                $isIos = strtolower((string) $row->device_type) === 'ios';
                $min = $isIos ? $ios : $android;
                if ($min === '') {
                    return false;
                }
                $current = trim((string) $row->app_version);
                return $current === '' || version_compare($current, $min, '<');
            });

        if ($rows->isEmpty()) {
            $this->info('No outdated logged-in users found.');
            return self::SUCCESS;
        }

        $this->table(
            ['User ID', 'Name', 'Device', 'Recorded version', 'Last login'],
            $rows->map(fn ($r) => [
                $r->user_id, $r->user->name ?? '-', $r->device_type, $r->app_version ?: '-', $r->last_login_date,
            ])->values()->all()
        );
        $this->line("Android min: {$android}" . ($ios !== '' ? " | iOS min: {$ios}" : ' | iOS: skipped (no version set)'));

        if (!$this->option('force')) {
            $this->warn($rows->count() . ' users listed. Run again with --force to log them out.');
            return self::SUCCESS;
        }

        $userIds = $rows->pluck('user_id')->unique()->values()->all();

        [$tokens, $logins] = DB::transaction(function () use ($userIds) {
            $tokens = DB::table('oauth_access_tokens')
                ->whereIn('user_id', $userIds)
                ->where('revoked', false)
                ->update(['revoked' => true]);

            $logins = DB::table('mobile_user_login_details')
                ->where('app', '2')
                ->whereIn('user_id', $userIds)
                ->update(['login_status' => '0', 'updated_at' => now()]);

            return [$tokens, $logins];
        });

        $this->info("Done: {$tokens} tokens revoked, {$logins} login records set to Logout.");

        return self::SUCCESS;
    }
}
