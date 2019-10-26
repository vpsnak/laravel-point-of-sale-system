<?php

/** @var Factory $factory */

use App\Customer;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->email,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
    ];
});
