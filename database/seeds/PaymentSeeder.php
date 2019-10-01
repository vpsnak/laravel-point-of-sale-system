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
            ['payment_type' => 'cash', 'amount' => 12, 'cash_register_id' => 1, 'order_id' => 1, 'created_by' => 1]
        ]);
    }
}
