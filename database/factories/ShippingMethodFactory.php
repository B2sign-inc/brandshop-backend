<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ShippingMethod::class, function (Faker $faker) {
    return [
        'code' => $faker->shuffleString(),
        'name' => $faker->shuffleString(),
    ];
});
