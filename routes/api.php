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
    'users' => 'UserController',
    'customers' => 'CustomerController',
    'address' => 'AddressController',
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

Route::post('/cash-register-logs/open', "{$baseRoutes['cash-register-logs']}@open");
Route::post('/cash-register-logs/close', "{$baseRoutes['cash-register-logs']}@close");

Route::post('/shipping/timeslot', "TimeslotController@getTimeslots");

Route::get('/magento/authorize', 'Auth\MagentoOAuthController@authorizeMagento');

// POS

// config
Route::post('/pos-terminal/configure', 'PosTerminalController@startCardReaderConfiguration');
Route::post('/pos-terminal/configure/status', 'PosTerminalController@getCommandStatusOnCardReader');
// search - info
Route::post('/pos-terminal/search', 'PosTerminalController@startCardReadersSearch');
Route::post('/pos-terminal/search/status', 'PosTerminalController@getCardReadersSearchStatus');
Route::post('/pos-terminal/info', 'PosTerminalController@getCardReaderInfo');
// transaction
Route::post('/pos-terminal/transaction/gateway', 'PosTerminalController@openPaymentGateway');
Route::post('/pos-terminal/transaction/start', 'PosTerminalController@startPaymentTransaction');
Route::post('/pos-terminal/transaction/status', 'PosTerminalController@getPaymentTransactionStatus');
