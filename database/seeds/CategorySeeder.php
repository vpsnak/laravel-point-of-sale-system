<?php

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Category::class, 10)->make()->each(function ($category) {
            $category->save();
            $products = App\Product::allData()->random(10);
            $category->products()->attach($products);
        });
    }
}
