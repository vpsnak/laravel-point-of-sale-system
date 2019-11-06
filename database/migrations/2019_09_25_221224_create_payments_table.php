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
            $table->decimal('amount')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('cash_register_id')->nullable();
            $table->string('code')->nullable();
            $table->string('status');
            $table->boolean('refunded')->default(0);

            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('payment_type')->references('id')->on('payment_types')->onDelete('restrict');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
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
