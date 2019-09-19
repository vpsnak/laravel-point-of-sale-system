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

// Customers 
Route::get('/customers', 'CustomerController@getAll');
Route::post('/customers/create', 'CustomerController@create');
Route::post('/customers/search', 'CustomerController@search');

// Products 
Route::get('/products', 'ProductController@all');
Route::post('/products/search', 'ProductController@search');
