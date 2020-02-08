<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MasOrderController;
use App\Order;
use App\CashRegisterReport;
use App\Product;
use App\Helper\PhpHelper;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function test(Order $id)
    {
        // $a = "{$id->billingAddress['first_name']} {$id->billingAddress['last_name']}";
        $r = MasOrderController::submitToMas($id);
        return response(['mas response' => $r, 'shipping_address' => $id->shipping_address, 'billing_address' => $id->billing_address]);

        // if () {
        //     $address_region = [];

        //     return response([$customer, $billingAddress]);

        // $onlinePartner = [
        //     'SoldName' => "{$customer->name} {$customer->name}",
        //     'SoldAddress' => $billingAddress->street,
        //     'SoldAddress2' => $billingAddress->street2,
        //     'SoldCity' => $billingAddress->city,
        //     'SoldState' => $billingAddress->,
        //     'SoldZip' => $billingAddress->postcode,
        //     'SoldPhoneHome' => $billingAddress->phone,
        //     'SoldPhoneWork' => $billingAddress->,
        //     'SoldPhoneMobile' => $billingAddress->,
        //     'SoldEmail' => $customer->email,
        //     // 'SalesRep' => $customer->,
        //     // 'ShippingVia' => $customer->,
        //     // 'ShippingPriority' => $customer->,
        //     // 'AuthCode' => $customer->,
        //     // 'SoldAttention' => $customer->,
        //     // 'CustomerId' => $customer->,
        // ];
        // return $onlinePartner;
        // }
    }

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
