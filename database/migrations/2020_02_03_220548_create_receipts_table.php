<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedSmallInteger('issued_by');
            $table->json('content');
            $table->unsignedBigInteger('order_id');
            $table->unsignedSmallInteger('cash_register_id');
            $table->unsignedTinyInteger('print_count');
            $table->unsignedTinyInteger('email_count');
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
        Schema::dropIfExists('receipts');
    }
}
