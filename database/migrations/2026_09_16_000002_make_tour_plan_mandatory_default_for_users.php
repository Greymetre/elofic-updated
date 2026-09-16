<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('users')->update(['tour_plan_mandatory' => true]);
        DB::statement('ALTER TABLE users MODIFY tour_plan_mandatory TINYINT(1) NOT NULL DEFAULT 1');
    }

    public function down()
    {
        DB::statement('ALTER TABLE users MODIFY tour_plan_mandatory TINYINT(1) NOT NULL DEFAULT 0');
    }
};
