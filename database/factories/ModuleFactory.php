<?php

use Faker\Generator as Faker;

$factory->define(App\Module::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
    ];
});
