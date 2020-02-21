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
        // $a = "{$id->billingAddress['first_name']} {$id->billingAddress['last_name']}";
        // $r = MasOrderController::submitToMas($id);
        // return response(['mas response' => $r, 'shipping_address' => $id->shipping_address, 'billing_address' => $id->billing_address]);

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

        // CHECK getDefaultBilling getDefaultShipping setIsDefaultBillingAttribute setIsDefaultShippingAttribute
        // $customer = Customer::getOne(1);
        // $billingAddress = $customer->getDefaultBilling();
        // $shippingAddress = $customer->getDefaultShipping();
        // $customer_addresses = $customer->addresses;
        // $customer_address_first = $customer->addresses->get(0);
        // $customer_address_second = $customer->addresses->get(1);
        // $customer_address_first->setIsDefaultBillingAttribute(1);
        // $customer_address_second->setIsDefaultShippingAttribute(1);
        // return response($customer_addresses, 201);
    }

    public function index()
    {
        return view('app');
    }

    public function receipt(Order $order)
    {
        $order = $order->load(['created_by', 'store']);
        return view('receipt')->with([
            'order' => $order,
            'created_by' => $order->created_by,
            'store' => $order->store,
            'cash_register' => $order->created_by->open_register->cash_register,
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
