<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Product::class, function (Faker $faker) {
    return [
        'sku' => $faker->uuid,
        'name' => $faker->name,
        'photo_url' => $faker->imageUrl(),
    ];
});
