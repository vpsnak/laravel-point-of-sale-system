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

            $table->enum('discount_type', ['none', 'flat', 'percentage'])->nullable()->default('none');
            $table->decimal('discount_amount', 10, 2)->nullable()->default(0);
            $table->decimal('tax')->unsigned();
            $table->decimal('shipping_cost', 10, 2)->nullable()->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('change', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('items');
            $table->json('billing_address')->nullable();
            $table->json('delivery')->nullable();
            $table->enum('method', ['retail', 'pickup', 'delivery']);

            $table->unsignedBigInteger('mas_order_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('magento_id')->nullable();
            $table->unsignedBigInteger('magento_shipping_address_id')->nullable();
            $table->unsignedBigInteger('magento_billing_address_id')->nullable();

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
        Schema::dropIfExists('orders');
    }
}
