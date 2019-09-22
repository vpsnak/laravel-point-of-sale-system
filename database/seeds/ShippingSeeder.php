<?php

use App\Price;
use App\Shipping;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shippings')->insert([
            [
                'name' => 'Drone Delivery',
                'price_id' => factory(Price::class)->create([
                    'amount' => 100,
                    'priceable_id' => 1,
                    'priceable_type' => Shipping::class
                ])->id
            ]
        ]);
        DB::table('shippings')->insert([
            [
                'name' => 'Nikos Delivery',
                'price_id' => factory(Price::class)->create([
                    'amount' => 100000,
                    'priceable_id' => 2,
                    'priceable_type' => Shipping::class
                ])->id
            ]
        ]);
        DB::table('shippings')->insert([
            [
                'name' => 'Free Shipping',
                'price_id' => factory(Price::class)->create([
                    'amount' => 0,
                    'priceable_id' => 3,
                    'priceable_type' => Shipping::class
                ])->id
            ]
        ]);
    }
}
