<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashRegisterLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_register_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable();
            $table->foreignId('cash_register_id');
            $table->double('opening_amount')->nullable();
            $table->double('closing_amount')->nullable();
            $table->boolean('status')->nullable();
            $table->dateTime('opening_time')->nullable();
            $table->dateTime('closing_time')->nullable();
            $table->foreignId('opened_by_id')->nullable();
            $table->foreignId('closed_by_id')->nullable();
            $table->text('note')->nullable();

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_register_logs');
    }
}
