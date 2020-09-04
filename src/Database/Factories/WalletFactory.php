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

$factory->define( \M74asoud\Paymenter\Models\Wallet::class, function ( Faker $faker ) {
    $amount = $faker->randomElement( [ 1000, 2000, 3000, 500, 850000 ] );

    return [
        'user_hash' => $faker->md5,
        'before'    => 0,
        'amount'    => $amount,
        'balance'   => $amount,
    ];

} );
