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
        Schema::table('settings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('cash_register_reports', function (Blueprint $table) {
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('cascade');
        });

        Schema::table('elavon_sdk_transactions', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });

        Schema::table('elavon_api_transactions', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('cash_register_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('cascade');
            $table->foreign('opened_by_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('closed_by_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('restrict');
            $table->foreign('refund_id')->references('id')->on('refunds')->onDelete('restrict');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('restrict');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('restrict');
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('restrict');
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('restrict');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('restrict');
        });

        Schema::table('cash_registers', function (Blueprint $table) {
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('restrict');
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::table('taxes', function (Blueprint $table) {
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('cascade');
        });

        Schema::table('order_status', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('processed_by_id')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('countries');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions');
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        Schema::table('menu_item_role', function (Blueprint $table) {
            $table->foreign('menu_item_id')->references('id')->on('menu_items');
            $table->foreign('role_id')->references('id')->on('roles');
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::table('product_store', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
            $table->foreign('mas_order_id')->references('id')->on('mas_orders')->onDelete('restrict');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('restrict');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('restrict');
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
        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['created_by_id']);
        });

        Schema::table('cash_register_reports', function (Blueprint $table) {
            $table->dropForeign(['created_by_id']);
            $table->dropForeign(['cash_register_id']);
        });

        Schema::table('elavon_sdk_transactions', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
        });

        Schema::table('elavon_api_transactions', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['created_by_id']);
        });

        Schema::table('cash_register_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['cash_register_id']);
            $table->dropForeign(['opened_by_id']);
            $table->dropForeign(['closed_by_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['cash_register_id']);
            $table->dropForeign(['created_by_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['payment_type_id']);
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['payment_id']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['tax_id']);
            $table->dropForeign(['company_id']);
        });

        Schema::table('taxes', function (Blueprint $table) {
            $table->dropForeign(['created_by_id']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['cash_register_id']);
        });

        Schema::table('order_status', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['status_id']);
            $table->dropForeign(['processed_by_id']);
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropForeign(['customer_id']);
        });

        Schema::table('menu_item_role', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
            $table->dropForeign(['role_id']);
        });

        Schema::table('product_store', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['store_id']);
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['store_id']);
            $table->dropForeign(['created_by_id']);
            $table->dropForeign(['mas_order_id']);
        });

        Schema::table('mas_orders', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        Schema::table('mas_order_logs', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
    }
}
