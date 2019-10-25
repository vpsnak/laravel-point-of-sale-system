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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('magento_id')->default(0);
            $table->unsignedBigInteger('stock_id')->default(0);
            $table->string('sku')->unique()->index();
            $table->string('name');
            $table->string('barcode')->nullable();
            $table->string('photo_url');
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
            // stock_id price_id foreign
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
