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
    private $secure = true;

    public function __construct()
    {
        $this->secure = config('app.env') === 'local' ? false : true;
    }

    public function index()
    {
        return view('app');
    }

    public function config()
    {
        return response([
            'name' => config('app.name'),
            'env' => config('app.env'),
            'debug' => config('app.debug'),
            'receiptImg' => Storage::disk('public')->url('img/plantshed_receipt.png')
        ]);
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
