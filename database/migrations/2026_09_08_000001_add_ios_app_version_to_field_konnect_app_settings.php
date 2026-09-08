<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_konnect_app_settings', function (Blueprint $table) {
            $table->string('ios_app_version')->nullable()->after('app_version');
        });
    }

    public function down(): void
    {
        Schema::table('field_konnect_app_settings', function (Blueprint $table) {
            $table->dropColumn('ios_app_version');
        });
    }
};
