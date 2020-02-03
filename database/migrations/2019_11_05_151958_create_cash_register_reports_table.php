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
            $table->bigIncrements('id');
            $table->string('report_name');
            $table->string('report_type');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('cash_register_id');
            $table->decimal('opening_amount', 12, 2)->default(0);
            $table->decimal('closing_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('cash_total', 12, 2)->default(0);
            $table->decimal('gift_card_total', 12, 2)->default(0);
            $table->decimal('credit_card_total', 12, 2)->default(0);
            $table->decimal('pos_terminal_total', 12, 2)->default(0);
            $table->decimal('change_total', 12, 2)->default(0);
            $table->decimal('cash_tax', 12, 2)->default(0);
            $table->decimal('gift_card_tax', 12, 2)->default(0);
            $table->decimal('credit_card_tax', 12, 2)->default(0);
            $table->decimal('pos_terminal_tax', 12, 2)->default(0);
            $table->integer('order_count')->default(0);
            $table->integer('order_product_count')->default(0);
            $table->integer('order_refund_count')->default(0);
            $table->decimal('order_refund_total', 12, 2)->default(0);
            $table->integer('order_complete_count')->default(0);
            $table->decimal('order_complete_total', 12, 2)->default(0);
            $table->integer('order_retail_count')->default(0);
            $table->integer('order_in_store_count')->default(0);
            $table->integer('order_delivery_count')->default(0);

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('cash_register_reports');
    }
}
