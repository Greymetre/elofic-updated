<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // A sale can now involve records from master_distributors and
            // secondary_customers. The legacy customer foreign keys cannot
            // represent that polymorphic relationship.
            $table->dropForeign(['buyer_id']);
            $table->dropForeign(['seller_id']);

            $table->string('buyer_type', 32)->nullable()->after('buyer_id');
            $table->string('seller_type', 32)->nullable()->after('seller_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['buyer_type', 'seller_type']);

            // This rollback is only safe after any new-model sales have been
            // migrated back to the legacy customers table.
            $table->foreign('buyer_id')->references('id')->on('customers');
            $table->foreign('seller_id')->references('id')->on('customers');
        });
    }
};
