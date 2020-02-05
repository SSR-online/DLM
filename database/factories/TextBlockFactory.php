<?php

use Faker\Generator as Faker;

$factory->define(App\TextBlock::class, function (Faker $faker) {
    return [
        'content' => $faker->paragraph(3)
    ];
});
