<?php

use App\Price;
use App\Product;
use App\Store;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Product::class, 30)->make()->each(function ($product) {
            $product->save();
            factory(Price::class)->create([
                'priceable_id' => $product->id,
                'priceable_type' => Product::class
            ]);
            foreach (Store::all() as $store) {
                $product->stores()->attach($store, ['qty' => rand(-100, 200)]);
            }
        });
    }
}
