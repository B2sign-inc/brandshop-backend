<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Attribute::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'data_type' => array_random(['INTEGER','DECIMAL','DATETIME','VARCHAR','BOOLEAN','TEXT']),
        'field_type' => array_random(['TEXT', 'TEXTAREA', 'CKEDITOR', 'SELECT', 'FILE', 'DATETIME','CHECKBOX','RADIO','SWITCH']),
        'sort_order' => $faker->numberBetween(0, 10),
    ];
});
