<?php

namespace App\Http\Controllers;

use App\Receipt;
use App\Order;
use Illuminate\Support\Carbon;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;


class ReceiptController extends Controller
{
    public function create(Order $model)
    {
        $store = $model->store;
        $createdBy = auth()->user();
        $cash_register = $createdBy->open_register->cash_register;
        $payments = [];

        foreach (json_decode($model['payments'], true) as $payment) {
            if ($payment['payment_type']['type'] === 'card' || $payment['payment_type']['type'] === 'pos-terminal') {
                $payment['payment_type']['name'] = 'Credit card';
            } else if ($payment['status'] === 'refunded') {
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
            "clrk" => $createdBy->name,
            "oper" => $createdBy->name,
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
            'shipping_address' => $model->delivery,
            'shipping_type' => $model->method,
            'shipping_cost' => $model->shipping_cost,
            'delivery_slot' => $model->delivery_slot,
            'notes' => $model->notes,
            'dlvr_on' => $model->delivery ? Carbon::parse($model->delivery['date'])->format('m/d/Y l') : null,
            'dlvr_to' => $model->delivery ? $model->delivery['address']['first_name'] . " " . $model->delivery['address']['last_name'] : null,
            'c_o' => $model->delivery ? $model->customer->first_name . " " . $model->customer->last_name : null,
            'address' =>  $model->delivery ? $model->delivery['address']['street'] : null,
            'address_2' => $model->delivery ? $model->delivery['address']['street2'] : null,
            'city' =>  $model->delivery ? $model->delivery['address']['city'] : null
        ];

        $receipt['order_id'] = $model->id;
        $receipt['print_count'] = 0;
        $receipt['email_count'] = 0;
        $receipt['issued_by_id'] = $createdBy->id;
        $receipt['cash_register_id'] = $cash_register->id;
        $receipt['content'] = $content;

        $receipt = Receipt::create($receipt);

        return response(['data' => $receipt], 201);
    }

    public function printReceipt(Order $model)
    {
        $receipts = $model->load('receipts');
        // $receipt->print_count++;
        // $receipt->update();
        // return view('test_print_receipt')->with($receipts->content);
    }

    public function emailReceipt(Order $model)
    {
        $receipts = $model->load('receipts');
        // $receipt->email_count++;
        // $receipt->save();
        // return view('test_print_receipt')->with($receipt->content);
    }

    public function receipt(Order $order)
    {
        $order = $order->load(['createdBy', 'store']);

        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);

        return view('receipt')->with([
            'order' => $order,
            'created_by' => $order->createdBy,
            'store' => $order->store,
            'cash_register' => $order->createdBy->open_register->cash_register,
            'moneyFormatter' => $moneyFormatter
        ]);
    }

    public function all()
    {
        return response(Receipt::paginate());
    }

    public function getOne(Receipt $model)
    {
        return response($model);
    }
}
