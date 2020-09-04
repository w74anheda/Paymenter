<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define( \M74asoud\Paymenter\Models\Bill::class, function ( Faker $faker ) {

    return [
        'user_hash'  => $faker->md5,
        'hash'       => $faker->md5,
        'status'     => 0,
        'amount'     => $faker->randomElement( [ 100000, 20000, 250000, 5000000 ] ),
        'actionType' => $faker->randomElement( [ 0, 1 ] ),
        'type'      => 100,
    ];

} );
