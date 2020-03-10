<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElavonSdkPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elavon_sdk_payments', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedMediumInteger('payment_id')->nullable(); // @TODO: remove nullable

            $table->string('test_case')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_gateway_id')->nullable();
            $table->string('chan_id')->nullable();

            $table->string('status');
            $table->json('log');

            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');

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
        Schema::dropIfExists('elavon_sdk_payments');
    }
}
