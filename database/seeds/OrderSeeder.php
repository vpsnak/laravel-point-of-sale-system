<?php

use App\Order;
use App\OrderProduct;
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
        Order::store([
            'customer_id' => 1,
            'store_id' => 1,
            'created_by' => 1,
            'status' => 'pending',
            'discount_type' => 'flat',
            'discount_amount' => 5,
            'shipping_type' => '??',
            'shipping_cost' => 10,
            'tax' => 5,
            'subtotal' => 121,
            'notes' => 'eksogioinoi diastimoploia kai ousies',
        ]);
        OrderProduct::store([
            'order_id' => 1,
            'name' => 'Paionia on fire',
            'sku' => 'p-1337',
            'price' => 4.20,
            'qty' => 30,
            'discount_type' => 'percentage',
            'discount_amount' => 10,
            'notes' => 'king size wrapping',
        ]);
    }
}
