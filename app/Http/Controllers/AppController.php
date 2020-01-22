<?php

namespace App\Http\Controllers;

use App\Order;
use App\Helper\PhpHelper;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index()
    {
        return view('app');
    }

    public function env()
    {
        return response(config('app.env'));
    }

    public function debug()
    {
        return response(config('app.debug'));
    }

    public function receipt(Order $order)
    {
        return view('receipt')->with([
            'order' => $order,
            'created_by' => $order->created_by()->first(),
            'store' => $order->store_id()->first(),
            'cash_register' => $order->created_by()->first()->open_register()->first()->cash_register()->first(),
        ]);
    }
}
