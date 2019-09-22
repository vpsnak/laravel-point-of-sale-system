<?php

/** @var Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'sku' => $faker->uuid,
        'name' => $faker->name,
        'photo_url' => $faker->imageUrl(),
    ];
});
