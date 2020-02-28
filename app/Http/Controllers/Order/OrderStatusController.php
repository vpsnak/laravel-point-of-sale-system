<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public $timestamps = ["processed_on"];

    protected $casts = [
        'processed_on' => 'datetime:m-d-Y H:i:s',
    ];
}
