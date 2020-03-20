<?php

use App\Store;
use App\Product;
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
            $store_ids = Store::where('id', '>', 0)->pluck('id')->toArray();
            $product->stores()->attach($store_ids, ['qty' => rand(-100, 200)]);
        });
    }
}
