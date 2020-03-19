<?php

namespace App\Http\Controllers;

use App\Helper\Price;
use App\Receipt;
use App\Order;


class ReceiptController extends Controller
{
    public function create(Order $model)
    {
        $createdBy = auth()->user();
        $cash_register = $createdBy->open_register->cash_register;
        $payments = [];

        foreach ($model->transaction as $transaction) {
            if ($model->transaction->payment && $transaction['status'] !== 'failed') {
                if ($transaction['payment_type_name'] === 'card' || $transaction['payment_type_name'] === 'pos-terminal') {
                    $payment['payment_type_name'] = 'Credit card';
                } else if ($payment['status'] === 'refunded') {
                    $payment['price']['amount'];
                }

                array_push($payments, $payment);
            }
        }

        $receipt = Receipt::create([
            'order_id' => $model->id,
            'print_count' => 0,
            'email_count' => 0,
            'issued_by_id' => $createdBy->id,
            'cash_register_id' => $cash_register->id
        ]);

        return response($receipt);
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

        return view('receipt')->with([
            'order' => $order,
            'created_by' => $order->createdBy,
            'store' => $order->store,
            'cash_register' => $order->createdBy->open_register->cash_register,
            'moneyFormatter' => Price::newFormatter()
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
