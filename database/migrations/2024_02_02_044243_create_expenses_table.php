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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('expenses_type');
            $table->string('date');
            $table->float('claim_amount');
            $table->string('start_km')->nullable();
            $table->string('stop_km')->nullable();
            $table->string('total_km')->nullable();
            $table->string('note')->nullable();
            $table->string('attechment')->nullable();
            $table->tinyInteger('checker_status')->default('0');
            $table->tinyInteger('accountant_status')->default('0');
            $table->bigInteger('created_by');
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
        Schema::dropIfExists('expenses');
    }
};
