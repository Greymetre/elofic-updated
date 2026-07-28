<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LogoutAllMobileUsers extends Command
{
    protected $signature = 'mobile-users:logout-all
                            {--force : Skip the confirmation prompt}';

    protected $description = 'Revoke all Passport access tokens and log out all mobile users';

    public function handle(): int
    {
        if (!$this->option('force') &&
            !$this->confirm('This will sign every mobile user out. Continue?')) {
            $this->info('No users were logged out.');
            return self::SUCCESS;
        }

        [$revokedTokens, $updatedLogins] = DB::transaction(function () {
            $revokedTokens = DB::table('oauth_access_tokens')
                ->where('revoked', false)
                ->update(['revoked' => true]);

            $updatedLogins = DB::table('mobile_user_login_details')
                ->where('login_status', '1')
                ->update([
                    'login_status' => '0',
                    'updated_at' => now(),
                ]);

            return [$revokedTokens, $updatedLogins];
        });

        $this->info(
            "Logout complete: {$revokedTokens} tokens revoked; "
            ."{$updatedLogins} mobile login records updated."
        );

        return self::SUCCESS;
    }
}
