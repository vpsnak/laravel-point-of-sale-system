<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Price::class, function (Faker $faker) {
    return [
        'tax' => 24,
        'amount' => $faker->numberBetween(0, 100),
        'priceable_id' => $faker->unique()->numberBetween(1, 10),
        'priceable_type' => 'App\Product'
    ];
});
