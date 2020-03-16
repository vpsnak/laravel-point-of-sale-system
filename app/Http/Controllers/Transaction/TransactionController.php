<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Customer;
use App\ElavonApiTransaction;
use App\Http\Controllers\ElavonSdkTransactionController;
use App\Http\Controllers\OrderStatusController;
use App\Giftcard;
use App\Helper\Price;
use App\Jobs\ProcessOrder;
use App\Order;
use App\Payment;
use App\Transaction;
use App\PaymentType;
use App\Refund;
use Illuminate\Http\Request;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;

class TransactionController extends Controller
{
    private $transactionData;
    private $paymentData;

    public function createPayment(Request $request)
    {
        $paymentType =
            $request->validate([
                'payment_type' => 'required|exists:payment_types,type'
            ])['payment_type'];

        $this->transactionData =
            $request->validate([
                'price' => 'nullable|required_unless:payment_type,coupon|array',
                'order_id' => 'required|exists:orders,id',
                'card.number' => 'nullable|required_if:payment_type,card|numeric',
                'card.cvc' => 'nullable|required_if:payment_type,card|digits_between:3,4',
                'card.card_holder' => 'nullable|required_if:payment_type,card|string',
                'card.exp_date' => 'nullable|required_if:payment_type,card|after_or_equal:today',
                'code' => 'required_if:payment_type,coupon|required_if:payment_type,giftcard',
                'house_account_number' => 'required_if:payment_type,house-account'
            ]);

        $this->paymentData = $request->validate([
            'payment_type_id' => 'required|exists:payment_types,id'
        ]);

        $creditCardData = $request->validate([
            'card.number' => 'nullable|required_if:payment_type,card|numeric',
            'card.cvc' => 'nullable|required_if:payment_type,card|digits_between:3,4',
            'card.card_holder' => 'nullable|required_if:payment_type,card|string',
            'card.exp_date' => 'nullable|required_if:payment_type,card|after_or_equal:today'
        ]);

        $this->transactionData['created_by_id'] = auth()->user()->id;
        $this->transactionData['cash_register_id'] = auth()->user()->open_register->cash_register->id;
        $this->transactionData['status'] = 'pending';
        $this->transactionData = Transaction::create($this->transactionData);

        $this->paymentData['transaction_id'] = $this->transactionData->id;

        $payment = Payment::create($this->paymentData);
        $this->transactionData->payment_id = $payment->id;

        $response = [];
        $order = Order::findOrFail($this->transactionData['order_id']);

        switch ($paymentType) {
            case 'cash':
                $change_price = $this->transactionData->price->subtract($order->remaining_price);
                if ($change_price->isPositive()) {
                    $payment->change_price = $change_price;
                    $payment->save();
                }
                break;
            case 'pos-terminal':
                $response = $this->posPay($this->transactionData);
                break;
            case 'card':
                $creditCardData = $creditCardData['card'];
                $response = $this->apiPay($creditCardData);
                break;
            case 'coupon':
                $response = $this->couponPay($this->transactionData);
                break;
            case 'giftcard':
                $response = $this->giftcardPay($this->transactionData);
                break;
            case 'house-account':
                $response = $this->houseAccPay($this->transactionData);
                break;
        }



        if (is_array($response)) {
            if (array_key_exists('transaction_id', $response)) {
                $this->transactionData->code = $response['transaction_id'];
                $this->transactionData->save();
            } else if (array_key_exists('id', $response)) {
                $this->transactionData->code = $response['id'];
                $this->transactionData->save();
            }

            if (array_key_exists('errors', $response)) {
                $this->transactionData->status = 'failed';
                $this->transactionData->save();
                $response['transaction'] = $this->transactionData;

                return response()->json($response, 422, [], JSON_NUMERIC_CHECK);
            }
        }

        $this->transactionData->status = 'approved';
        $this->transactionData->save();
        $orderStatusController = new OrderStatusController($order->refresh());
        $response = $orderStatusController->updateOrderStatus();
        ProcessOrder::dispatchNow($order);
        $response['transaction'] = $this->transactionData;
        $response['notification'] = [
            'msg' => 'Payment received!',
            'type' => 'success'
        ];

        return response()->json($response, 201, [], JSON_NUMERIC_CHECK);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|exists:orders,id'
        ]);

        return response(Transaction::where('order_id', $validatedData['keyword'])->get());
    }

    private function posPay(Transaction $transaction)
    {
        $elavonSdkPayment = new ElavonSdkTransactionController();
        $elavonSdkPayment->selected_transaction = 'SALE';
        $elavonSdkPayment->amount = $transaction->price->getAmount();
        $elavonSdkPayment->transaction_id = $transaction->id;

        return $elavonSdkPayment->posPayment();
    }

    private function apiPay(array $creditCardData)
    {
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);
        $price = $this->transactionData->price;

        $paymentResponse = (new CreditCardController)->creditCardAction(
            'ccsale',
            $creditCardData['number'],
            $creditCardData['exp_date'],
            $creditCardData['cvc'],
            $creditCardData['card_holder'],
            $moneyFormatter->format($price)
        );

        ElavonApiTransaction::create([
            'txn_id' => $paymentResponse['response']['ssl_txn_id'] ?? '',
            'transaction' => $paymentResponse['response']['ssl_transaction_type'] ?? '',
            'card_number' => $paymentResponse['response']['ssl_card_number'] ?? '',
            'card_holder' => $creditCardData['card_holder'],
            'status' => $paymentResponse['response']['ssl_result_message'] ?? '',
            'log' => $paymentResponse['response'] ? json_encode($paymentResponse['response']) : '',
            'transaction_id' => $this->transactionData->id,
        ]);

        return $paymentResponse;
    }

    private function couponPay(Transaction $transaction)
    {
        $coupon = Coupon::where('code', $transaction->code)->first();

        if (empty($coupon)) {
            $transaction->status = 'failed';
            $transaction->save();

            return ['errors' => ['Coupon does not exist']];
        }

        if (date('Y-m-d H:i:s') > $coupon->to || $coupon->uses === 0) {
            return ['errors' => ['This coupon has expired']];
        } else if ($coupon->from > date('Y-m-d H:i:s')) {
            return ['errors' => ['Coupon activates at ' . date("m-d-Y", strtotime($coupon->from))]];
        } else {
            $order = Order::findOrFail($transaction['order_id']);

            $transaction->price =
                $order
                ->subtotal
                ->subtract(Price::calculateDiscount($order->subtotal, $coupon->discount));
            $transaction->save();
            $coupon->decrement('uses');

            return true;
        }
    }

    private function giftcardPay(Transaction $transaction)
    {

        $giftcard = Giftcard::where('code', $transaction['code'])->first();

        if (!$giftcard->is_enabled) {
            return ['errors' => ['Gift card' => ['This gift card is inactive']]];
        } else if ($giftcard->amount < $validatedData['amount']) {
            return ['errors' => ['Gift card' => ['This gift card has insufficient balance to complete the transaction']]];
        } else {
            // subtract the payed amount from giftcard
            return  $giftcard->decrement('amount', $validatedData['amount']);
        }
    }


    private function houseAccPay($validatedData, Transaction $payment)
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

    public function posRefund(Transaction $payment)
    {
        $elavonSdkPaymentController = new ElavonSdkTransactionController;

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

    public function apiRefund(Transaction $transaction)
    {
        $paymentResponse = (new CreditCardController)->cardRefund($transaction->code);
        ElavonApiTransaction::create([
            'txn_id' => $paymentResponse['response']['ssl_txn_id'] ?? '',
            'transaction' => $paymentResponse['response']['ssl_transaction_type'] ?? '',
            'card_number' => $paymentResponse['response']['ssl_card_number'] ?? '',
            'status' => $paymentResponse['response']['ssl_result_message'] ?? '',
            'transaction_id' => $transaction->id,
            'log' => $paymentResponse['response'] ?? null
        ]);

        return $paymentResponse;
    }

    public function houseAccRefund(Transaction $transaction)
    {
        $customer = Customer::where('house_account_number', $transaction->code)->firstOrFail();
        $customer->increment('house_account_limit', $transaction->amount);

        return true;
    }

    public function giftcardRefund(Transaction $payment)
    {
        $giftcard = Giftcard::whereCode($payment->code)->firstOrFail();
        $giftcard->increment('amount', $payment->amount);

        return true;
    }

    public function couponRefund(Transaction $payment)
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
            return response([
                'errors' => [
                    'The amount field cannot exceed the total paid amount of $' . $order->total_paid
                ]
            ], 422);
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
                    'is_enabled' => true,
                ]);
                break;
        }

        $refund = new Transaction([
            'payment_type' => $refundType->id,
            'amount' => abs($validatedData['amount']) * -1,
            'code' => $code,
            'status' => 'refund approved',
            'refunded' => false,
            'cash_register_id' => $cash_register_id,
            'order_id' => $order->id,
            'created_by_id' => $user->id
        ]);

        $refund->save();
        $refund = $refund->load(['createdBy', 'paymentType', 'order']);

        $orderStatusController = new OrderStatusController($refund->order);
        $orderStatus = $orderStatusController->updateOrderStatus(true);
        $orderStatus['notification'] = [
            'msg' => ['Refund completed successfully!'],
            'type' => 'success'
        ];
        $orderStatus['refund'] = $refund->refresh();

        return response($orderStatus, 500); // 500 status for debug purposes only!
    }

    private function createLinkedRefund(Transaction $transaction, bool $succeed)
    {
        $user = auth()->user();
        $payment  = $transaction->payment;
        $type = $payment['type_name'];

        $refundTransaction = Transaction::create([
            'price' => $transaction->price->subtract($payment->change_price),
            'status' => $succeed ? 'refund approved' : 'refund failed',
            'cash_register_id' => $user->open_register->cash_register_id,
            'order_id' => $transaction->order->id,
            'created_by_id' => $user->id
        ]);
        $refund = Refund::create([
            'transaction_id' => $refundTransaction->id,
            'payment_id' =>  $transaction->payment->id,
            'type' => "{$type} refund"
        ]);

        $refundTransaction->refund_id = $refund->id;
        $refundTransaction->save();

        return $refundTransaction;
    }

    public function rollbackPayment(Payment $model, bool $setOrderStatus = true)
    {
        $transaction = $model->transaction;
        if (!$model->is_refundable) {
            return response(['errors' => ['This payment cannot be refunded']], 422);
        }

        switch ($model->paymentType->type) {
            case 'pos-terminal':
                $result = $this->posRefund($transaction);
                break;
            case 'card':
                $result = $this->apiRefund($transaction);
                break;
            case 'coupon':
                $result = $this->couponRefund($transaction);
                break;
            case 'giftcard':
                $result = $this->giftcardRefund($transaction);
                break;
            case 'house-account':
                $result = $this->houseAccRefund($transaction);
                break;
            case 'cash':
                $result = true;
                break;
            default:
                if ($setOrderStatus) {
                    return response(['errors' => ["Payment method: {$model->paymentType->type} cannot be refunded"]], 500);
                } else {
                    return ['errors' => ["Payment method: {$model->paymentType->type} cannot be refunded"]];
                }
        }

        if ($setOrderStatus) {
            $orderStatusController = new OrderStatusController($model->transaction->order);

            if (is_array($result) && array_key_exists('errors', $result)) {
                $refundTransaction = $this->createLinkedRefund($transaction, false);

                $orderStatus = $orderStatusController->updateOrderStatus(true);
                $orderStatus['errors'] = $result['errors'];
                $orderStatus['transaction'] = $refundTransaction;

                return response($orderStatus, 500);
            } else {
                $refundTransaction = $this->createLinkedRefund($transaction, true);

                $orderStatus = $orderStatusController->updateOrderStatus(true);
                $orderStatus['refunded_payment_id'] = $transaction->id;
                $orderStatus['notification'] = [
                    'msg' => ['Refund completed successfully!'],
                    'type' => 'success'
                ];
                $orderStatus['transaction'] = $refundTransaction;

                return response($orderStatus);
            }
        } else {
            if (is_array($result) && array_key_exists('errors', $result)) {
                return ['errors' => $result['errors']];
            } else {
                $refund = $this->createLinkedRefund($transaction, true);
                return $refund;
            }
        }
    }
}