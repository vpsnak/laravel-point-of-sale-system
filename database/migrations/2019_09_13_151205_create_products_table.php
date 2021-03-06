<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('sku')->unique()->index();
            $table->string('name')->index();
            $table->string('barcode')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('url')->nullable();
            $table->string('plantcare_pdf')->nullable();
            $table->boolean('is_price_editable')->nullable()->default(true);
            $table->boolean('is_discountable')->nullable()->default(true);
            $table->json('price');
            $table->json('discount')->nullable();

            $table->text('description')->nullable();

            $table->foreignId('magento_id')->nullable();
            $table->foreignId('stock_id')->nullable();

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
        Schema::dropIfExists('products');
    }
}
