<?php

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

$baseRoutes = [
    'customers' => 'CustomerController',
    'address' => 'AddressController',
    'products' => 'ProductController',
    'carts' => 'CartController',
    'orders' => 'OrderController',
];

foreach ($baseRoutes as $route => $controller) {
    Route::get("/$route", "$controller@all");
    Route::post("/$route/create", "$controller@create");
    Route::post("/$route/search", "$controller@search");
}

Route::get('/carts/hold', "{$baseRoutes['carts']}@getHold");
Route::get('/carts/{model}', "{$baseRoutes['carts']}@delete");