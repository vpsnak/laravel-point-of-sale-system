<?php

return [
    'production_mode' => env('MAS_MODE', false),

    'endpoints' => [
        'test' => env('MAS_ENDPOINT_TEST'),
        'production' => env('MAS_ENDPOINT_PRODUCTION'),
    ],

    'credentials' => [
        'direct_id' => env('MAS_DIRECT_ID'),
        'username' => env('MAS_USERNAME'),
        'password' => env('MAS_PASSWORD')
    ],
];
