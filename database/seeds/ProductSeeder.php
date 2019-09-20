<?php

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
        factory(App\Product::class, 10)->make()->each(function ($product) {
            //            $product->save(factory(App\Post::class)->make());

            $price = factory(App\Price::class)->create();

            $product->save();
        });
    }
}
