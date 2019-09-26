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
            $table->tinyInteger('taxable');
            $table->tinyInteger('is_default')->default(0);
            $table->unsignedBigInteger('tax_id')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
