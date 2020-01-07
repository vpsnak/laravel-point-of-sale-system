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



$cashier = 'cashier';
$store_manager = 'store_manager';
$admin = 'admin';
$allRoles = "$admin,$store_manager,$cashier";

// roles
Route::get('/roles', "RoleController@all")->middleware('scope:admin');
Route::post('/roles/set', "RoleController@setRole")->middleware('scope:admin');

// custom end points (cancer free!)
Route::get('/cash-register-reports/check/{cashRegister}', "CashRegisterReportController@checkCurrent")->middleware("scope:{$allRoles}");

$routeAll = [
    'url' => '/',
    'action' => 'all',
];

$routeGet = [
    'url' => '/{id}',
    'action' => 'get',
];

$routeCreate = [
    'url' => '/create',
    'action' => 'create',
    'middleware' => "scope:$allRoles"
];
$routeSearch = [
    'url' => '/search',
    'action' => 'search',
    'middleware' => "scope:$allRoles"
];
$routeDelete = [
    'url' => '/{id}',
    'action' => 'delete',
    'middleware' => "scope:$admin"
];

$baseEndpoints = [
    'get' => [
        $routeAll,
        $routeGet
    ],
    'post' => [
        $routeCreate,
        $routeSearch
    ],
    'delete' => [
        $routeDelete
    ]
];

$allRoutes = [
    'auth' => [
        'controller' => 'UserController',
        'endpoints' => [
            'get' => [
                [
                    'url' => '/logout',
                    'action' => 'logout',
                    'middleware' => "scope:$allRoles"
                ]
            ],
            'post' => [
                [
                    'url' => '/login',
                    'action' => 'login',
                    'middleware' => "guest"
                ],
                [
                    'url' => '/verify',
                    'action' => 'verifySelfPwd',
                    'middleware' => "scope:$allRoles"
                ],
                [
                    'url' => '/password',
                    'action' => 'changeSelfPwd',
                    'middleware' => "scope:$admin,$store_manager"
                ],
            ]
        ]
    ],
    'users' => [
        'controller' => 'UserController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                [
                    'url' => '/password',
                    'action' => 'changeUserPwd',
                    'middleware' => "scope:$admin"
                ],
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'customers' => [
        'controller' => 'CustomerController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'addresses' => [
        'controller' => 'AddressController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'products' => [
        'controller' => 'ProductController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet,
                [
                    'url' => '/barcode/{id}',
                    'action' => 'getBarcode',
                    'middleware' => "scope:$allRoles"
                ]
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'carts' => [
        'controller' => 'CartController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet,
                [
                    'url' => '/hold',
                    'action' => 'getHold',
                    'middleware' => "scope:$allRoles"
                ]
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'orders' => [
        'controller' => 'OrderController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'categories' => [
        'controller' => 'CategoryController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet,
                [
                    'url' => '/{category}/products',
                    'action' => 'productsByCategory',
                    'middleware' => "scope:$allRoles"
                ]
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'stores' => [
        'controller' => 'StoreController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'taxes' => [
        'controller' => 'TaxController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'payments' => [
        'controller' => 'PaymentController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'payment-types' => [
        'controller' => 'PaymentTypeController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'cash-registers' => [
        'controller' => 'CashRegisterController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'cash-register-logs' => [
        'controller' => 'CashRegisterLogsController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch,
                [
                    'url' => '/open',
                    'action' => 'open',
                    'middleware' => "scope:$allRoles"
                ],
                [
                    'url' => '/close',
                    'action' => 'close',
                    'middleware' => "scope:$allRoles"
                ],
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'cash-register-reports' => [
        'controller' => 'CashRegisterReportController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'gift-cards' => [
        'controller' => 'GiftcardController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'coupons' => [
        'controller' => 'CouponController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'regions' => [
        'controller' => 'RegionController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'countries' => [
        'controller' => 'CountryController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ],
    'store-pickups' => [
        'controller' => 'StorePickupController',
        'endpoints' => [
            'get' => [
                $routeAll,
                $routeGet
            ],
            'post' => [
                $routeCreate,
                $routeSearch
            ],
            'delete' => [
                $routeDelete
            ]
        ]
    ]
];

foreach ($allRoutes as $routeBase => $route) {
    $controller = $route['controller'];
    foreach ($route['endpoints'] as $method => $endpoints) {
        foreach ($endpoints as $endpoint) {
            $url = $endpoint['url'];
            $action = $endpoint['action'];
            switch ($method) {
                case 'get':
                    $createdRoute = $createdRoute = Route::get("/$routeBase$url", "$controller@$action");
                    break;
                case 'post':
                    $createdRoute = Route::post("/$routeBase$url", "$controller@$action");
                    break;
                case 'put':
                    $createdRoute = Route::put("/$routeBase$url", "$controller@$action");
                    break;
                case 'delete':
                    $createdRoute = Route::delete("/$routeBase$url", "$controller@$action");
                    break;
            }
            if (!empty($createdRoute) && !empty($endpoint['middleware'])) {
                $createdRoute->middleware($endpoint['middleware']);
            }
        }
    }
}

// categories to list in sales
Route::get('/product-listing/categories', "CategoryController@productListingCategories")->middleware("scope:$allRoles");

// delivery times slot
Route::post('/shipping/timeslot', "TimeslotController@getTimeslots")->middleware("scope:$allRoles");

// magento oauth authenticate
Route::get('/magento/authorize', 'Auth\MagentoOAuthController@authorizeMagento')->middleware("scope:$admin");

// e-mail
Route::post('/mail-receipt/{order}', 'MailReceiptController@send')->middleware("scope:$allRoles");

// guest email list
Route::get('/guest-email', 'GuestEmailListController@all')->middleware("scope:$allRoles");
Route::post('/guest-email/create', 'GuestEmailListController@create')->middleware("scope:$allRoles");

// elavon sdk certification
Route::post('/elavon/sdk', 'ElavonSdkPaymentController@index')->middleware("scope:$allRoles");
Route::post('/elavon/sdk/lookup', 'ElavonSdkPaymentController@lookup')->middleware("scope:$allRoles");
Route::get('/elavon/sdk/logs', 'ElavonSdkPaymentController@getLogs')->middleware("scope:$allRoles");
Route::get('/elavon/sdk/logs/{test_case}', 'ElavonSdkPaymentController@getLogs')->middleware("scope:$allRoles");
Route::delete('/elavon/sdk/logs/delete', 'ElavonSdkPaymentController@deleteAll')->middleware("scope:$allRoles");

// elavon api certification
Route::post('/elavon/api', 'ElavonApiPaymentController@index')->middleware("scope:$allRoles");
Route::post('/elavon/api/lookup', 'ElavonApiPaymentController@lookup')->middleware("scope:$allRoles");
Route::get('/elavon/api/logs', 'ElavonApiPaymentController@getLogs')->middleware("scope:$allRoles");
Route::get('/elavon/api/logs/{test_case}', 'ElavonApiPaymentController@getLogs')->middleware("scope:$allRoles");
Route::delete('/elavon/api/logs/delete', 'ElavonApiPaymentController@deleteAll')->middleware("scope:$allRoles");
