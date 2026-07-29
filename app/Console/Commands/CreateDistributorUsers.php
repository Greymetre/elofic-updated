<?php

namespace App\Console\Commands;

use App\Models\MasterDistributor;
use App\Models\User;
use App\Models\UserCityAssign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class CreateDistributorUsers extends Command
{
    protected $signature = 'distributors:create-users
        {--role=Distributor : Role name assigned to created distributor users}
        {--dry-run : Show what would happen without saving changes}';

    protected $description = 'Create login users for master distributors and assign the Distributor role';

    public function handle()
    {
        $roleName = (string) $this->option('role');
        $dryRun = (bool) $this->option('dry-run');

        if (! Schema::hasColumn('users', 'customerid')) {
            $this->error('users.customerid column was not found.');

            return Command::FAILURE;
        }

        $role = Role::where('name', $roleName)
            ->where('guard_name', 'users')
            ->first();

        if (! $role) {
            $this->error("Role '{$roleName}' with guard 'users' was not found.");

            return Command::FAILURE;
        }

        $stats = [
            'created' => 0,
            'updated' => 0,
            'deleted' => 0,
            'skipped' => 0,
        ];

        MasterDistributor::query()
            ->select(['id', 'legal_name', 'trade_name', 'contact_person', 'mobile', 'email', 'business_status'])
            ->orderBy('id')
            ->chunkById(200, function ($distributors) use ($role, $dryRun, &$stats) {
                foreach ($distributors as $distributor) {
                    $email = $this->normalizeEmail($distributor->email);
                    $mobile = $this->normalizeMobile($distributor->mobile);

                    if ($email === '' || $mobile === '') {
                        $stats['skipped']++;
                        $this->warn("Skipped distributor #{$distributor->id}: email or mobile is missing.");
                        continue;
                    }

                    if (strlen($mobile) > 11) {
                        $stats['skipped']++;
                        $this->warn("Skipped distributor #{$distributor->id}: mobile {$mobile} is longer than the users.mobile column.");
                        continue;
                    }

                    // customerid is the distributor link. Do not require the role here:
                    // this command is also responsible for repairing a missing role.
                    $userByCustomer = User::where('customerid', $distributor->id)->first();

                    $userByEmail = User::where('email', $email)->first();

                    if ($userByCustomer && $userByEmail && $userByCustomer->id !== $userByEmail->id) {
                        $stats['skipped']++;
                        $this->warn("Skipped distributor #{$distributor->id}: email {$email} belongs to user #{$userByEmail->id}, but customerid is already linked to user #{$userByCustomer->id}.");
                        continue;
                    }

                    $user = $userByCustomer ?: $userByEmail;

                    if ($user && filled($user->customerid) && (int) $user->customerid !== (int) $distributor->id) {
                        $stats['skipped']++;
                        $this->warn("Skipped distributor #{$distributor->id}: email {$email} already belongs to user #{$user->id} with customerid {$user->customerid}.");
                        continue;
                    }

                    $mobileOwner = User::where('mobile', $mobile)
                        ->when($user, function ($query) use ($user) {
                            $query->where('id', '!=', $user->id);
                        })
                        ->first();

                    if ($mobileOwner) {
                        $stats['skipped']++;
                        $this->warn("Skipped distributor #{$distributor->id}: mobile {$mobile} already belongs to user #{$mobileOwner->id}.");
                        continue;
                    }

                    if ($dryRun) {
                        $stats[$user ? 'updated' : 'created']++;
                        $this->line(($user ? 'Would update' : 'Would create') . " user for distributor #{$distributor->id} ({$email}).");
                        continue;
                    }

                    DB::transaction(function () use ($distributor, $email, $mobile, $role, $user, &$stats) {
                        if ($user) {
                            // Never reset an existing user's password during a backfill.
                            $user->fill($this->userData($distributor, $email, $mobile, false));
                            $user->save();
                            $stats['updated']++;
                        } else {
                            $user = User::create($this->userData($distributor, $email, $mobile, true));
                            $stats['created']++;
                        }

                        $user->assignRole($role);

                        $permissions = $role->permissions;
                        if ($permissions->isNotEmpty()) {
                            $user->givePermissionTo($permissions);
                        }
                    });
                }
            });

        User::query()
            ->whereNotNull('customerid')
            ->whereHas('roles', function ($roleQuery) use ($role) {
                $roleQuery->where('roles.id', $role->id);
            })
            ->whereNotIn('customerid', MasterDistributor::query()->select('id'))
            ->orderBy('id')
            ->chunkById(200, function ($users) use ($dryRun, &$stats) {
                foreach ($users as $user) {
                    if ($dryRun) {
                        $stats['deleted']++;
                        $this->line("Would delete orphan distributor user #{$user->id} ({$user->email}); distributor #{$user->customerid} does not exist.");
                        continue;
                    }

                    try {
                        DB::transaction(function () use ($user) {
                            $user->tokens()->delete();
                            UserCityAssign::where('userid', $user->id)->delete();
                            $user->syncPermissions([]);
                            $user->syncRoles([]);
                            $user->delete();
                        });

                        $stats['deleted']++;
                    } catch (\Throwable $exception) {
                        $stats['skipped']++;
                        $this->error("Could not delete orphan distributor user #{$user->id}: {$exception->getMessage()}");
                    }
                }
            });

        $mode = $dryRun ? 'Dry run complete' : 'Distributor user creation complete';
        $this->info("{$mode}. Created: {$stats['created']}, Updated: {$stats['updated']}, Deleted: {$stats['deleted']}, Skipped: {$stats['skipped']}.");

        return Command::SUCCESS;
    }

    private function userData(
        MasterDistributor $distributor,
        string $email,
        string $mobile,
        bool $includePassword
    ): array
    {
        $name = trim((string) ($distributor->contact_person ?: $distributor->trade_name ?: $distributor->legal_name));
        $name = $name !== '' ? $name : 'Distributor ' . $distributor->id;

        $nameParts = preg_split('/\s+/', $name, 2);

        $data = [
            'active' => strcasecmp((string) $distributor->business_status, 'Active') === 0 ? 'Y' : 'N',
            'name' => $name,
            'first_name' => $nameParts[0] ?? $name,
            'last_name' => $nameParts[1] ?? '',
            'mobile' => $mobile,
            'email' => $email,
            'customerid' => $distributor->id,
        ];

        if ($includePassword) {
            $data['password'] = Hash::make($mobile);
        }

        if ($includePassword && Schema::hasColumn('users', 'password_string')) {
            $data['password_string'] = $mobile;
        }

        return $data;
    }

    private function normalizeEmail(?string $email): string
    {
        return strtolower(trim((string) $email));
    }

    private function normalizeMobile(?string $mobile): string
    {
        return preg_replace('/\D+/', '', (string) $mobile) ?? '';
    }
}
