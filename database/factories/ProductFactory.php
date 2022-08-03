<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'code' => $faker->randomNumber(),
        'name' => $faker->word(),
        'rate' => $faker->randomNumber()
    ];
});
