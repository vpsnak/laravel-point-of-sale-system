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

// Route::get('/test/{model}', 'AppController@test');

// mas
// Route::get('/mas/test', 'MasAccountController@test');
Route::get('/mas/env', 'MasAccountController@getEnv')->middleware('scope:admin,store_manager,cashier');
Route::get('/mas/set/{mode}', 'MasAccountController@setEnv')->middleware('scope:admin');

// menu
Route::get('/menu-items', 'MenuItemController@all')->middleware('scope:admin,store_manager,cashier');

// roles
Route::get('/roles', "RoleController@all")->middleware('scope:admin');
Route::post('/roles/set', "RoleController@setRole")->middleware('scope:admin');

// auth
Route::get('/auth/logout', 'UserController@logout')->middleware('scope:admin,store_manager,cashier');
Route::post('/auth/login', 'UserController@login')->middleware('guest');
Route::post('/auth/verify', 'UserController@verifySelfPwd')->middleware('scope:admin,store_manager,cashier');
Route::post('/auth/password', 'UserController@changeSelfPwd')->middleware('scope:admin,store_manager');

// users
Route::get('/users', 'UserController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/users/get/{model}', 'UserController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::post('/users/create', 'UserController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/users/search', 'UserController@search')->middleware('scope:admin,store_manager,cashier');
Route::post('/users/password', 'UserController@changeUserPwd')->middleware('scope:admin');
Route::patch('/users/update', 'UserController@update')->middleware('scope:admin,store_manager');
Route::delete('/users/{model}', 'UserController@delete')->middleware('scope:admin');

// payments
Route::get('/payments', 'PaymentController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/payments/get/{payment}', 'PaymentController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::post('/payments/create', 'PaymentController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/payments/search', 'PaymentController@search')->middleware('scope:admin,store_manager,cashier');
Route::post('payments/unlinked-refund', 'PaymentController@createUnlinkedRefund')->middleware('scope:admin,store_manager');
Route::delete('/payments/{payment}', 'PaymentController@refundPayment')->middleware('scope:admin,store_manager,cashier');

// customers
Route::get('/customers', 'CustomerController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/customers/get/{model}', 'CustomerController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::post('/customers/create', 'CustomerController@create')->middleware('scope:admin,store_manager,cashier');
Route::patch('/customers/update', 'CustomerController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/customers/search', 'CustomerController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/customers/{model}', 'CustomerController@delete')->middleware('scope:admin,store_manager,cashier');

// addresses
Route::get('/addresses', 'AddressController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/addresses/get/{model}', 'AddressController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::post('/addresses/create', 'AddressController@create')->middleware('scope:admin,store_manager,cashier');
Route::patch('/addresses/update', 'AddressController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/addresses/search', 'AddressController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/addresses/{model}', 'AddressController@delete')->middleware('scope:admin,store_manager,cashier');

// orders
Route::get('/orders', 'OrderController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/orders/get/{model}', 'OrderController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::get('/orders/{model}/payment-details', 'OrderAnalysisController@getPaymentDetails'); // @TODO ADD AUTH!!
Route::get('/orders/{model}/statuses', 'OrderStatusController@getOrderStatuses')->middleware('scope:admin,store_manager,cashier');
Route::get('/orders/{model}/mas-status', 'MasOrderController@getOrderDetails'); // @TODO ADD AUTH!!
Route::post('/orders/create', 'OrderController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/orders/update-items', 'OrderController@updateItems')->middleware('scope:admin,store_manager,cashier');
Route::post('/orders/update-options', 'OrderController@updateOptions')->middleware('scope:admin,store_manager,cashier');
Route::post('/orders/search', 'OrderController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/orders/{model}', 'OrderController@rollbackOrder')->middleware('scope:admin,store_manager,cashier');

// statuses
Route::get('/statuses', 'StatusController@all')->middleware('scope:admin,store_manager,cashier');

// stores
Route::get('/stores', 'StoreController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/stores/get/{model}', 'StoreController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::post('/stores/create', 'StoreController@create')->middleware('scope:admin,store_manager,cashier');
Route::patch('/stores/update', 'StoreController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/stores/search', 'StoreController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/stores/{model}', 'StoreController@delete')->middleware('scope:admin,store_manager,cashier');

// taxes
Route::get('/taxes', 'TaxController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/taxes/get/{model}', 'TaxController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::post('/taxes/create', 'TaxController@create')->middleware('scope:admin,store_manager,cashier');
Route::patch('/taxes/update', 'TaxController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/taxes/search', 'TaxController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/taxes/{model}', 'TaxController@delete')->middleware('scope:admin,store_manager,cashier');

// payment-types
Route::get('/payment-types', 'PaymentTypeController@paymentTypes')->middleware('scope:admin,store_manager,cashier');
Route::get('/refund-types', 'PaymentTypeController@refundTypes')->middleware('scope:admin,store_manager');

// cash-registers
Route::get('/cash-registers', 'CashRegisterController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-registers/get/{model}', 'CashRegisterController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::patch('/cash-registers/update', 'CashRegisterController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-registers/create', 'CashRegisterController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-registers/search', 'CashRegisterController@search')->middleware('scope:admin,store_manager,cashier');

// cash-register-reports
Route::get('/cash-register-reports', 'CashRegisterReportController@all')->middleware('scope:admin,store_manager,cashier');;
Route::get('/cash-register-reports/get/{model}', 'CashRegisterReportController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-register-reports/check/get/{model}', "CashRegisterReportController@checkCurrent")->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-reports/create', 'CashRegisterReportController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-reports/search', 'CashRegisterReportController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/cash-register-reports/{model}', 'CashRegisterReportController@delete')->middleware('scope:admin,store_manager,cashier');

// gift-cards
Route::get('/gift-cards', 'GiftcardController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/gift-cards/get/{model}', 'GiftcardController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::post('/gift-cards/create', 'GiftcardController@create')->middleware('scope:admin,store_manager,cashier');
Route::patch('/gift-cards/update', 'GiftcardController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/gift-cards/search', 'GiftcardController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/gift-cards/{model}', 'GiftcardController@delete')->middleware('scope:admin,store_manager,cashier');

// coupons
Route::get('/coupons', 'CouponController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/coupons/get/{model}', 'CouponController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::post('/coupons/create', 'CouponController@create')->middleware('scope:admin,store_manager,cashier');
Route::patch('/coupons/update', 'CouponController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/coupons/search', 'CouponController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/coupons/{model}', 'CouponController@delete')->middleware('scope:admin,store_manager,cashier');

// regions / countries
Route::get('/regions', 'RegionController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/countries', 'CountryController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/countries/{model}/regions', 'CountryController@regions')->middleware('scope:admin,store_manager,cashier');

// store-pickups
Route::get('/store-pickups', 'StorePickupController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/store-pickups/get/{model}', 'StorePickupController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::patch('/store-pickups/update', 'StorePickupController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/store-pickups/create', 'StorePickupController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/store-pickups/search', 'StorePickupController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/store-pickups/{model}', 'StorePickupController@delete')->middleware('scope:admin,store_manager,cashier');

// companies
Route::get('/companies', 'CompanyController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/companies/get/{model}', 'CompanyController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::patch('/companies/update', 'CompanyController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/companies/create', 'CompanyController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/companies/search', 'CompanyController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/companies/{model}', 'CompanyController@delete')->middleware('scope:admin,store_manager,cashier');

// products
Route::get('/products', 'ProductController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/products/get/{model}', 'ProductController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::get('/products/barcode/{model}', 'ProductController@getBarcode')->middleware('scope:admin,store_manager,cashier');
Route::post('/products/create', 'ProductController@create')->middleware('scope:admin,store_manager,cashier');
Route::patch('/products/update', 'ProductController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/products/search', 'ProductController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/products/{model}', 'ProductController@delete')->middleware('scope:admin');

// carts
Route::get('/carts', 'CartController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/carts/get/{model}', 'CartController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::get('/carts/hold', 'CartController@getHold')->middleware('scope:admin,store_manager,cashier');
Route::post('/carts/create', 'CartController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/carts/search', 'CartController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/carts/{model}', 'CartController@delete')->middleware('scope:admin');

// categories
Route::get('/categories', 'CategoryController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/categories/get/{model}', 'CategoryController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::get('/categories/{category}/products', 'CategoryController@productsByCategory')->middleware('scope:admin,store_manager,cashier');
Route::post('/categories/create', 'CategoryController@create')->middleware('scope:admin,store_manager,cashier');
Route::patch('/categories/update', 'CategoryController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/categories/search', 'CategoryController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/categories/{model}', 'CategoryController@delete')->middleware('scope:admin');

// cash-register-logs
Route::get('/cash-register-logs', 'CashRegisterLogsController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-register-logs/get/{model}', 'CashRegisterLogsController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-register-logs/logout', 'CashRegisterLogsController@logout')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-register-logs/retrieve', 'CashRegisterLogsController@retrieve')->middleware('scope:admin,store_manager,cashier');
Route::get('/cash-register-logs/{model}/amount', 'CashRegisterLogsController@amount')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-logs/create', 'CashRegisterLogsController@create')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-logs/search', 'CashRegisterLogsController@search')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-logs/open', 'CashRegisterLogsController@open')->middleware('scope:admin,store_manager,cashier');
Route::post('/cash-register-logs/close', 'CashRegisterLogsController@close')->middleware('scope:admin,store_manager,cashier');
Route::delete('/cash-register-logs/{model}', 'CashRegisterLogsController@delete')->middleware('scope:admin');

//receipts
Route::get('/receipts/get/{model}', 'ReceiptController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::get('/receipts/create/{model}', 'ReceiptController@create')->middleware('scope:admin,store_manager,cashier');

// cards
Route::get('/cards', 'CardController@all')->middleware('scope:admin,store_manager,cashier');
Route::get('/cards/get/{model}', 'CardController@getOne')->middleware('scope:admin,store_manager,cashier');
Route::post('/cards/create', 'CardController@create')->middleware('scope:admin,store_manager,cashier');
Route::patch('/cards/update', 'CardController@update')->middleware('scope:admin,store_manager,cashier');
Route::post('/cards/search', 'CardController@search')->middleware('scope:admin,store_manager,cashier');
Route::delete('/cards/{model}', 'CardController@delete')->middleware('scope:admin,store_manager,cashier');

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
