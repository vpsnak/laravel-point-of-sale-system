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
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::table('product_store', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        Schema::table('address_customer', function (Blueprint $table) {
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
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

        Schema::table('category_product', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::table('address_customer', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropForeign(['customer_id']);
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
    }
}
