<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->json('price');
            $table->foreignId('payment_id')->nullable();
            $table->foreignId('refund_id')->nullable();
            $table->foreignId('order_id');
            $table->foreignId('cash_register_id');
            $table->string('code')->nullable();
            $table->string('status');
            $table->foreignId('created_by_id');

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
        Schema::dropIfExists('transactions');
    }
}
