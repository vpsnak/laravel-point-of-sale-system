<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Customer;
use App\ElavonApiPayment;
use App\Http\Controllers\ElavonSdkPaymentController;
use App\Http\Controllers\OrderController;
use App\Giftcard;
use App\Helper\PhpHelper;
use App\Helper\Price;
use App\Order;
use App\Payment;
use App\PaymentType;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $model = Payment::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'payment_type' => 'required|exists:payment_types,type',
            'amount' => 'nullable|required_unless:payment_type,coupon|numeric|min:0.01',
            'order_id' => 'required|exists:orders,id',

            // card validation
            'card.number' => 'nullable|required_if:payment_type,card|numeric',
            'card.cvc' => 'nullable|required_if:payment_type,card|digits_between:3,4',
            'card.card_holder' => 'nullable|required_if:payment_type,card|string',
            'card.exp_date' => 'nullable|required_if:payment_type,card|after_or_equal:today',

            // coupon/giftcard validation
            'code' => 'required_if:payment_type,coupon|required_if:payment_type,giftcard',

            'house_account_number' => 'required_if:payment_type,house-account'
        ]);

        $validatedData['created_by'] = auth()->user()->id;
        $validatedData['cash_register_id'] = auth()->user()->open_register->cash_register->id;

        $newPayment = $validatedData;
        $newPayment['payment_type'] = PaymentType::getFirst('type', $validatedData['payment_type'])->id;
        $newPayment['status'] = 'pending';

        $payment = $this->model::store($newPayment);

        switch ($validatedData['payment_type']) {
            default:
                $payment->status = 'failed';
                $payment->save();

                return response([
                    'errors' => ['Payment method' => 'This payment method does not exist'],
                    'payment' => $payment
                ], 422);
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

                ElavonApiPayment::create([
                    'txn_id' => $paymentResponse['response']['ssl_txn_id'] ?? '',
                    'transaction' => $paymentResponse['response']['ssl_transaction_type'] ?? '',
                    'card_number' => $paymentResponse['response']['ssl_card_number'] ?? '',
                    'card_holder' => $validatedData['card']['card_holder'],
                    'status' => $paymentResponse['response']['ssl_result_message'] ?? '',
                    'log' => json_encode($paymentResponse['response']),
                    'payment_id' => $payment->id,
                ]);

                if (array_key_exists('errors', $paymentResponse)) {
                    $payment->status = 'failed';
                    $payment->save();

                    $paymentResponse['payment'] = $payment->load(['created_by', 'paymentType']);
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
                        'payment' => $payment->load(['created_by', 'paymentType']),
                        'errors' => [
                            'Coupon' => ['Coupon does not exist']
                        ]
                    ], 404);
                }

                if (date('Y-m-d H:i:s') > $coupon->to || $coupon->uses === 0) {
                    $payment->status = 'failed';
                    $payment->save();

                    return response([
                        'payment' => $payment->load(['created_by', 'paymentType']),
                        'errors' => [
                            'Coupon' => ['This coupon has expired']
                        ]
                    ], 403);
                } else {
                    if ($coupon->from > date('Y-m-d H:i:s')) {
                        $payment->status = 'failed';
                        $payment->save();

                        return response([
                            'payment' => $payment->load(['created_by', 'paymentType']),
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
                        'payment' => $payment->load(['created_by', 'paymentType']),
                        'errors' => [
                            'Gift card' => ['This gift card is inactive']
                        ]
                    ], 403);
                } else {
                    if ($giftcard->amount < $validatedData['amount']) {
                        $payment->status = 'failed';
                        $payment->save();

                        return response([
                            'payment' => $payment->load(['created_by', 'paymentType']),
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

                    $paymentResponse['payment'] = $payment->load(['created_by', 'paymentType']);
                    return response($paymentResponse, 422);
                }

                $payment->code = $paymentResponse['transaction_id'];

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
        $payment->load(['created_by', 'paymentType', 'order']);

        $orderStatus = OrderController::updateOrderStatus($payment);
        $orderStatus['payment'] = $payment;

        return response($orderStatus, 201);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|exists:orders,id'
        ]);

        return response(Payment::where('order_id', $validatedData['keyword'])->get());
    }

    private function posRefund(Payment $payment)
    {
        $elavonSdkPaymentController = new ElavonSdkPaymentController;

        $elavonSdkPaymentController->selected_transaction = 'VOID';
        $elavonSdkPaymentController->originalTransId = $payment->code;
        $paymentResponse = $elavonSdkPaymentController->posPayment();

        if (isset($paymentResponse['errors'])) {
            $elavonSdkPaymentController->selected_transaction = 'LINKED_REFUND';
            $elavonSdkPaymentController->amount = $payment->amount;
            $paymentResponse = $elavonSdkPaymentController->posPayment();

            if (isset($paymentResponse['errors'])) {
                return false;
            }
        }
        return true;
    }

    private function apiRefund($payment)
    {
        $paymentResponse = (new CreditCardController)->cardRefund($payment->code);

        ElavonApiPayment::create([
            'txn_id' => $paymentResponse['response']['ssl_txn_id'] ?? '',
            'transaction' => $paymentResponse['response']['ssl_transaction_type'] ?? '',
            'card_number' => $paymentResponse['response']['ssl_card_number'] ?? '',
            'status' => $paymentResponse['response']['ssl_result_message'] ?? '',
            'log' => json_encode($paymentResponse['response']),
            'payment_id' => $payment->id,
        ]);

        if (isset($paymentResponse['errors'])) {
            return false;
        }

        return true;
    }

    private function houseAccRefund(Payment $payment)
    {
        $customer = Customer::where('house_account_number', $payment->code)->firstOrFail();
        $customer->increment('house_account_limit', $payment->amount);

        return true;
    }

    private function giftcardRefund(Payment $payment)
    {
        $giftcard = Giftcard::whereCode($payment->code)->firstOrFail();
        $giftcard->increment('amount', $payment->amount);

        return true;
    }

    private function couponRefund(Payment $payment)
    {
        $coupon = Coupon::whereCode($payment->code)->firstOrFail();
        $coupon->increment('uses');

        return true;
    }

    private function createRefund(Payment $payment, bool $succeed)
    {
        $refund = $payment->replicate();
        $refund->amount = abs($refund->amount) * -1;
        $refund->status = $succeed ? 'refunded' : 'failed';
        $refund->created_by = auth()->user()->id;
        $refund->cash_register_id = auth()->user()->open_register->cash_register_id;
        $refund->save();

        $payment->refunded = $succeed;
        $payment->save();

        return $refund;
    }

    public function delete(Payment $payment)
    {
        switch ($payment->paymentType->type) {
            case 'pos-terminal':
                $result = $this->posRefund($payment);
                break;
            case 'card':
                $result = $this->apiRefund($payment);
                break;
            case 'coupon':
                $result = $this->couponRefund($payment);
                break;
            case 'giftcard':
                $result = $this->giftcardRefund($payment);
                break;
            case 'house-account':
                $result = $this->houseAccRefund($payment);
                break;
            case 'cash':
                $result = true;
                break;
            default:
                return response(['errors' => ['Refund' => 'This payment method cannot be refunded']], 500);
        }

        $refund = $this->createRefund($payment, $result);
        $refund->load(['created_by', 'paymentType', 'order']);

        $orderStatus = OrderController::updateOrderStatus($refund, true);
        $orderStatus['refund'] = $refund->refresh();
        $orderStatus['info'] = ['Refund' => 'Refund completed successfully!'];
        $orderStatus['refunded_payment_id'] = $payment->id;

        return response($orderStatus, 200);
    }
}
