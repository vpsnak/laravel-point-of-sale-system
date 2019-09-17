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
        // @TODO order status
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->enum('discount_type', ['flat', 'percent'])->nullable();
            $table->smallInteger('discount')->unsigned()->default(0);
            $table->string('shipping_type');
            $table->tinyInteger('shipping_cost')->unsigned()->default(0);
            $table->decimal('tax', 4)->unsigned();
            $table->decimal('subtotal');
            $table->string('note')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
