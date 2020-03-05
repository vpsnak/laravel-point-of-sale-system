<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mas_order_logs', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedMediumInteger('order_id');

            $table->json('payload');
            $table->string('response');
            $table->string('status');
            $table->string('environment');

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
        Schema::dropIfExists('mas_order_logs');
    }
}
