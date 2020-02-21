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
            // payment types
            ['name' => 'CC', 'type' => 'pos-terminal', 'icon' => 'mdi-credit-card-scan', 'status' => true],
            ['name' => 'Cash', 'type' => 'cash', 'icon' => 'mdi-cash-usd', 'status' => true],
            ['name' => 'CC API', 'type' => 'card', 'icon' => 'mdi-credit-card', 'status' => true],
            ['name' => 'House Account', 'type' => 'house-account', 'icon' => 'mdi-home-variant-outline', 'status' => true],
            ['name' => 'Coupon', 'type' => 'coupon', 'icon' => 'mdi-ticket', 'status' => true],
            ['name' => 'Gift Card', 'type' => 'giftcard', 'icon' => 'mdi-wallet-giftcard', 'status' => true],
            // refund types
            ['name' => 'Existing gift card', 'type' => 'giftcard-existing', 'icon' => 'mdi-wallet-giftcard', 'status' => false],
            ['name' => 'New gift card', 'type' => 'giftcard-new', 'icon' => 'mdi-wallet-giftcard', 'status' => false],
        ]);
    }
}
