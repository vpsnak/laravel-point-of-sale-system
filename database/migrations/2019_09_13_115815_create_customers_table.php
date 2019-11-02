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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('magento_id')->default(0);
            $table->string('email')->unique()->index();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->boolean('house_account_status')->default(0);
            $table->string('house_account_number', 100)->nullable();
            $table->decimal('house_account_limit', 12,4)->nullable();
            $table->boolean('no_tax')->default(0);
            $table->string('no_tax_file', 100)->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('customers');
    }
}
