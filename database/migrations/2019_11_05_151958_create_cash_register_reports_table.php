<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashRegisterReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_register_reports', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('report_name');
            $table->string('report_type');
            $table->unsignedSmallInteger('user_id');
            $table->unsignedSmallInteger('cash_register_id');
            $table->unsignedMediumInteger('opening_amount')->default(0);
            $table->unsignedMediumInteger('closing_amount')->default(0);
            $table->unsignedMediumInteger('subtotal')->default(0);
            $table->unsignedMediumInteger('tax')->default(0);
            $table->unsignedMediumInteger('total')->default(0);
            $table->unsignedMediumInteger('cash_total')->default(0);
            $table->unsignedMediumInteger('gift_card_total')->default(0);
            $table->unsignedMediumInteger('credit_card_total')->default(0);
            $table->unsignedMediumInteger('pos_terminal_total')->default(0);
            $table->unsignedMediumInteger('change_total')->default(0);
            $table->unsignedMediumInteger('cash_tax')->default(0);
            $table->unsignedMediumInteger('gift_card_tax')->default(0);
            $table->unsignedMediumInteger('credit_card_tax')->default(0);
            $table->unsignedMediumInteger('pos_terminal_tax')->default(0);
            $table->integer('order_count')->default(0);
            $table->integer('order_product_count')->default(0);
            $table->integer('order_refund_count')->default(0);
            $table->unsignedMediumInteger('order_refund_total')->default(0);
            $table->integer('order_complete_count')->default(0);
            $table->unsignedMediumInteger('order_complete_total')->default(0);
            $table->integer('order_retail_count')->default(0);
            $table->integer('order_in_store_count')->default(0);
            $table->integer('order_delivery_count')->default(0);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('cascade');

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
        Schema::dropIfExists('cash_register_reports');
    }
}
