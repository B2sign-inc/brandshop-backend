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
Route::group(['namespace' => 'Api'], function () {
    Route::post('token/refresh', 'AuthController@refreshToken')->name('api.auth.token.refresh');
    Route::get('user/verify/{token}', 'AuthController@verifyUser')->name('api.user.verify');

    Route::post('login', 'AuthController@login')->name('api.login');
    Route::post('register', 'AuthController@register')->name('api.register');

    Route::get('categories', 'CategoryController@index')->name('api.categories.index');
    Route::get('categories/{category}/products', 'CategoryController@products')->name('api.categories.products');
    Route::get('products/{product}', 'ProductController@show')->name('api.products.show');
});


Route::group(['middleware' => ['auth:api'], 'namespace' => 'Api'], function () {
    Route::get('logout', 'AuthController@logout')->name('api.logout');

    Route::get('carts', 'CartController@index')->name('api.carts.index');
    Route::post('carts', 'CartController@store')->name('api.carts.store');
    Route::put('carts/{cart}', 'CartController@update')->name('api.carts.update');
    Route::delete('carts/empty', 'CartController@empty')->name('api.carts.empty');
    Route::delete('carts/{cart}', 'CartController@destroy')->name('api.carts.destroy');

    Route::post('orders/place', 'OrderController@place')->name('api.orders.place');
    Route::get('payments/token', 'PaymentController@generateBrainTreeToken')->name('api.payments.token');
    Route::post('payments/{order}/paid', 'PaymentController@paid')->name('api.payments.paid');

    Route::get('/user', function (Request $request) {
        return new \App\Http\Resources\UserResource($request->user());
    });
    Route::put('user', 'UserController@update')->name('api.user.update');
    Route::put('user/address/{address}', 'UserController@updateAddress')->name('api.user.update.address');

    Route::get('addresses', 'AddressesController@index')->name('api.addresses.index');
    Route::get('addresses/all', 'AddressesController@all')->name('api.addresses.all');
    Route::get('addresses/{address}', 'AddressesController@show')->name('api.addresses.show');
    Route::post('addresses', 'AddressesController@store')->name('api.addresses.store');
    Route::put('addresses/{address}', 'AddressesController@update')->name('api.addresses.update');
    Route::delete('addresses/{address}', 'AddressesController@destroy')->name('api.addresses.delete');

    Route::get('emailMessages', 'EmailMessagesController@index')->name('api.emailMessages.index');
    Route::get('emailMessages/{emailMessage}', 'EmailMessagesController@show')->name('api.emailMessages.show');

    Route::get('shippingMethod/all', 'ShippingMethodController@all')->name('api.shippingMethod.all');
});