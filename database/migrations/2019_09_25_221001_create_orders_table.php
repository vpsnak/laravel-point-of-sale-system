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
            $table->id();

            $table->foreignId('store_id');
            $table->enum('method', ['retail', 'pickup', 'delivery']);
            $table->json('items');
            $table->text('notes')->nullable();

            $table->foreignId('customer_id')->nullable();
            $table->json('billing_address')->nullable();
            $table->json('delivery')->nullable();

            $table->unsignedDecimal('tax_percentage', 6, 4);

            $table->string('currency', 3);
            $table->json('discount')->nullable();
            $table->json('delivery_fees_price')->nullable();

            $table->foreignId('created_by_id');
            $table->foreignId('mas_order_id')->nullable();

            $table->foreignId('magento_id')->nullable();
            $table->foreignId('magento_shipping_address_id')->nullable();
            $table->foreignId('magento_billing_address_id')->nullable();

            $table->timestampsTz();
        });
        DB::statement("ALTER TABLE orders AUTO_INCREMENT = 10000;");
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
