<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')->nullable();
            $table->foreignId('store_pickup_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('street');
            $table->string('street2')->nullable();
            $table->string('city');
            $table->foreignId('region_id');
            $table->string('postcode', 10);
            $table->string('phone', 20)->index();
            $table->string('company')->nullable();
            $table->string('vat_id', 20)->nullable();
            $table->boolean('is_default_billing')->nullable();
            $table->boolean('is_default_shipping')->nullable();
            $table->foreignId('location')->nullable();
            $table->string('location_name')->nullable();
            $table->foreignId('magento_id')->nullable();

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
        Schema::dropIfExists('addresses');
    }
}
