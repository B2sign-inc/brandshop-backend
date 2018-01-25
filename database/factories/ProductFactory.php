<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Product::class, function (Faker $faker) {
    $name = $faker->name;
    return [
        'name' => $name,
        'images' => [(string)Avatar::create($name)->toBase64()],
        'price' => $faker->randomFloat(2, 0, 200),
        'stock' => $faker->numberBetween(0, 100),
        'description' => $faker->realText(),
    ];
});
