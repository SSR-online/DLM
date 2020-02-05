<?php

use Faker\Generator as Faker;

$factory->define(App\Node::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence
    ];
});
