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

            $table->enum('type', ['payment', 'refund']);
            $table->foreignId('payment_type_id')->nullable();
            $table->foreignId('refund_type_id')->nullable();
            $table->json('price');
            $table->json('change_price')->nullable();
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
