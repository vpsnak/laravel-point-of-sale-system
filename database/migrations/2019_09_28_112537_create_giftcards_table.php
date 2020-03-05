<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftcardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('giftcards', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->string('name');
            $table->string('code');
            $table->timestampTz('enabled')->nullable();
            $table->json('price');

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
        Schema::dropIfExists('giftcards');
    }
}
