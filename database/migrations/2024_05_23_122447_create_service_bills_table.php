<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no');
            $table->string('complaint_id');
            $table->string('complaint_no');
            $table->string('division');
            $table->string('category');
            $table->string('complaint_type');
            $table->string('complaint_reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_bills');
    }
};
