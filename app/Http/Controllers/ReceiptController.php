<?php

namespace App\Http\Controllers;

use App\Receipt;
use App\Order;
use App\User;
use App\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;


class ReceiptController extends Controller
{
    protected $model = Receipt::class;

    public function create(Order $model)
    {
        $store = $model->store;
        $cash_register = $model->created_by()->first()->open_register()->first()->cash_register()->first();
        $created_by = $model->created_by()->first();
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
            "clrk" => $created_by->name,
            "oper" => $created_by->name,
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
        return view('test_email_receipt')->with($content);
    }

    public function all()
    {
        return response($this->model::paginate(), 200);
    }

    public function get($id)
    {
        return response($this->model::find($id), 200);
    }
}
