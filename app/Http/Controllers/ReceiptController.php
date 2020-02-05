<?php

namespace App\Http\Controllers;

use App\Receipt;
use App\Order;
use App\User;
use App\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class ReceiptController extends Controller
{
    protected $model = Receipt::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required'
        ]);

        $order = Order::findOrFail($validatedData['order_id']);
        $store = $order->store;
        $cash_register = $order->created_by()->first()->open_register()->first()->cash_register()->first();
        $created_by = $order->created_by()->first();
        $payments = [];

        foreach (json_decode($order['payments'], true) as $payment) {
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
            "customer" => $order->customer,
            "customer_no_tax" => $order->customer ? $order->customer->no_tax : false,
            "trans" => $order->id,
            "store" => ["name" => $store->name, "phone" => $store->phone, "street" => $store->street, "city" => $store->city, "postcode" => $store->postcode],
            "reg" => $cash_register->name,
            "clrk" => $created_by->name,
            "oper" => $created_by->name,
            "date" => $order->created_at->format('m/d/Y'),
            "time" => $order->created_at->format('h:m:s'),
            "items" => $order->items,
            "sales_tax" => $order->total - $order->total_without_tax,
            "total_without_tax" => $order->total_without_tax,
            "subtotal" => $order->subtotal,
            "total_ant" => $order->total,
            "delivery_fees" => $order->shipping_cost,
            'payments' => $payments,
            'balance_remaining' => $order->total - $order->total_paid,
            'total_amt_tendered' =>  $order->total_paid,
            'customer_change' => $order->change,
            'shipping_address' => $order->shipping_address,
            'shipping_type' => $order->shipping_type,
            'shipping_cost' => $order->shipping_cost,
            'delivery_slot' => $order->delivery_slot,
            'notes' => $order->notes,
            'dlvr_on' => Carbon::parse($order->delivery_date)->format('m/d/Y l'),
            'dlvr_to' => $order->shipping_address ? $order->shipping_address->first_name . " " . $order->shipping_address->last_name : null,
            'c_o' => $order->shipping_address ? $order->customer->first_name . " " . $order->customer->last_name : null,
            'address' =>  $order->shipping_address ? $order->shipping_address->street : null,
            'address_2' => $order->shipping_address ? $order->shipping_address->street2 : null,
            'city' =>  $order->shipping_address ? $order->shipping_address->city : null
        ];

        $receipt['order_id'] = $order->id;
        $receipt['print_count'] = 0;
        $receipt['email_count'] = 0;
        $receipt['issued_by'] = $created_by->id;
        $receipt['cash_register_id'] = $cash_register->id;
        $receipt['content'] =  $content;

        $receipt = Receipt::create($receipt);
        return response(['info' => ['Receipt ' . $receipt->id . ' created successfully!']], 201);
    }

    public function printReceipt(Order $order)
    {
        // $receipt = $order->receipt;
        // $receipt->print_count++;
        // $receipt->update();
        // return view('test_print_receipt')->with($receipt->content);
    }

    public function emailReceipt(Order $order)
    {
        // $receipt = $order->receipt;
        // $receipt->email_count++;
        // $receipt->save();
        // return view('test_print_receipt')->with($receipt->content);
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
