<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_types')->insert([
            ['name' => 'CC', 'type' => 'pos-terminal', 'icon' => 'mdi-credit-card-scan', 'status' => 1],
            ['name' => 'Cash', 'type' => 'cash', 'icon' => 'mdi-cash-usd', 'status' => 1],
            ['name' => 'CC API', 'type' => 'card', 'icon' => 'mdi-credit-card', 'status' => 1],
            ['name' => 'House Account', 'type' => 'house-account', 'icon' => 'mdi-home-variant-outline', 'status' => 1],
            ['name' => 'Coupon', 'type' => 'coupon', 'icon' => 'mdi-ticket', 'status' => 1],
            ['name' => 'Gift Card', 'type' => 'giftcard', 'icon' => 'mdi-wallet-giftcard', 'status' => 1],
        ]); 
    }
}
