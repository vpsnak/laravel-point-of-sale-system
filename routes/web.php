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

Route::get('/report/{report}', 'CashRegisterReportController@report');

Route::get('/order/{order}', 'OrderController@printOrder');

Route::get('/product_barcode/{product}', 'ProductController@productBarcode');

Route::get('/receipt/{order}', 'ReceiptController@receipt');

Route::get('/test_receipt/{order}', 'ReceiptController@printReceipt');
