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
            $table->string('name')->index();
            $table->string('barcode')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('url')->nullable();
            $table->string('plantcare_pdf')->nullable();
            $table->boolean('editable_price')->nullable()->default(true);

            $table->text('description')->nullable();

            $table->timestamps();
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
