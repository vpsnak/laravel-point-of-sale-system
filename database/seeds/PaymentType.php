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
            ['name' => 'POS Terminal', 'type' => 'pos-terminal', 'icon' => 'mdi-credit-card-scan', 'status' => 1, 'is_default' => 1, 'created_by' => 1],
            ['name' => 'Cash', 'type' => 'cash', 'icon' => 'mdi-cash-usd', 'status' => 1, 'is_default' => 0, 'created_by' => 1],
            ['name' => 'Credit Card', 'type' => 'card', 'icon' => 'mdi-credit-card', 'status' => 1, 'is_default' => 0, 'created_by' => 1],
            ['name' => 'Coupon', 'type' => 'coupon', 'icon' => 'mdi-ticket', 'status' => 1, 'is_default' => 0, 'created_by' => 1],
            ['name' => 'Gift Card', 'type' => 'giftcard', 'icon' => 'mdi-wallet-giftcard', 'status' => 1, 'is_default' => 0, 'created_by' => 1],
        ]);
    }
}
