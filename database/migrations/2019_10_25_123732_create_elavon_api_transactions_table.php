<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElavonApiTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elavon_api_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id');
            $table->string('txn_id')->nullable();
            $table->string('transaction')->nullable();
            $table->string('card_number')->nullable();
            $table->string('card_holder')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('elavon_api_transactions');
    }
}
