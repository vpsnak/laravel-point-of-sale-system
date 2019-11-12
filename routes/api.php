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
Route::post('/auth/login', "UserController@login")->middleware('guest');
Route::get('/auth/logout', "UserController@logout")->middleware('scope:admin,cashier,store_manager');
Route::post('/auth/change-password', "UserController@changePassword")->middleware('scope:admin,cashier,store_manager');

// roles
Route::get('/roles', "RoleController@all")->middleware('scope:admin');
Route::post('/roles/set', "RoleController@setRole")->middleware('scope:admin');

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
    'cash-register-reports' => 'CashRegisterReportController',
    'gift-cards' => 'GiftCardController',
    'coupons' => 'CouponController',
    'regions' => 'RegionController',
    'countries' => 'CountryController',
    'store-pickups' => 'StorePickupController',
];

Route::get('categories/{category}/products', "CategoryController@productsByCategory")->middleware('scope:admin,cashier,store_manager');

foreach ($baseRoutes as $route => $controller) {
    Route::get("/$route", "$controller@all")->middleware('scope:admin,cashier,store_manager');
    Route::get("/$route/{id}", "$controller@get")->middleware('scope:admin,cashier,store_manager');
    Route::post("/$route/create", "$controller@create")->middleware('scope:admin,cashier,store_manager');
    Route::post("/$route/search", "$controller@search")->middleware('scope:admin,cashier,store_manager');
    Route::delete("/$route/{id}", "$controller@delete")->middleware('scope:admin');;
}

Route::get('/products/barcode/{id}', "{$baseRoutes['products']}@getBarcode")->middleware('scope:admin,cashier,store_manager');

Route::get('/carts/hold', "{$baseRoutes['carts']}@getHold")->middleware('scope:admin,cashier,store_manager');
Route::get('/product-listing/categories', "CategoryController@productListingCategories")->middleware('scope:admin,cashier,store_manager');

Route::post('/cash-register-logs/open', "{$baseRoutes['cash-register-logs']}@open")->middleware('scope:admin,cashier,store_manager');
Route::post('/cash-register-logs/close', "{$baseRoutes['cash-register-logs']}@close")->middleware('scope:admin,cashier,store_manager');

Route::post('/shipping/timeslot', "TimeslotController@getTimeslots")->middleware('scope:admin,cashier,store_manager');

Route::get('/magento/authorize', 'Auth\MagentoOAuthController@authorizeMagento');

// e-mail
Route::get('/mail-receipt', 'MailReceiptController@template');
Route::post('/mail-receipt/{order}', 'MailReceiptController@send')->middleware('scope:admin,cashier,store_manager');

// guest email list
Route::get('/guest-email', 'GuestEmailListController@all')->middleware('scope:admin,cashier,store_manager');
Route::post('/guest-email/create', 'GuestEmailListController@create')->middleware('scope:admin,cashier,store_manager');

// elavon sdk certification
Route::post('/elavon/sdk', 'ElavonSdkPaymentController@index')->middleware('scope:admin,cashier,store_manager');
Route::post('/elavon/sdk/lookup', 'ElavonSdkPaymentController@lookup')->middleware('scope:admin,cashier,store_manager');
Route::get('/elavon/sdk/logs', 'ElavonSdkPaymentController@getLogs')->middleware('scope:admin,cashier,store_manager');
Route::get('/elavon/sdk/logs/{test_case}', 'ElavonSdkPaymentController@getLogs')->middleware('scope:admin,cashier,store_manager');
Route::delete('/elavon/sdk/logs/delete', 'ElavonSdkPaymentController@deleteAll')->middleware('scope:admin,cashier,store_manager');

// elavon api certification
Route::post('/elavon/api', 'ElavonApiPaymentController@index')->middleware('scope:admin,cashier,store_manager');
Route::post('/elavon/api/lookup', 'ElavonApiPaymentController@lookup')->middleware('scope:admin,cashier,store_manager');
Route::get('/elavon/api/logs', 'ElavonApiPaymentController@getLogs')->middleware('scope:admin,cashier,store_manager');
Route::get('/elavon/api/logs/{test_case}', 'ElavonApiPaymentController@getLogs')->middleware('scope:admin,cashier,store_manager');
Route::delete('/elavon/api/logs/delete', 'ElavonApiPaymentController@deleteAll')->middleware('scope:admin,cashier,store_manager');
