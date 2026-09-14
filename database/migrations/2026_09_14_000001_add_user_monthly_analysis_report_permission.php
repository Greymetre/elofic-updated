<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::firstOrCreate([
            'name' => 'user_monthly_analysis_report',
            'guard_name' => 'users',
        ]);

        $superadmin = Role::query()
            ->where('name', 'superadmin')
            ->where('guard_name', 'users')
            ->first();

        if ($superadmin) {
            $superadmin->givePermissionTo($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::query()
            ->where('name', 'user_monthly_analysis_report')
            ->where('guard_name', 'users')
            ->first();

        if ($permission) {
            Role::query()
                ->where('name', 'superadmin')
                ->where('guard_name', 'users')
                ->first()?->revokePermissionTo($permission);
            $permission->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
