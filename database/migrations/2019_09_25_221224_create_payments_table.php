<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_type');
            $table->decimal('amount');
            $table->integer('order_id');
            $table->unsignedBigInteger('cash_register_id')->nullable();
            $table->timestamps();
            $table->foreign('payment_type')->references('id')->on('payment_types')->onDelete('cascade');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
