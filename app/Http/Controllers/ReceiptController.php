<?php

namespace App\Http\Controllers;

use App\Receipt;
use App\Order;
use Illuminate\Support\Carbon;


class ReceiptController extends Controller
{
    public function create(Order $model)
    {
        var_dump($model);
        $model = $model->load('user_id');
        $store = $model->store;
        $cash_register = $model->user_id->open_register->cash_register;
        $user_id = $model->user_id;
        $payments = [];

        foreach (json_decode($model['payments'], true) as $payment) {
            if ($payment['payment_type']['type'] === 'card' || $payment['payment_type']['type'] === 'pos-terminal') {
                $payment['payment_type']['name'] = 'Credit card';
            } elseif ($payment['status'] === 'refunded') {
                $payment['amount'] = abs($payment['amount']);
            }
            if (!($payment['status'] === 'failed')) {
                array_push($payments, $payment);
            }
        }
        $content = [
            "customer" => $model->customer,
            "customer_no_tax" => $model->customer ? $model->customer->no_tax : false,
            "trans" => $model->id,
            "store" => ["name" => $store->name, "phone" => $store->phone, "street" => $store->street, "city" => $store->city, "postcode" => $store->postcode],
            "reg" => $cash_register->name,
            "clrk" => $user_id->name,
            "oper" => $user_id->name,
            "date" => $model->created_at->format('m/d/Y'),
            "time" => $model->created_at->format('h:m:s'),
            "items" => $model->items,
            "sales_tax" => $model->total - $model->total_without_tax,
            "total_without_tax" => $model->total_without_tax,
            "subtotal" => $model->subtotal,
            "total_ant" => $model->total,
            "delivery_fees" => $model->shipping_cost,
            'payments' => $payments,
            'balance_remaining' => $model->total - $model->total_paid,
            'total_amt_tendered' =>  $model->total_paid,
            'customer_change' => $model->change,
            'shipping_address' => $model->shipping_address,
            'shipping_type' => $model->shipping_type,
            'shipping_cost' => $model->shipping_cost,
            'delivery_slot' => $model->delivery_slot,
            'notes' => $model->notes,
            'dlvr_on' => Carbon::parse($model->delivery_date)->format('m/d/Y l'),
            'dlvr_to' => $model->shipping_address ? $model->shipping_address->first_name . " " . $model->shipping_address->last_name : null,
            'c_o' => $model->shipping_address ? $model->customer->first_name . " " . $model->customer->last_name : null,
            'address' =>  $model->shipping_address ? $model->shipping_address->street : null,
            'address_2' => $model->shipping_address ? $model->shipping_address->street2 : null,
            'city' =>  $model->shipping_address ? $model->shipping_address->city : null
        ];

        $receipt['order_id'] = $model->id;
        $receipt['print_count'] = 0;
        $receipt['email_count'] = 0;
        $receipt['issued_by'] = $user_id->id;
        $receipt['cash_register_id'] = $cash_register->id;
        $receipt['content'] =  $content;

        $receipt = Receipt::create($receipt);
        return response(['info' => ['Receipt ' . $receipt->id . ' created successfully!']], 201);
    }

    public function printReceipt(Order $model)
    {
        $receipts = $model->load('receipts');
        // $receipt->print_count++;
        // $receipt->update();
        // return view('test_print_receipt')->with($receipt->content);
    }

    public function emailReceipt(Order $model)
    {
        $receipts = $model->load('receipts');
        // $receipt->email_count++;
        // $receipt->save();
        // return view('test_print_receipt')->with($receipt->content);
    }

    public function all()
    {
        return response(Receipt::paginate(), 200);
    }

    public function get(Receipt $model)
    {
        return response($model, 200);
    }
}
