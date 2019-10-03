<?php

use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payments')->insert([
            [
                'payment_type' => 1,
                'amount' => 12,
                'cash_register_id' => 1,
                'order_id' => 1,
                'created_by' => 1
            ],
            [
                'payment_type' => 2,
                'amount' => 12,
                'cash_register_id' => 1,
                'order_id' => 1,
                'created_by' => 1
            ],
            [
                'payment_type' => 3,
                'amount' => 12,
                'cash_register_id' => 1,
                'order_id' => 1,
                'created_by' => 1
            ],
            [
                'payment_type' => 4,
                'amount' => 12,
                'cash_register_id' => 1,
                'order_id' => 1,
                'created_by' => 1
            ],
        ]);
    }
}
