<?php

/** @var Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Price::class, function (Faker $faker) {
    return [
        'tax' => 24,
        'amount' => $faker->numberBetween(0, 100)
    ];
});
