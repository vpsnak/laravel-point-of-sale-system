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

            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('status')->nullable();
            $table->enum('discount_type', ['flat', 'percentage'])->nullable();
            $table->unsignedSmallInteger('discount_amount')->default(0)->nullable();
            $table->decimal('tax')->unsigned();
            $table->decimal('subtotal');
            $table->decimal('change')->default(0);
            $table->string('notes')->nullable();
            $table->string('shipping_type')->nullable();
            $table->decimal('shipping_cost')->default(0);
            $table->string('store_pickup_id')->nullable();
            $table->string('delivery_date')->nullable();
            $table->string('delivery_slot')->nullable();
            $table->string('occasion')->nullable();

            $table->json('items');
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();

            $table->unsignedBigInteger('magento_shipping_address_id')->nullable();
            $table->unsignedBigInteger('magento_billing_address_id')->nullable();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict');
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
        Schema::dropIfExists('orders');
    }
}
