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
            $table->tinyIncrements('id');

            $table->string('name');
            $table->string('phone');
            $table->string('street');
            $table->string('postcode');
            $table->string('city');
            $table->boolean('active');
            $table->boolean('is_phone_center');
            $table->unsignedTinyInteger('company_id');
            $table->unsignedTinyInteger('tax_id');
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
