<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_product', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });

        Schema::table('product_store', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('area_code_id')->references('id')->on('area_codes')->onDelete('restrict');
        });

        Schema::table('address_customer', function (Blueprint $table) {
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('restrict');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
        });

        Schema::table('order_product', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
        });

        Schema::table('order_payment', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('restrict');
        });

        Schema::table('cart_product', function (Blueprint $table) {
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('restrict');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });

        Schema::table('shippings', function (Blueprint $table) {
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('restrict');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('restrict');
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_store', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['store_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['price_id']);
        });


        Schema::table('category_product', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['area_code_id']);
        });
        Schema::table('address_customer', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropForeign(['customer_id']);
        });
        Schema::table('order_product', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        Schema::table('order_payment', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['payment_id']);
        });

        Schema::table('cart_product', function (Blueprint $table) {
            $table->dropForeign(['cart_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::table('shippings', function (Blueprint $table) {
            $table->dropForeign(['price_id']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
        });
    }
}
