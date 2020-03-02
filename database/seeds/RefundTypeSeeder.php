<?php

use Illuminate\Database\Seeder;

class RefundTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('refund_types')->insert([
            ['name' => 'Card', 'type' => 'refund-card', 'icon' => 'mdi-credit-card-outline', 'is_enabled' => false],
            ['name' => 'New gift card', 'type' => 'refund-giftcard-new', 'icon' => 'mdi-card-plus-outline', 'is_enabled' => false],
            ['name' => 'Existing gift card', 'type' => 'refund-giftcard-existing', 'icon' => 'mdi-wallet-giftcard', 'is_enabled' => false],
            ['name' => 'Cash', 'type' => 'refund-cash', 'icon' => 'mdi-cash-usd', 'is_enabled' => false],
            ['name' => 'Refund API', 'type' => 'refund-api', 'icon' => 'mdi-cash-usd', 'is_enabled' => false],
            ['name' => 'Refund POS', 'type' => 'refund-pos', 'icon' => 'mdi-cash-usd', 'is_enabled' => false]
        ]);
    }
}
