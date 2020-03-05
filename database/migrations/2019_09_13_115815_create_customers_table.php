<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');

            $table->string('email')->unique()->index();
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->boolean('house_account_status')->nullable()->default(false);
            $table->string('house_account_number')->nullable();
            $table->unsignedInteger('house_account_limit')->nullable();
            $table->boolean('no_tax')->default(false)->nullable();
            $table->string('no_tax_file')->nullable();
            $table->text('comment')->nullable();
            $table->string('phone')->nullable();

            $table->unsignedInteger('magento_id')->nullable();

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
        Schema::dropIfExists('customers');
    }
}
