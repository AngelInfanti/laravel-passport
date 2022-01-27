<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('/user')->group(function(){
    Route::post('/login', 'App\Http\Controllers\LoginController@login');
    Route::post('/register', 'App\Http\Controllers\LoginController@register');
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/assets', 'App\Http\Controllers\WalletController@assets');
        Route::post('/deposit', 'App\Http\Controllers\WalletController@deposit');
        Route::post('/pay', 'App\Http\Controllers\WalletController@pay');
        Route::post('/confirmPay', 'App\Http\Controllers\WalletController@confirmPay');
    });
});


