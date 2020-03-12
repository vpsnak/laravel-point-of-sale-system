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
            $table->mediumIncrements('id');

            $table->enum('type', ['payment', 'refund']);
            $table->unsignedTinyInteger('payment_type_id')->nullable();
            $table->unsignedTinyInteger('refund_type_id')->nullable();
            $table->json('price');
            $table->json('change_price')->nullable();
            $table->unsignedMediumInteger('order_id');
            $table->unsignedSmallInteger('cash_register_id');
            $table->string('code')->nullable();
            $table->string('status');

            $table->unsignedSmallInteger('user_id');
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
