<?php

use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'auth'], function () {
    Route::controller(UserAuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        // Route::get('verify/{token}', 'verify');
        Route::post('verify', 'verify');
    });
});

Route::controller(UserController::class)->middleware('auth')->group(function () {
    Route::get('user-profile', 'userProfile');
});

Route::group(['prefix' => 'user'], function () {
    Route::controller(UserController::class)->group(function () {
        // Users
        Route::post('store', 'store');
        Route::post('update/{id}', 'update');
        Route::get('show/{id}', 'show');
        Route::delete('delete/{id}', 'destroy');

    });
});


// Route::post('login', '\App\Http\Controllers\AuthController@login');
// Route::post('logout', '\App\Http\Controllers\AuthController@logout')->middleware('jwt.auth');;
// Route::post('refresh', '\App\Http\Controllers\AuthController@refresh');

// Route::get('protected', function () {
//     // Only authenticated users can access this route
// })->middleware('jwt.auth');


// Route::group([

//     'middleware' => 'api',
//     'prefix' => 'auth'

// ], function ($router) {

//     Route::post('login', 'AuthController@login');
//     Route::post('logout', 'AuthController@logout');
//     Route::post('refresh', 'AuthController@refresh');
//     Route::post('me', 'AuthController@me');

// });
