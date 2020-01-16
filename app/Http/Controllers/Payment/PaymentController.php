<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Customer;
use App\Http\Controllers\ElavonSdkPaymentController;
use App\Giftcard;
use App\Helper\Price;
use App\Order;
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
            'amount' => 'nullable|required_unless:payment_type,coupon|numeric|min:0.01',
            'order_id' => 'required|exists:orders,id',
            'cash_register_id' => 'required|exists:cash_registers,id',

            // card validation
            'card.number' => 'nullable|required_if:payment_type,card|numeric',
            'card.cvc' => 'nullable|required_if:payment_type,card|digits_between:3,4',
            'card.exp_date' => 'nullable|required_if:payment_type,card|after_or_equal:today',

            // coupon/giftcard validation
            'code' => 'required_if:payment_type,coupon|required_if:payment_type,giftcard',

            'house_account_number' => 'required_if:payment_type,house-account'
        ]);

        $validatedData['created_by'] = auth()->user()->id;

        $newPayment = $validatedData;
        $newPayment['payment_type'] = PaymentType::getFirst('type', $validatedData['payment_type'])->id;
        $newPayment['status'] = 'pending';

        $payment = $this->model::store($newPayment);

        switch ($validatedData['payment_type']) {
            default:
                $payment->status = 'failed';
                $payment->save();

                return response(['errors' => ['Payment method' => 'This payment method does not exist']], 422);
                break;
            case 'cash':
                // nothing to do here, move on
                break;

            case 'card':
                $paymentResponse = (new CreditCardController)->cardPayment(
                    $validatedData['card']['number'],
                    $validatedData['card']['exp_date'],
                    $validatedData['card']['cvc'],
                    $validatedData['card']['card_holder'] ?? '',
                    $validatedData['amount']
                );
                if (array_key_exists('errors', $paymentResponse)) {
                    $payment->status = 'failed';
                    $payment->save();
                    return response($paymentResponse, 500);
                }

                $payment->code = $paymentResponse['id'];

                break;

            case 'coupon':
                $coupon = Coupon::getFirst('code', $validatedData['code']);

                if (empty($coupon)) {
                    $payment->status = 'failed';
                    $payment->save();

                    return response([
                        'errors' => [
                            'Coupon' => ['Coupon does not exist']
                        ]
                    ], 404);
                }

                if (date('Y-m-d H:i:s') > $coupon->to || $coupon->uses === 0) {
                    $payment->status = 'failed';
                    $payment->save();

                    return response([
                        'errors' => [
                            'Coupon' => ['This coupon has expired']
                        ]
                    ], 403);
                } else {
                    if ($coupon->from > date('Y-m-d H:i:s')) {
                        $payment->status = 'failed';
                        $payment->save();

                        return response([
                            'errors' => [
                                'Coupon' => ['Coupon activates at ' . date("m-d-Y", strtotime($coupon->from))]
                            ]
                        ], 403);
                    }
                }

                $order = Order::findOrFail($validatedData['order_id']);

                $payment->amount = $order->subtotal - Price::calculateDiscount(
                    $order->subtotal,
                    $coupon->discount->type,
                    $coupon->discount->amount
                );
                $payment->save();
                $coupon->decrement('uses');
                break;

            case 'giftcard':
                $giftcard = Giftcard::getFirst('code', $validatedData['code']);

                if (!$giftcard->enabled) {
                    $payment->status = 'failed';
                    $payment->save();

                    return response([
                        'errors' => [
                            'Gift card' => ['This gift card is inactive']
                        ]
                    ], 403);
                } else {
                    if ($giftcard->amount < $validatedData['amount']) {
                        $payment->status = 'failed';
                        $payment->save();

                        return response([
                            'errors' => [
                                'Gift card' => ['This gift card has insufficient balance to complete the transaction']
                            ]
                        ], 403);
                    } else {
                        // subtract the payed amount from giftcard
                        $giftcard->decrement('amount', $validatedData['amount']);
                    }
                }
                break;

            case 'pos-terminal':
                $elavonSdkPayment = new ElavonSdkPaymentController();
                $elavonSdkPayment->selected_transaction = 'SALE';
                $elavonSdkPayment->amount = 100 * $validatedData['amount'];
                $elavonSdkPayment->payment_id = $payment->id;

                $paymentResponse = $elavonSdkPayment->posPayment();

                if (array_key_exists('errors', $paymentResponse)) {
                    $payment->status = 'failed';
                    $payment->save();

                    return response($paymentResponse, 422);
                }

                break;
            case 'house-account':
                $customer = Customer::getFirst('house_account_number', $validatedData['house_account_number']);
                if (empty($customer)) {
                    $payment->status = 'failed';
                    $payment->save();
                    return response([
                        'errors' => [
                            'House Account' => ['House account does not exist.']
                        ]
                    ], 403);
                }
                if ($validatedData['amount'] > $customer->house_account_limit) {
                    $payment->status = 'failed';
                    $payment->save();
                    return response([
                        'errors' => [
                            'House Account' => [
                                'House account has insufficient balance.<br>Balance available: $ ' . round(
                                    $customer->house_account_limit,
                                    2
                                )
                            ]
                        ]
                    ], 403);
                }
                $customer->decrement('house_account_limit', $validatedData['amount']);
                $payment->code = $validatedData['house_account_number'];
                break;
        }

        $payment->status = 'approved';
        $payment->save();

        if (!empty($payment)) {
            return response([
                'total' => $payment->order->total,
                'total_paid' => $payment->order->total_paid,
                'payment' => $payment,
            ], 201);
        }
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|numeric'
        ]);

        return $this->searchResult(
            ['order_id'],
            $validatedData['keyword']
        );
    }

    public function delete($id)
    {
        if (!isset($this->model)) {
            return response('Model not found', 404);
        }

        $payment = Payment::findOrFail($id);
        $refund = $payment->replicate();
        $refund->status = 'refunded';
        $refund->created_by = auth()->user()->id;
        $refund->cash_register_id = auth()->user()->open_register->cash_register_id;
        $refund->save();
        $payment->refunded = 1;
        $payment->save();

        return response(['info' => ['Refund' => 'Refund completed successfully!']], 200);
    }
}
