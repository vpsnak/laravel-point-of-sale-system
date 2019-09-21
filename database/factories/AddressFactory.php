<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Address::class, function (Faker $faker) {
    return [
        'area_code_id' => $faker->randomNumber(),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'street' => $faker->streetAddress,
        'city' => $faker->city,
        'country_id' => $faker->countryCode,
        'region' => $faker->country,
        'postcode' => $faker->postcode,
        'phone' => $faker->phoneNumber,
    ];
});
