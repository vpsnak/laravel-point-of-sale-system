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
            $table->dropForeign(['customer_id'])->references('id')->on('customers')->onDelete('restrict');
            $table->dropForeign(['store_id'])->references('id')->on('stores')->onDelete('restrict');
            $table->dropForeign(['created_by'])->references('id')->on('users')->onDelete('restrict');
        });
    }
}
