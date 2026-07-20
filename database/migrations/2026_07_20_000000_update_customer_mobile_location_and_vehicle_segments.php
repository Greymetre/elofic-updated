<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_distributors', function (Blueprint $table) {
            if (!Schema::hasColumn('master_distributors', 'gps_location')) {
                $table->string('gps_location')->nullable()->after('secondary_email');
            }
            if (!Schema::hasColumn('master_distributors', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('gps_location');
            }
            if (!Schema::hasColumn('master_distributors', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('master_distributors', 'is_current_location')) {
                $table->boolean('is_current_location')->default(true)->after('longitude');
            }
        });

        Schema::table('secondary_customers', function (Blueprint $table) {
            if (!Schema::hasColumn('secondary_customers', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('gps_location');
            }
            if (!Schema::hasColumn('secondary_customers', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('secondary_customers', 'is_current_location')) {
                $table->boolean('is_current_location')->default(true)->after('longitude');
            }
        });

        DB::table('secondary_customers')
            ->select('id', 'vehicle_segment')
            ->whereNotNull('vehicle_segment')
            ->orderBy('id')
            ->chunkById(500, function ($customers) {
                foreach ($customers as $customer) {
                    $value = trim((string) $customer->vehicle_segment);
                    $decoded = json_decode($value, true);
                    $segments = is_array($decoded) ? $decoded : explode(',', $value);
                    $segments = array_values(array_unique(array_filter(array_map(
                        static fn ($segment) => trim((string) $segment),
                        $segments
                    ), static fn ($segment) => $segment !== '')));

                    DB::table('secondary_customers')
                        ->where('id', $customer->id)
                        ->update(['vehicle_segment' => json_encode($segments)]);
                }
            });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE secondary_customers MODIFY vehicle_segment JSON NULL');
            DB::statement("ALTER TABLE secondary_customers MODIFY opportunity_status ENUM('HOT','WARM','COLD','LOST','EXISTING') NOT NULL DEFAULT 'COLD'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('secondary_customers')
                ->where('opportunity_status', 'EXISTING')
                ->update(['opportunity_status' => 'COLD']);
            DB::statement('ALTER TABLE secondary_customers MODIFY vehicle_segment VARCHAR(255) NULL');
            DB::statement("ALTER TABLE secondary_customers MODIFY opportunity_status ENUM('HOT','WARM','COLD','LOST') NOT NULL DEFAULT 'COLD'");
        }

        Schema::table('secondary_customers', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['latitude', 'longitude', 'is_current_location'],
                static fn ($column) => Schema::hasColumn('secondary_customers', $column)
            ));
            if ($columns) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('master_distributors', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['latitude', 'longitude', 'is_current_location'],
                static fn ($column) => Schema::hasColumn('master_distributors', $column)
            ));
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
