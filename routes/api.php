<?php

use Illuminate\Http\Request;

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

Route::post('login', 'Api\AuthController@login')->name('api.login');
Route::post('register', 'Api\AuthController@register')->name('api.register');
Route::post('token/refresh', 'Api\AuthController@refreshToken')->name('api.auth.token.refresh');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('logout', 'Api\AuthController@logout')->name('api.logout'); 
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
