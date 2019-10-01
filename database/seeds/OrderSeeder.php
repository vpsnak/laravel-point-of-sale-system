<?php

use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
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
                'customer_id' => 1,
                'store_id' => 1,
                'created_by' => 1,
                'status' => '??',
                'items' => '??',
                'discount_type' => null,
                'discount_amount' => null,
                'shipping_type' => '??',
                'shipping_cost' => 'asd',
                'tax' => '??',
                'subtotal' => '??',
                'notes' => 'asd',
            ]
        ]);
    }
}
