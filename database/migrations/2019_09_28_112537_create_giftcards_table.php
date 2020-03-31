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
            $table->id();

            $table->string('name');
            $table->string('code');
            $table->timestampTz('enabled_at')->nullable();
            $table->json('price');
            $table->json('original_price');
            $table->unsignedSmallInteger('times_used')->default(0)->nullable();
            $table->foreignId('created_by_id');

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
