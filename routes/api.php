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

// auth

Route::post('/auth/login', "UserController@login");

Route::get('/auth/logout', "UserController@logout")->middleware('auth:api');

$baseRoutes = [
    'users' => 'UserController',
    'customers' => 'CustomerController',
    'addresses' => 'AddressController',
    'products' => 'ProductController',
    'carts' => 'CartController',
    'orders' => 'OrderController',
    'categories' => 'CategoryController',
    'stores' => 'StoreController',
    'taxes' => 'TaxController',
    'payments' => 'PaymentController',
    'payment-types' => 'PaymentTypeController',
    'cash-registers' => 'CashRegisterController',
    'cash-register-logs' => 'CashRegisterLogsController',
    'gift-cards' => 'GiftCardController',
    'coupons' => 'CouponController',
    'regions' => 'RegionController',
    'countries' => 'CountryController',
];

Route::get('categories/{category}/products', "CategoryController@productsByCategory");

foreach ($baseRoutes as $route => $controller) {
    Route::get("/$route", "$controller@all");
    Route::get("/$route/{id}", "$controller@get");
    Route::post("/$route/create", "$controller@create");
    Route::post("/$route/search", "$controller@search");
    Route::delete("/$route/{id}", "$controller@delete");
}

Route::get('/carts/hold', "{$baseRoutes['carts']}@getHold");
Route::get('/product-listing/categories', "CategoryController@productListingCategories");

Route::post('/cash-register-logs/open', "{$baseRoutes['cash-register-logs']}@open")->middleware('auth:api');
Route::post('/cash-register-logs/close', "{$baseRoutes['cash-register-logs']}@close")->middleware('auth:api');

Route::post('/shipping/timeslot', "TimeslotController@getTimeslots");

Route::get('/magento/authorize', 'Auth\MagentoOAuthController@authorizeMagento');

// e-mail
Route::get('/sendemail', 'SendEmailController@index');
Route::get('/send/{order}', 'SendEmailController@send');

// elavon certification
Route::post('/elavon/sdk', 'ElavonSdkPaymentController@index')->middleware('auth:api');
Route::get('/elavon/sdk/logs', 'ElavonSdkPaymentController@getLogs')->middleware('auth:api');
Route::get('/elavon/sdk/logs/delete', 'ElavonSdkPaymentController@deleteAll')->middleware('auth:api');
Route::post('/elavon/api', 'ElavonApiPaymentController@index')->middleware('auth:api');
Route::get('/elavon/api/logs', 'ElavonApiPaymentController@getLogs')->middleware('auth:api');
Route::get('/elavon/api/logs/delete', 'ElavonApiPaymentController@deleteAll')->middleware('auth:api');
