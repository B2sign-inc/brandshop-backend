<?php

use Faker\Generator as Faker;

use App\Models\Address;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'user_id' => 0,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'street_address' => $faker->streetAddress,
        'extra_address' => $faker->streetSuffix,
        'postcode' => $faker->postcode,
        'city' => $faker->city,
        'state' => $faker->colorName,
        'telephone' => $faker->phoneNumber
    ];
});
