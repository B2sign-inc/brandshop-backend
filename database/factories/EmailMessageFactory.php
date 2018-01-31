<?php

use Faker\Generator as Faker;
use App\Models\EmailMessage;
use Carbon\Carbon;

$factory->define(EmailMessage::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'to_name' => $faker->name,
        'to_address' => $faker->email,
        'from_name' => $faker->name,
        'from_address' => $faker->email,
        'subject' => $faker->sentence,
        'body' => $faker->paragraph,
        'date_sent' => Carbon::now()->toDateTimeString(),
    ];
});
