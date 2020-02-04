<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('issued_by');
            $table->json('content');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('cash_register_id');
            $table->integer('print_count');
            $table->integer('email_count');
            $table->timestamps();

            $table->foreign('issued_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
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
        Schema::dropIfExists('receipts');
    }
}
