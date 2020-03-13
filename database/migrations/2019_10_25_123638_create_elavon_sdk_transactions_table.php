<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElavonSdkTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elavon_sdk_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id');
            $table->string('payment_gateway_id')->nullable();
            $table->string('chan_id')->nullable();
            $table->string('status');
            $table->json('log');

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
        Schema::dropIfExists('elavon_sdk_transactions');
    }
}
