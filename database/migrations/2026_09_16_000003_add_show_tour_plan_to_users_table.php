<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'show_tour_plan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('show_tour_plan')->default(true)->after('show_attandance_report');
            });
        }

        DB::table('users')->whereNull('show_tour_plan')->update(['show_tour_plan' => 1]);
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'show_tour_plan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('show_tour_plan');
            });
        }
    }
};
