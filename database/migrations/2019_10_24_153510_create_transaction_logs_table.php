<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('cash_register_id');
            $table->text('log');

            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('cascade');

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
        Schema::dropIfExists('transaction_logs');
    }
}
