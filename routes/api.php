<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserVehicleController;
use App\Http\Controllers\VehicleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


$api = app('Dingo\Api\Routing\Router');
//Api versioning
$api->version('v1', function (Dingo\Api\Routing\Router $api) {
    //routes that accessible by guests
    $api->group(['middleware' => ['guest']], function ($api) {
        $api->post('/register', AuthController::class . '@register');
        $api->post('/login', AuthController::class . '@login');
    });

    $api->get('/token', AuthController::class . '@refreshToken')->middleware('jwt.refresh');
    //Authentication restricted routes
    $api->group(['middleware' => ['jwt.auth', 'bindings']], function (Dingo\Api\Routing\Router $api) {

        $api->get('/user', AuthController::class . '@user');
        $api->get('/logout', AuthController::class . '@logout');
        $api->resource('users', UserController::class);
        $api->resource('vehicles', VehicleController::class);
        $api->resource('users.vehicles', UserVehicleController::class);
        $api->get('/dbSeed', function(){ Artisan::call('db:seed');  return response('Success');  });

        //In these routes, request object will return null instead of empty string
        //to ease frontend display and backend updating actions

        $api->group(['middleware' => ['fromNullToString']], function (Dingo\Api\Routing\Router $api) {
        });
    });
    $api->get('/checkEmail', UserController::class . '@checkEmail');
    $api->get('/checkPlate', VehicleController::class . '@checkPlate');
    $api->get('/checkName', UserController::class . '@checkName');
});



