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
            $table->mediumIncrements('id');

            $table->unsignedTinyInteger('store_id');
            $table->enum('method', ['retail', 'pickup', 'delivery']);
            $table->json('items');
            $table->text('notes')->nullable();

            $table->unsignedMediumInteger('customer_id')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('delivery')->nullable();

            $table->unsignedDecimal('tax_percentage', 6, 4);

            $table->string('currency', 3);
            $table->json('discount')->nullable();
            $table->unsignedMediumInteger('delivery_fees_amount')->nullable()->default(0);

            $table->unsignedSmallInteger('user_id');
            $table->unsignedMediumInteger('mas_order_id')->nullable();

            $table->unsignedMediumInteger('magento_id')->nullable();
            $table->unsignedMediumInteger('magento_shipping_address_id')->nullable();
            $table->unsignedMediumInteger('magento_billing_address_id')->nullable();

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
