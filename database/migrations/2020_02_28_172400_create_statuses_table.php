<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('value');
            $table->string('color')->nullable(); // @TODO Remove nullable
            $table->string('icon')->nullable();  // @TODO Remove nullable

            $table->boolean('can_checkout');
            $table->boolean('can_edit_order_options');
            $table->boolean('can_edit_order_items');
            $table->boolean('can_receipt');
            $table->boolean('can_mas_upload');
            $table->boolean('can_mas_reupload');
            $table->boolean('can_refund');
            $table->boolean('can_cancel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statuses');
    }
}
