<?php

namespace App\Http\Controllers;

use App\Helper\Price;
use App\Receipt;
use App\Order;


class ReceiptController extends Controller
{
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
            'store' => $order->store,
            'cash_register' => $order->createdBy->open_register->cash_register,
            'moneyFormatter' => Price::newFormatter()
        ]);
    }
}
