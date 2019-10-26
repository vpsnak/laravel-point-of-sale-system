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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('magento_id')->default(0);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('street', 100);
            $table->string('street2', 100)->nullable();
            $table->string('city', 100);
            $table->string('country_id', 100);
            $table->string('region', 100);
            $table->string('postcode', 100);
            $table->string('phone', 100);
            $table->string('company', 100)->nullable();
            $table->string('vat_id', 100)->nullable();
            $table->string('deliverydate', 100)->nullable();
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
        Schema::dropIfExists('addresses');
    }
}
