<?php

namespace App\Http\Controllers;

use App\Payment;
use App\PaymentType;
use Illuminate\Http\Request;

class PaymentController extends BaseController
{
    protected $model = Payment::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'payment_type' => 'required|exists:payment_types,type',
            'amount' => 'required|numeric',
            'order_id' => 'required|exists:orders,id',
            'cash_register_id' => 'required|exists:cash_registers,id',
            'created_by' => 'required|exists:users,id',
        ]);

        $stored_payment_type = PaymentType::getFirst('type', $validatedData['payment_type']);
        if (empty($stored_payment_type)) {
            response([
                'error' => 'invalid_payment_type',
                'message' => 'The requested payment method is invalid'
            ], 400);
        }
        $validatedData['payment_type'] = $stored_payment_type->id;
        $payment = $this->model::store($validatedData);
        if (!empty($payment)) {

            return response([
                'total' => $payment->order->total,
                'total_paid' => $payment->order->total_paid,
                'payment' => $payment
            ], 201);
        }
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|numeric',
            'per_page' => 'nullable|numeric',
            'page' => 'nullable|numeric',
        ]);

        return $this->searchResult(
            ['order_id'],
            $validatedData['keyword'],
            array_key_exists('per_page', $validatedData) ? $validatedData['per_page'] : 0,
            array_key_exists('page', $validatedData) ? $validatedData['page'] : 0
        );
    }
}
