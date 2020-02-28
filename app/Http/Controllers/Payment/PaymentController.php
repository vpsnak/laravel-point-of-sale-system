<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Customer;
use App\ElavonApiPayment;
use App\Http\Controllers\ElavonSdkPaymentController;
use App\Http\Controllers\OrderController;
use App\Giftcard;
use App\Helper\Price;
use App\Order;
use App\Payment;
use App\PaymentType;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'payment_type' => 'required|exists:payment_types,type',
            'amount' => 'nullable|required_unless:payment_type,coupon|numeric|min:0.01',
            'order_id' => 'required|exists:orders,id',
            'card.number' => 'nullable|required_if:payment_type,card|numeric',
            'card.cvc' => 'nullable|required_if:payment_type,card|digits_between:3,4',
            'card.card_holder' => 'nullable|required_if:payment_type,card|string',
            'card.exp_date' => 'nullable|required_if:payment_type,card|after_or_equal:today',
            'code' => 'required_if:payment_type,coupon|required_if:payment_type,giftcard',
            'house_account_number' => 'required_if:payment_type,house-account'
        ]);
        $validatedData['user_id'] = auth()->user()->id;
        $validatedData['cash_register_id'] = auth()->user()->open_register->cash_register->id;

        $newPayment = $validatedData;
        $newPayment['payment_type'] = PaymentType::whereType($validatedData['payment_type'])->first()->id;
        $newPayment['status'] = 'pending';

        $payment = Payment::create($newPayment);
        $response = [];

        switch ($validatedData['payment_type']) {
            case 'cash':
                break;
            case 'pos-terminal':
                $response = $this->posPay($payment);
                break;
            case 'card':
                $response = $this->apiPay($validatedData, $payment);
                break;
            case 'coupon':
                $response = $this->couponPay($validatedData, $payment);
                break;
            case 'giftcard':
                $response = $this->giftcardPay($validatedData, $payment);
                break;
            case 'house-account':
                $response = $this->houseAccPay($validatedData, $payment);
                break;
            default:
                $response = [
                    'errors' => ['Payment method' => 'This payment method does not exist'],
                    'payment' => $payment
                ];
                break;
        }

        if (is_array($response)) {
            if (array_key_exists('transaction_id', $response)) {
                $payment->code = $response['transaction_id'];
                $payment->save();
            } else if (array_key_exists('id', $response)) {
                $payment->code = $response['id'];
                $payment->save();
            }

            if (array_key_exists('errors', $response)) {
                $payment->status = 'failed';
                $payment->save();
                $response['payment'] = $payment->load(['created_by', 'paymentType']);

                return response($response, 422);
            }
        }

        $payment->status = 'approved';
        $payment->save();
        $payment->load(['created_by', 'paymentType', 'order']);
        $orderController = new OrderController($payment->order);
        $orderStatus = $orderController->updateOrderStatus($payment);
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

    private function posPay(Payment $payment)
    {
        $elavonSdkPayment = new ElavonSdkPaymentController();
        $elavonSdkPayment->selected_transaction = 'SALE';
        $elavonSdkPayment->amount = 100 * $payment->amount;
        $elavonSdkPayment->payment_id = $payment->id;

        $paymentResponse = $elavonSdkPayment->posPayment();

        return $paymentResponse;
    }

    private function apiPay($validatedData, Payment $payment)
    {
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
            'log' => $paymentResponse['response'] ? json_encode($paymentResponse['response']) : '',
            'payment_id' => $payment->id,
        ]);

        return $paymentResponse;
    }

    private function couponPay($validatedData, Payment $payment)
    {
        $coupon = Coupon::where('code', $validatedData['code'])->first();

        if (empty($coupon)) {
            $payment->status = 'failed';
            $payment->save();

            return ['errors' => ['Coupon' => ['Coupon does not exist']]];
        }

        if (date('Y-m-d H:i:s') > $coupon->to || $coupon->uses === 0) {
            return ['errors' => ['Coupon' => ['This coupon has expired']]];
        } else if ($coupon->from > date('Y-m-d H:i:s')) {
            return ['errors' => ['Coupon' => ['Coupon activates at ' . date("m-d-Y", strtotime($coupon->from))]]];
        } else {
            $order = Order::findOrFail($validatedData['order_id']);

            $payment->amount = $order->subtotal - Price::calculateDiscount(
                $order->subtotal,
                $coupon->discount->type,
                $coupon->discount->amount
            );
            $payment->save();
            $coupon->decrement('uses');

            return true;
        }
    }

    private function giftcardPay($validatedData, Payment $payment)
    {

        $giftcard = Giftcard::where('code', $validatedData['code'])->first();

        if (!$giftcard->enabled) {
            return ['errors' => ['Gift card' => ['This gift card is inactive']]];
        } else if ($giftcard->amount < $validatedData['amount']) {
            return ['errors' => ['Gift card' => ['This gift card has insufficient balance to complete the transaction']]];
        } else {
            // subtract the payed amount from giftcard
            return  $giftcard->decrement('amount', $validatedData['amount']);
        }
    }


    private function houseAccPay($validatedData, Payment $payment)
    {
        $customer = Customer::where('house_account_number', $validatedData['house_account_number'])->first();
        if (empty($customer)) {
            return ['errors' => ['House Account' => ['House account does not exist.']]];
        } else if ($validatedData['amount'] > $customer->house_account_limit) {
            return [
                'errors' => [
                    'House Account' => [
                        'House account has insufficient balance.<br>Balance available: $ ' . round($customer->house_account_limit, 2)
                    ]
                ]
            ];
        } else {
            $customer->decrement('house_account_limit', $validatedData['amount']);
            $payment->code = $validatedData['house_account_number'];
        }
    }

    public function posRefund(Payment $payment)
    {
        $elavonSdkPaymentController = new ElavonSdkPaymentController;

        $elavonSdkPaymentController->selected_transaction = 'VOID';
        $elavonSdkPaymentController->originalTransId = $payment->code;
        $paymentResponse = $elavonSdkPaymentController->posPayment();

        if (isset($paymentResponse['errors'])) {
            $elavonSdkPaymentController->selected_transaction = 'LINKED_REFUND';
            $elavonSdkPaymentController->amount = $payment->amount;
            $paymentResponse = $elavonSdkPaymentController->posPayment();
        } else {
            return $paymentResponse;
        }
    }

    public function apiRefund($payment)
    {
        $paymentResponse = (new CreditCardController)->cardRefund($payment->code);

        ElavonApiPayment::create([
            'txn_id' => $paymentResponse['response']['ssl_txn_id'] ?? '',
            'transaction' => $paymentResponse['response']['ssl_transaction_type'] ?? '',
            'card_number' => $paymentResponse['response']['ssl_card_number'] ?? '',
            'status' => $paymentResponse['response']['ssl_result_message'] ?? '',
            'log' => array_key_exists(
                'response',
                $paymentResponse
            ) ? json_encode($paymentResponse['response']) : '',
            'payment_id' => $payment->id,
        ]);

        return $paymentResponse;
    }

    public function houseAccRefund(Payment $payment)
    {
        $customer = Customer::where('house_account_number', $payment->code)->firstOrFail();
        $customer->increment('house_account_limit', $payment->amount);

        return true;
    }

    public function giftcardRefund(Payment $payment)
    {
        $giftcard = Giftcard::whereCode($payment->code)->firstOrFail();
        $giftcard->increment('amount', $payment->amount);

        return true;
    }

    public function couponRefund(Payment $payment)
    {
        $coupon = Coupon::whereCode($payment->code)->firstOrFail();
        $coupon->increment('uses');

        return true;
    }

    public function createUnlinkedRefund(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required|string',
            'amount' => 'required|numeric',
            // giftcard-existing validation
            'existing_gc_code' => 'nullable|required_if:method,giftcard-existing|exists:giftcards,code',
            // giftcard-new validation
            'giftcard.code' => 'nullable|required_if:method,giftcard-new|unique:giftcards,code|string',
            'giftcard.name' => 'nullable|required_if:method,giftcard-new|string|',
            // cc validation
            'card.number' => 'nullable|required_if:method,cc-api',
            'card.holder' => 'nullable|required_if:method,cc-api',
            'card.exp_date' => 'nullable|required_if:method,cc-api',
            'card.cvc' => 'nullable|required_if:method,cc-api'
        ]);

        $order = Order::findOrFail($validatedData['order_id']);
        $refundType = PaymentType::whereType($validatedData['method'])->firstOrFail();
        $user = auth()->user();
        $cash_register_id = $user->open_register->cash_register_id;

        if ($validatedData['amount'] > $order->total_paid) {
            return response(['errors' => [
                ['The amount field cannot exceed the total paid amount of $' . $order->total_paid]
            ]], 422);
        }

        $code = null;
        switch ($validatedData['method']) {
            case 'cc-api':
                break;
            case 'cc-pos':
                break;
            case 'giftcard-existing':
                $code = $validatedData['existing_gc_code'];
                $giftcard = Giftcard::whereCode($validatedData['existing_gc_code'])->firstOrFail();
                $giftcard->amount += $validatedData['amount'];
                $giftcard->save();
                break;
            case 'giftcard-new':
                $code = $validatedData['giftcard']['code'];
                Giftcard::create([
                    'code' => $validatedData['giftcard']['code'],
                    'name' => $validatedData['giftcard']['name'],
                    'amount' => $validatedData['amount'],
                    'enabled' => true,
                ]);
                break;
        }

        $refund = new Payment([
            'payment_type' => $refundType->id,
            'amount' => abs($validatedData['amount']) * -1,
            'code' => $code,
            'status' => 'refunded',
            'refunded' => false,
            'cash_register_id' => $cash_register_id,
            'order_id' => $order->id,
            'user_id' => $user->id
        ]);

        $refund->save();
        $refund = $refund->load(['created_by', 'paymentType', 'order']);

        $orderController = new OrderController($payment->order);
        $orderStatus = $orderController->updateOrderStatus($refund, true);
        $orderStatus['notification'] = [
            'msg' => ['Refund completed successfully!'],
            'type' => 'success'
        ];
        $orderStatus['refund'] = $refund->refresh();

        return response($orderStatus, 500); // 500 status for debug purposes only!
    }

    private function createLinkedRefund(Payment $payment, bool $succeed)
    {
        $user = auth()->user();
        $refund = $payment->replicate();
        $refund->amount = abs($refund->amount) * -1;
        $refund->status = $succeed ? 'refunded' : 'failed';
        $refund->user_id = $user->id;
        $refund->cash_register_id = $user->open_register->cash_register_id;
        $refund->save();

        $payment->refunded = $succeed;
        $payment->save();

        return $refund;
    }

    public function refundPayment(Payment $payment, bool $setOrderStatus = true)
    {
        if ($payment->refunded) {
            return response(['errors' => ['Refund' => 'This payment has already been refunded']], 422);
        }

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
                if ($setOrderStatus) {
                    return response(['errors' => ['Refund' => "Payment method: {$payment->paymentType->type} cannot be refunded"]], 500);
                } else {
                    return ['errors' => ['Refund' => "Payment method: {$payment->paymentType->type} cannot be refunded"]];
                }
        }

        if ($setOrderStatus) {
            if (is_array($result) && array_key_exists('errors', $result)) {
                $refund = $this->createLinkedRefund($payment, false);
                $refund->load(['created_by', 'paymentType', 'order']);

                $orderController = new OrderController($payment->order);
                $orderStatus = $orderController->updateOrderStatus($refund, true);
                $orderStatus['errors'] = $result['errors'];
                $orderStatus['refund'] = $refund->refresh();

                return response($orderStatus, 500);
            } else {
                $refund = $this->createLinkedRefund($payment, true);
                $refund->load(['created_by', 'paymentType', 'order']);

                $orderController = new OrderController($payment->order);
                $orderStatus = $orderController->updateOrderStatus($refund, true);
                $orderStatus['refunded_payment_id'] = $payment->id;
                $orderStatus['notification'] = [
                    'msg' => ['Refund completed successfully!'],
                    'type' => 'success'
                ];
                $orderStatus['refund'] = $refund->refresh();

                return response($orderStatus);
            }
        } else {
            if (is_array($result) && array_key_exists('errors', $result)) {
                return ['errors' => $result['errors']];
            } else {
                $refund = $this->createLinkedRefund($payment, true);
                return $refund;
            }
        }
    }
}
