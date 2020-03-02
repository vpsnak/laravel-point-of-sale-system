<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->json('mdse_price');
            $table->json('delivery_fees_price')->nullable();
            $table->json('total_tax_price');
            $table->json('discount')->nullable();

            $table->text('notes')->nullable();
            $table->json('items');
            $table->json('billing_address')->nullable();
            $table->json('delivery')->nullable();
            $table->enum('method', ['retail', 'pickup', 'delivery']);

            $table->unsignedBigInteger('mas_order_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedTinyInteger('store_id');
            $table->unsignedSmallInteger('user_id');
            $table->unsignedBigInteger('magento_id')->nullable();
            $table->unsignedBigInteger('magento_shipping_address_id')->nullable();
            $table->unsignedBigInteger('magento_billing_address_id')->nullable();

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
        Schema::dropIfExists('orders');
    }
}
