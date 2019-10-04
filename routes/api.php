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

foreach ($baseRoutes as $route => $controller) {
    Route::get("/$route", "$controller@all");
    Route::get("/$route/{id}", "$controller@get");
    Route::post("/$route/create", "$controller@create");
    Route::post("/$route/search", "$controller@search");
    Route::delete("/$route/{id}", "$controller@delete");
}

Route::get('/carts/hold', "{$baseRoutes['carts']}@getHold");
Route::get('/product-listing/categories', "CategoryController@productListingCategories");

Route::get('/magento/authorize', 'Auth\MagentoOAuthController@authorizeMagento');
