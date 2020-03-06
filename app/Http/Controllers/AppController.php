<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MasOrderController;
use App\Order;
use App\CashRegisterReport;
use App\Product;
use App\Customer;
use App\Helper\PhpHelper;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function test()
    {
    }

    public function index()
    {
        return view('app');
    }

    public function receipt(Order $order)
    {
        $order = $order->load(['createdBy', 'store']);
        return view('receipt')->with([
            'order' => $order,
            'created_by' => $order->createdBy,
            'store' => $order->store,
            'cash_register' => $order->createdBy->open_register->cash_register,
        ]);
    }

    public function report(CashRegisterReport $report)
    {
        return view('report')->with([
            'report' => $report
        ]);
    }

    public function productBarcode(Product $product)
    {
        return view('product_barcode')->with([
            'product' => $product
        ]);
    }
}
