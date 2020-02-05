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

Route::get('/', 'AppController@index');

Route::get('/receipt/{order}', 'AppController@receipt');

Route::get('/report/{report}', 'AppController@report');

Route::get('/product_barcode/{product}', 'AppController@productBarcode');

Route::get('/test_receipt/{model}', 'ReceiptController@create');
