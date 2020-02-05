<?php

namespace App\Http\Controllers;

use App\Order;
use App\CashRegisterReport;
use App\Product;
use App\Helper\PhpHelper;
use Illuminate\Http\Request;
use Storage;

class AppController extends Controller
{
    public function index()
    {
        return view('app');
    }

    public function receipt(Order $order)
    {
        return view('receipt')->with([
            'order' => $order,
            'created_by' => $order->created_by()->first(),
            'store' => $order->store()->first(),
            'cash_register' => $order->created_by()->first()->open_register()->first()->cash_register()->first(),
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
