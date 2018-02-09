<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Cart::class, function (Faker $faker) {
    return [
        'product_id' => factory(\App\Models\Product::class)->create()->id,
        'quantity' => $faker->numberBetween(1, 10),
    ];
});
