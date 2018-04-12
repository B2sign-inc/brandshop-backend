<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('user/verify/{token}', 'Api\AuthController@verifyUser')->name('user.verify');

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function ($router) {
    $router->get('/', function() {
       return redirect()->route('admin.dashboard');
    });
    $router->get('login', 'AuthController@showLoginForm')->name('admin.login');
    $router->post('login', 'AuthController@login');
    $router->post('logout', 'AuthController@logout')->name('admin.logout');

    $router->get('dashboard', 'DashboardController@index')->name('admin.dashboard');

    $router->get('categories', 'CategoryController@index')->name('admin.categories.index');
    $router->post('categories', 'CategoryController@save')->name('admin.categories.save');
    $router->delete('categories/{category}', 'CategoryController@destroy')->name('admin.categories.destroy');
    $router->post('categories/resetAndSync', 'CategoryController@resetAndSyncFromB2sign')->name('admin.categories.resetAndSync');


    $router->get('products', 'ProductController@index')->name('admin.products.index');
});