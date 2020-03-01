<?php

/** @var Factory $factory */

use App\Address;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'customer_id' => $faker->numberBetween(31, 60),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'street' => $faker->streetAddress,
        'street2' => $faker->streetAddress,
        'city' => $faker->city,
        'region_id' => $faker->numberBetween(1, 100),
        'postcode' => $faker->randomElement(['00606', '10025', '01524', '01561', '97029', '0']),
        'phone' => $faker->numberBetween(10000000000, 20000000000)
    ];
});
