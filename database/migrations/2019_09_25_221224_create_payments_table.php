<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedTinyInteger('payment_type_id');
            $table->json('price');
            $table->unsignedBigInteger('order_id');
            $table->unsignedSmallInteger('cash_register_id');
            $table->string('code')->nullable();
            $table->string('status');

            $table->unsignedSmallInteger('user_id');
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
        Schema::dropIfExists('payments');
    }
}
