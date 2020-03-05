<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status', function (Blueprint $table) {
            $table->mediumIncrements('id');

            $table->unsignedMediumInteger('order_id');
            $table->unsignedTinyInteger('status_id');
            $table->unsignedSmallInteger('user_id')->nullable();
            $table->text('notes')->nullable();
            $table->json('attributes')->nullable();

            $table->timestampTz('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_statuses');
    }
}
