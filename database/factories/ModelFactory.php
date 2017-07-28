<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/



/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->unique()->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
    ];
});

$factory->define(App\Brand::class, function(Faker\Generator $faker){
    $faker->addProvider(new \MattWells\Faker\Vehicle\Provider($faker));
    return [
    'name'=> $faker->vehicleMake,
    ];
});

$factory->define(App\Type::class, function(Faker\Generator $faker){
    $faker->addProvider(new \MattWells\Faker\Vehicle\Provider($faker));
    return [
        'name'=> $faker->streetName,
    ];
});

$factory->define(App\Vmodel::class, function(Faker\Generator $faker){
    $faker->addProvider(new \MattWells\Faker\Vehicle\Provider($faker));
    return [
        'name'=>  $faker->vehicleModel,
        'year' => rand(1901, 2017),
        'type_id' => App\Type::orderByRaw("RAND()")->first()->id,
        'brand_id' => App\Brand::orderByRaw("RAND()")->first()->id
    ];
});



/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Vehicle::class, function (Faker\Generator $faker) {
    $faker->addProvider(new \MattWells\Faker\Vehicle\Provider($faker));

    return [
        'plate' => rand(10, 81) . "MNM" .rand(10, 999),
        'nickname' => $faker->unique()->userName,
        'color' => $faker->safeColorName,
        'active' => $faker->boolean,
        'user_id' => App\User::orderByRaw("RAND()")->first()->id,
        'vmodel_id' => App\Vmodel::orderByRaw("RAND()")->first()->id
    ];
});

