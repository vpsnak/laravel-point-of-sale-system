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

Route::get('/test/{id}', 'AppController@test');

// mas
// Route::get('/mas/test', 'MasAccountController@test');
Route::get('/mas/env', 'MasAccountController@getEnv')->middleware('scope:admin,store_manager,cashier');
Route::get('/mas/set/{mode}', 'MasAccountController@setEnv')->middleware('scope:admin');

// roles
Route::get('/roles', "RoleController@all")->middleware('scope:admin');
Route::post('/roles/set', "RoleController@setRole")->middleware('scope:admin');

//auth
Route::get('/auth/logout', 'UserController@logout')->middleware('scope:admin,store_manager,cashier');
Route::post('/auth/login', 'UserController@login')->middleware('guest');
Route::post('/auth/verify', 'UserController@verifySelfPwd')->middleware('scope:admin,store_manager,cashier');
Route::post('/auth/password', 'UserController@changeSelfPwd')->middleware('scope:admin,store_manager');

// users
Route::get('/users', 'UserController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/users/get/{id}', 'UserController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/users/create', 'UserController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/users/search', 'UserController@search')->middleware('scope:admin,store_manager,cashier');
Route::post('/users/password', 'UserController@changeUserPwd')->middleware('scope:admin');
Route::patch('/users/update/{model}', 'UserController@update')->middleware('scope:admin,store_manager');
Route::delete('/users/{id}', 'UserController@delete')->middleware('scope:admin');

// payments
Route::get('/payments', 'PaymentController@all')->middleware('scope:admin,cashier');
Route::get('/payments/get/{payment}', 'PaymentController@get')->middleware('scope:admin,cashier');
Route::post('/payments/create', 'PaymentController@create')->middleware('scope:admin,cashier');
Route::post('/payments/search', 'PaymentController@search')->middleware('scope:admin,cashier');
Route::delete('/payments/{payment}', 'PaymentController@refundPayment')->middleware('scope:admin,cashier');

// customers
Route::get('/customers', 'CustomerController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/customers/get/{id}', 'CustomerController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/customers/create', 'CustomerController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/customers/search', 'CustomerController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/customers/{id}', 'CustomerController@delete')->middleware('scope:admin,store_manager,cashier');

// addresses
Route::get('/addresses', 'AddressController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/addresses/get/{id}', 'AddressController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/addresses/create', 'AddressController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/addresses/search', 'AddressController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/addresses/{id}', 'AddressController@delete')->middleware('scope:admin,store_manager,cashier');

// orders
Route::get('/orders', 'OrderController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/orders/get/{model}', 'OrderController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/orders/create', 'OrderController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/orders/search', 'OrderController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/orders/{model}', 'OrderController@rollbackOrder')->middleware('scope:admin,store_manager,cashier');

// stores
Route::get('/stores', 'StoreController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/stores/get/{id}', 'StoreController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/stores/create', 'StoreController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/stores/search', 'StoreController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/stores/{id}', 'StoreController@delete')->middleware('scope:admin,store_manager,cashier');

// taxes
Route::get('/taxes', 'TaxController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/taxes/get/{id}', 'TaxController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/taxes/create', 'TaxController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/taxes/search', 'TaxController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/taxes/{id}', 'TaxController@delete')->middleware('scope:admin,store_manager,cashier');

// payment-types
Route::get('/payment-types', 'PaymentTypeController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/payment-types/get/{id}', 'PaymentTypeController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/payment-types/create', 'PaymentTypeController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/payment-types/search', 'PaymentTypeController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/payment-types/{id}', 'PaymentTypeController@delete')->middleware('scope:admin,store_manager,cashier');

// cash-registers
Route::get('/cash-registers', 'CashRegisterController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-registers/get/{id}', 'CashRegisterController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-registers/create', 'CashRegisterController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-registers/search', 'CashRegisterController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/cash-registers/{id}', 'CashRegisterController@delete')->middleware('scope:admin,store_manager,cashier');

// cash-register-reports
Route::get('/cash-register-reports', 'CashRegisterReportController@all')->middleware('scope:admin,store_manager,cashier');;
Route::get('/cash-register-reports/get/{id}', 'CashRegisterReportController@get')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-register-reports/check/{cashRegister}', "CashRegisterReportController@checkCurrent")->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-reports/create', 'CashRegisterReportController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-reports/search', 'CashRegisterReportController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/cash-register-reports/{id}', 'CashRegisterReportController@delete')->middleware('scope:admin,store_manager,cashier');

// gift-cards
Route::get('/gift-cards', 'GiftcardController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/gift-cards/get/{id}', 'GiftcardController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/gift-cards/create', 'GiftcardController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/gift-cards/search', 'GiftcardController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/gift-cards/{id}', 'GiftcardController@delete')->middleware('scope:admin,store_manager,cashier');

// coupons
Route::get('/coupons', 'CouponController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/coupons/get/{id}', 'CouponController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/coupons/create', 'CouponController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/coupons/search', 'CouponController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/coupons/{id}', 'CouponController@delete')->middleware('scope:admin,store_manager,cashier');

// regions
Route::get('/regions', 'RegionController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/regions/get/{id}', 'RegionController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/regions/create', 'RegionController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/regions/search', 'RegionController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/regions/{id}', 'RegionController@delete')->middleware('scope:admin,store_manager,cashier');

// countries
Route::get('/countries', 'CountryController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/countries/get/{id}', 'CountryController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/countries/create', 'CountryController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/countries/search', 'CountryController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/countries/{id}', 'CountryController@delete')->middleware('scope:admin,store_manager,cashier');

// store-pickups
Route::get('/store-pickups', 'StorePickupController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/store-pickups/get/{id}', 'StorePickupController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/store-pickups/create', 'StorePickupController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/store-pickups/search', 'StorePickupController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/store-pickups/{id}', 'StorePickupController@delete')->middleware('scope:admin,store_manager,cashier');

// companies
Route::get('/companies', 'CompanyController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/companies/get/{id}', 'CompanyController@get')->middleware('scope:admin,store_manager,cashier');
Route::post('/companies/create', 'CompanyController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/companies/search', 'CompanyController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/companies/{id}', 'CompanyController@delete')->middleware('scope:admin,store_manager,cashier');

//products
Route::get('/products', 'ProductController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/products/get/{id}', 'ProductController@get')->middleware('scope:admin,store_manager,cashier');
Route::get('/products/barcode/{id}', 'ProductController@getBarcode')->middleware('scope:admin,store_manager,cashier');
Route::post('/products/create', 'ProductController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/products/search', 'ProductController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/products/{id}', 'ProductController@delete')->middleware('scope:admin');

//carts
Route::get('/carts', 'CartController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/carts/get/{id}', 'CartController@get')->middleware('scope:admin,store_manager,cashier');
Route::get('/carts/hold', 'CartController@getHold')->middleware('scope:admin,store_manager,cashier');
Route::post('/carts/create', 'CartController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/carts/search', 'CartController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/carts/{id}', 'CartController@delete')->middleware('scope:admin');

//categories
Route::get('/categories', 'CategoryController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/categories/get/{id}', 'CategoryController@get')->middleware('scope:admin,store_manager,cashier');
Route::get('/categories/{category}/products', 'CategoryController@productsByCategory')->middleware('scope:admin,store_manager,cashier');
Route::post('/categories/create', 'CategoryController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/categories/search', 'CategoryController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/categories/{id}', 'CategoryController@delete')->middleware('scope:admin');

//cash-register-logs
Route::get('/cash-register-logs', 'CashRegisterLogsController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-register-logs/get/{id}', 'CashRegisterLogsController@get')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-register-logs/logout', 'CashRegisterLogsController@logout')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-register-logs/retrieve', 'CashRegisterLogsController@retrieve')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-register-logs/{id}/amount', 'CashRegisterLogsController@amount')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-logs/create', 'CashRegisterLogsController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-logs/search', 'CashRegisterLogsController@search')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-logs/open', 'CashRegisterLogsController@open')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-logs/close', 'CashRegisterLogsController@close')->middleware('scope:admin,store_manager,cashier');
Route::delete('/cash-register-logs/{id}', 'CashRegisterLogsController@delete')->middleware('scope:admin');

//receipts
Route::get('/receipts/get/{model}', 'ReceiptController@get')->middleware('scope:admin,store_manager,cashier');
Route::get('/receipts/create/{model}', 'ReceiptController@create')->middleware('scope:admin,store_manager,cashier');

// categories to list in sales
Route::get('/product-listing/categories', "CategoryController@productListingCategories")->middleware('scope:admin,store_manager,cashier');

// delivery times slot
Route::post('/shipping/timeslot', "TimeslotController@getTimeslots")->middleware('scope:admin,store_manager,cashier');

// magento oauth authenticate
Route::get('/magento/authorize', 'Auth\MagentoOAuthController@authorizeMagento');

// e-mail
Route::post('/mail-receipt/{order}', 'MailReceiptController@send')->middleware('scope:admin,store_manager,cashier');

// guest email list
Route::get('/guest-email', 'GuestEmailListController@all')->middleware('scope:admin,store_manager,cashier');
Route::post('/guest-email/create', 'GuestEmailListController@create')->middleware('scope:admin,store_manager,cashier');

// elavon sdk certification
Route::post('/elavon/sdk', 'ElavonSdkPaymentController@index')->middleware('scope:admin,store_manager,cashier');
Route::post('/elavon/sdk/lookup', 'ElavonSdkPaymentController@lookup')->middleware('scope:admin,store_manager,cashier');
Route::get('/elavon/sdk/logs', 'ElavonSdkPaymentController@getLogs')->middleware('scope:admin,store_manager,cashier');
Route::get('/elavon/sdk/logs/{test_case}', 'ElavonSdkPaymentController@getLogs')->middleware('scope:admin,store_manager,cashier');
Route::delete('/elavon/sdk/logs/delete', 'ElavonSdkPaymentController@deleteAll')->middleware('scope:admin,store_manager,cashier');

// elavon api certification
Route::post('/elavon/api', 'ElavonApiPaymentController@index')->middleware('scope:admin,store_manager,cashier');
Route::post('/elavon/api/lookup', 'ElavonApiPaymentController@lookup')->middleware('scope:admin,store_manager,cashier');
Route::get('/elavon/api/logs', 'ElavonApiPaymentController@getLogs')->middleware('scope:admin,store_manager,cashier');
Route::get('/elavon/api/logs/{test_case}', 'ElavonApiPaymentController@getLogs')->middleware('scope:admin,store_manager,cashier');
Route::delete('/elavon/api/logs/delete', 'ElavonApiPaymentController@deleteAll')->middleware('scope:admin,store_manager,cashier');
