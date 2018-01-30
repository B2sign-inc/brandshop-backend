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


Route::post('token/refresh', 'Api\AuthController@refreshToken')->name('api.auth.token.refresh');
Route::get('user/verify/{token}', 'Api\AuthController@verifyUser')->name('api.user.verify');

Route::post('login', 'Api\AuthController@login')->name('api.login');
Route::post('register', 'Api\AuthController@register')->name('api.register');

Route::get('categories', 'Api\CategoryController@index')->name('api.categories.index');
Route::get('categories/{category}/products', 'Api\CategoryController@products')->name('api.categories.products');
Route::get('products/{product}', 'Api\ProductController@show')->name('api.products.show');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('logout', 'Api\AuthController@logout')->name('api.logout');

    Route::post('carts', 'Api\CartController@store')->name('api.carts.store');
    Route::put('carts/{cart}', 'Api\CartController@update')->name('api.carts.update');
    Route::delete('carts/{cart}', 'Api\CartController@destroy')->name('api.carts.destroy');

    Route::get('/user', function (Request $request) {
        return new \App\Http\Resources\UserResource($request->user());
    });
});

Route::group(['middleware' => ['auth:api'], 'namespace' => 'Api'], function () {
    Route::get('addresses', 'AddressesController@index')->name('api.addresses.index');
    Route::get('addresses/{address}', 'AddressesController@show')->name('api.addresses.show');
    Route::post('addresses', 'AddressesController@store')->name('api.addresses.store');
    Route::put('addresses/{address}', 'AddressesController@update')->name('api.addresses.update');
    Route::delete('addresses/{address}', 'AddressesController@destroy')->name('api.addresses.delete');
});
