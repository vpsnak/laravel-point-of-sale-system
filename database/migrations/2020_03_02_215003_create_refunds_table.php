<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->unsignedMediumInteger('payment_id')->nullable();
            $table->unsignedMediumInteger('order_id')->nullable();
            $table->json('price');
            $table->unsignedSmallInteger('user_id')->nullable();
            $table->unsignedTinyInteger('refund_type_id')->nullable();

            $table->unsignedSmallInteger('cash_register_id')->nullable();

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
        Schema::dropIfExists('refunds');
    }
}
