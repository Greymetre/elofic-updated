<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'launch_date')) {
                // Product creation date from the catalogue API (entry_date); drives the automatic NPD tag
                $table->dateTime('launch_date')->nullable()->index();
            }
            if (!Schema::hasColumn('products', 'npd_excluded')) {
                // 1 = manually removed from NPD even though launched within the last year
                $table->boolean('npd_excluded')->default(false);
            }
            if (!Schema::hasColumn('products', 'npd_updated_by')) {
                $table->unsignedBigInteger('npd_updated_by')->nullable();
            }
            if (!Schema::hasColumn('products', 'npd_updated_at')) {
                $table->dateTime('npd_updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['launch_date']);
            $table->dropColumn(['launch_date', 'npd_excluded', 'npd_updated_by', 'npd_updated_at']);
        });
    }
};
