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
            $table->id();

            $table->string('name');
            $table->string('street');
            $table->string('street2')->nullable();
            $table->string('city');
            $table->foreignId('region_id');
            $table->string('postcode', 10);
            $table->string('phone', 20)->index();
            $table->string('company')->nullable();
            $table->foreignId('location_id')->nullable();

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
        Schema::dropIfExists('store_pickups');
    }
}
