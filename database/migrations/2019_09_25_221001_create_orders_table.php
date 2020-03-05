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
            $table->increments('id');

            $table->unsignedTinyInteger('store_id');
            $table->enum('method', ['retail', 'pickup', 'delivery']);
            $table->json('items');
            $table->text('notes')->nullable();

            $table->unsignedInteger('customer_id')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('delivery')->nullable();

            $table->json('discount')->nullable();
            $table->json('delivery_fees_price')->nullable();

            $table->unsignedSmallInteger('user_id');
            $table->unsignedInteger('mas_order_id')->nullable();

            $table->unsignedInteger('magento_id')->nullable();
            $table->unsignedInteger('magento_shipping_address_id')->nullable();
            $table->unsignedInteger('magento_billing_address_id')->nullable();

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
