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
        Schema::table('order_status', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('countries');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions');
        });

        Schema::table('menu_item_role', function (Blueprint $table) {
            $table->foreign('menu_item_id')->references('id')->on('menu_items');
            $table->foreign('role_id')->references('id')->on('roles');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::table('product_store', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
            $table->foreign('mas_order_id')->references('id')->on('mas_orders')->onDelete('restrict');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('mas_orders', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
        });

        Schema::table('mas_order_logs', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_status', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['status_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
        });

        Schema::table('menu_item_role', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
            $table->dropForeign(['role_id']);
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        Schema::table('product_store', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['store_id']);
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['store_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('mas_orders', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        Schema::table('mas_order_logs', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
    }
}
