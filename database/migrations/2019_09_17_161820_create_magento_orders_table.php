<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagentoOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magento_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('entity_id')->unsigned();
            $table->string('state', 32)->nullable();
            $table->string('status', 32)->nullable();
            $table->integer('customer_id')->unsigned();
            $table->decimal('discount_amount')->nullable();
            $table->decimal('grand_total')->nullable();
            $table->decimal('shipping_amount')->nullable();
            $table->decimal('subtotal')->nullable();
            $table->decimal('tax_amount')->nullable();
            $table->integer('shipping_address_id')->nullable();
            $table->string('increment_id', 50)->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_firstname')->nullable();
            $table->string('customer_lastname')->nullable();
            $table->string('customer_middlename')->nullable();
            $table->string('customer_taxvat')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('store_name')->nullable();
            $table->text('customer_note')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magento_orders');
    }
}
