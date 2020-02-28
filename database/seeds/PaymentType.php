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
            ['name' => 'CC', 'type' => 'pos-terminal', 'icon' => 'mdi-credit-card-scan', 'status' => true, 'hidden' => false],
            ['name' => 'Cash', 'type' => 'cash', 'icon' => 'mdi-cash-usd', 'status' => true, 'hidden' => false],
            ['name' => 'CC API', 'type' => 'card', 'icon' => 'mdi-credit-card', 'status' => true, 'hidden' => false],
            ['name' => 'House Account', 'type' => 'house-account', 'icon' => 'mdi-home-variant-outline', 'status' => true, 'hidden' => false],
            ['name' => 'Coupon', 'type' => 'coupon', 'icon' => 'mdi-ticket', 'status' => true, 'hidden' => false],
            ['name' => 'Gift Card', 'type' => 'giftcard', 'icon' => 'mdi-wallet-giftcard', 'status' => true, 'hidden' => false],
            // refund types
            ['name' => 'Card', 'type' => 'refund-card', 'icon' => 'mdi-credit-card-outline', 'status' => false, 'hidden' => false],
            ['name' => 'New gift card', 'type' => 'refund-giftcard-new', 'icon' => 'mdi-card-plus-outline', 'status' => false, 'hidden' => false],
            ['name' => 'Existing gift card', 'type' => 'refund-giftcard-existing', 'icon' => 'mdi-wallet-giftcard', 'status' => false, 'hidden' => false],
            ['name' => 'Cash', 'type' => 'refund-cash', 'icon' => 'mdi-cash-usd', 'status' => false, 'hidden' => false],
            ['name' => 'Refund API', 'type' => 'refund-api', 'icon' => 'mdi-cash-usd', 'status' => false, 'hidden' => true],
            ['name' => 'Refund POS', 'type' => 'refund-pos', 'icon' => 'mdi-cash-usd', 'status' => false, 'hidden' => true],
        ]);
    }
}
