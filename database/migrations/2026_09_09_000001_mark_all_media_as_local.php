<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('media')) {
            return;
        }

        DB::table('media')->where('disk', 's3')->update(['disk' => 'public']);
        DB::table('media')->where('conversions_disk', 's3')->update(['conversions_disk' => 'public']);
    }

    public function down(): void
    {
        // Files remain local; restoring S3 references would make media unavailable.
    }
};
