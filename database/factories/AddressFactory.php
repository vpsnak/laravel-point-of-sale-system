<?php

/** @var Factory $factory */

use App\Address;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'street' => $faker->streetAddress,
        'city' => $faker->city,
        'country_id' => $faker->countryCode,
        'region' => $faker->country,
        'postcode' => $faker->randomElement(['00606', '10025', '01524', '01561', '97029', '0']),
        'phone' => $faker->phoneNumber,
    ];
});
