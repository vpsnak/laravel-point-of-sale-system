<?php

/** @var Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'sku' => $faker->ean8,
//        'name' => $faker->name,
//        'photo_url' => $faker->imageUrl(640,480,'nature'),
        'name' => $faker->randomElement([
            'Sweet Season',
            'Make Me Blush',
            'Fireside',
            'Pink Perfection',
            'Limoncello Duo',
            'Music In Vienna',
            'Alani',
            'Blue Diamonds',
            'Friends & Family',
            'Friends Forever',
            'Merry & Bright',
            'Blooming Desert Duo',
        ]),
        'photo_url' => $faker->randomElement([
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10555_make_me_blush.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10556_sweet_season.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10572.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10583.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10661_beautiful_barcelona.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10138_truly_spectacular_1.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10579.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10333_alani.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10530_eclectica.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10563_tropical_sorbet.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10223_sienna.jpg',
            'https://www.plantshed.com//media/catalog/product/cache/1/small_image/551x/9df78eab33525d08d6e5fb8d27136e95/p/s/ps10561_a_moody_magic.jpg',
        ]),
    ];
});
