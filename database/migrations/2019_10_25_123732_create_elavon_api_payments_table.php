<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElavonApiPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elavon_api_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payment_id');

            $table->string('txn_id')->nullable();
            $table->string('transaction')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_holder')->nullable();
            $table->string('status')->nullable();
            $table->text('log');

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
        Schema::dropIfExists('elavon_api_payments');
    }
}
