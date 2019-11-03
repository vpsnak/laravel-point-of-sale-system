<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStorePickupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_pickups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('street');
            $table->string('street1')->nullable();
            $table->unsignedInteger('region_id');
            $table->string('country_id', 2);
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
        Schema::dropIfExists('store_pickups');
    }
}
