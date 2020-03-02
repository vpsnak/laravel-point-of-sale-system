<?php

use App\Price;
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
            $product->stores()->attach(1, ['qty' => rand(-100, 200)]);
        });
    }
}
