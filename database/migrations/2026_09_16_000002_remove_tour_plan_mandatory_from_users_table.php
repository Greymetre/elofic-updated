<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('users', 'tour_plan_mandatory')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('tour_plan_mandatory');
            });
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('tour_plan_mandatory')->default(false)->after('show_attandance_report');
        });
    }
};
