<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ShippingMethod::class, function (Faker $faker) {
    return [
        'code' => $faker->text(20),
        'name' => $faker->name,
    ];
});
