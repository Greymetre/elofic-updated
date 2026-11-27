<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Map every product to the "All" segment (subcategory). The app's Product Catalogue
     * lists products per segment, and products synced without a segment were not visible there.
     */
    public function up(): void
    {
        $allSegmentId = DB::table('subcategories')
            ->where('subcategory_name', 'All')
            ->where('active', 'Y')
            ->value('id');

        if (!$allSegmentId) {
            return;
        }

        DB::table('products')->update(['subcategory_id' => $allSegmentId]);
    }

    public function down(): void
    {
        // Previous segment mapping is not recoverable; nothing to roll back.
    }
};
