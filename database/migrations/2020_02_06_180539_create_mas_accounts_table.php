<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mas_accounts', function (Blueprint $table) {
            $table->id();

            $table->enum('environment', ['test', 'production'])->unique();
            $table->string('endpoint');
            $table->string('direct_id');
            $table->string('fulfiller_md_number');
            $table->string('username');
            $table->string('password');
            $table->boolean('active');
            $table->json('default_recipient_account')->nullable();
            $table->json('default_online_partner_account')->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('giftcard_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mas_accounts');
    }
}
