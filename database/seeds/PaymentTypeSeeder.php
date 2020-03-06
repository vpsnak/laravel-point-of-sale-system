<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_types')->insert([
            ['name' => 'Credit Card (POS)', 'type' => 'pos-terminal', 'icon' => 'mdi-credit-card-scan', 'is_enabled' => true],
            ['name' => 'Cash', 'type' => 'cash', 'icon' => 'mdi-cash-usd', 'is_enabled' => true],
            ['name' => 'Credit Card (keyed)', 'type' => 'card', 'icon' => 'mdi-credit-card', 'is_enabled' => true],
            ['name' => 'House Account', 'type' => 'house-account', 'icon' => 'mdi-home-variant-outline', 'is_enabled' => true],
            ['name' => 'Coupon', 'type' => 'coupon', 'icon' => 'mdi-ticket', 'is_enabled' => true],
            ['name' => 'Gift Card', 'type' => 'giftcard', 'icon' => 'mdi-wallet-giftcard', 'is_enabled' => true],
        ]);
    }
}
