<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('phone');
            $table->string('street');
            $table->string('postcode');
            $table->string('city');
            $table->boolean('active');
            $table->boolean('is_phone_center');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('tax_id')->default(0);
            $table->unsignedBigInteger('user_id');

            $table->timestamps();

            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the mig rations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
