<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
use Money\Money;

class TransactionController extends Controller
{
    private $transactionData;
    private $paymentData;
    private $order;

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
        $this->order = Order::findOrFail($this->transactionData['order_id']);
        $this->transactionData['created_by_id'] = auth()->user()->id;
        $this->transactionData['cash_register_id'] = auth()->user()->open_register->cash_register->id;
        $this->transactionData['status'] = 'pending';
        $response = [];

        switch ($paymentType) {
            case 'cash':
                $response = $this->cashPay();
                break;
            case 'pos-terminal':
                $response = $this->posPay();
                break;
            case 'card':
                $response = $this->apiPay($creditCardData['card']);
                break;
            case 'coupon':
                $response = $this->couponPay();
                break;
            case 'giftcard':
                $response = $this->giftcardPay();
                break;
            case 'house-account':
                $response = $this->houseAccPay();
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
                if (isset($this->transactionData['payment_id'])) {
                    $this->transactionData->status = 'failed';
                    $this->transactionData->save();
                    $response['transaction'] = $this->transactionData;

                    return response()->json($response, 422, [], JSON_NUMERIC_CHECK);
                } else {
                    return response($response, 422);
                }
            }
        }

        $this->transactionData->status = 'approved';
        $this->transactionData->save();
        $this->order = $this->order->refresh();

        $orderStatusController = new OrderStatusController($this->order);
        $response = $orderStatusController->updateOrderStatus();
        ProcessOrder::dispatchNow($this->order);

        $response['transaction'] = $this->transactionData;
        $response['notification'] = [
            'msg' => 'Payment received!',
            'type' => 'success'
        ];
        return response()->json($response, 201, [], JSON_NUMERIC_CHECK);
    }

    private function createTransaction()
    {
        $this->transactionData = Transaction::create($this->transactionData);
        $this->paymentData['transaction_id'] = $this->transactionData->id;
        $this->paymentData = Payment::create($this->paymentData);
        $this->transactionData->payment_id = $this->paymentData->id;
        $this->transactionData->save();
    }

    private function cashPay()
    {
        $this->createTransaction();

        $change_price = $this->transactionData->price->subtract($this->order->remaining_price);
        if ($change_price->isPositive()) {
            $this->paymentData->update(['change_price' => $change_price]);
        }
        return true;
    }

    private function posPay()
    {
        $this->createTransaction();
        $elavonSdkPayment = new ElavonSdkTransactionController();
        $elavonSdkPayment->selected_transaction = 'SALE';
        $elavonSdkPayment->amount = $this->transactionData->price->getAmount();
        $elavonSdkPayment->transaction_id = $this->transactionData->id;

        return $elavonSdkPayment->posPayment();
    }

    private function apiPay(array $creditCardData)
    {
        $this->createTransaction();
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

    private function couponPay()
    {
        $coupon = Coupon::where('code', $this->transactionData['code'])->first();

        if (empty($coupon)) {
            return ['errors' => ['Coupon does not exist']];
        }

        if (now() > $coupon->to || $coupon->uses === 0) {
            return ['errors' => ['This coupon has expired']];
        } else if ($coupon->from > now()) {
            return ['errors' => ['Coupon activates at ' . date("m-d-Y", strtotime($coupon->from))]];
        } else {
            $orderRemainingPrice = $this->order->remaining_price;
            $this->transactionData['price'] = $this->order->mdse_price->subtract(Price::calculateDiscount($this->order->mdse_price, $coupon->discount));
            if ($this->transactionData['price']->greaterThan($orderRemainingPrice)) {
                $this->transactionData['price'] = $orderRemainingPrice;
            }
            $change_price = Price::parsePrice($this->transactionData['price'])->subtract($this->order->remaining_price);
            $this->createTransaction();
            if ($change_price->isPositive()) {
                $this->paymentData->update(['change_price' => $change_price]);
            }

            $coupon->decrement('uses');

            return true;
        }
    }

    private function giftcardPay()
    {
        $this->createTransaction();
        $giftcard = Giftcard::where('code', $this->transactionData['code'])->first();

        if (!$giftcard) {
            return ['errors' => ['Giftcard <b>#' . $this->transactionData['code'] . '</b> does not exist']];
        } else if (!$giftcard->enabled_at) {
            return ['errors' => ['This gift card is inactive']];
        } else if ($giftcard->price->lessThan($this->transactionData->price)) {
            return ['errors' => ['This gift card has insufficient balance to complete the transaction']];
        } else {
            $giftcard->price = $giftcard->price->subtract($this->transactionData->price);
            $giftcard->save();
            return true;
        }
    }


    private function houseAccPay()
    {
        $this->createTransaction();
        $customer = Customer::where('house_account_number', $this->transactionData['house_account_number'])->first();
        if (empty($customer)) {
            return ['errors' => ['House Account' => ['House account does not exist.']]];
        } else if ($this->transactionData['price'] > $customer->house_account_limit) {
            return [
                'errors' => [
                    'House Account' => [
                        'House account has insufficient balance.<br>Balance available: $ ' . round($customer->house_account_limit, 2)
                    ]
                ]
            ];
        } else {
            $customer->decrement('house_account_limit', $this->transactionData['price']);
            $this->transactionData->code = $this->transactionData['house_account_number'];
        }
    }

    public function posRefund(Transaction $transaction)
    {
        $elavonSdkPaymentController = new ElavonSdkTransactionController;

        $elavonSdkPaymentController->selected_transaction = 'VOID';
        $elavonSdkPaymentController->originalTransId = $this->paymentData->code;
        $paymentResponse = $elavonSdkPaymentController->posPayment();

        if (isset($paymentResponse['errors'])) {
            $elavonSdkPaymentController->selected_transaction = 'LINKED_REFUND';
            $elavonSdkPaymentController->amount = $this->paymentData->amount;
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
        $customer->increment('house_account_limit', $this->transactionData->price);

        return true;
    }

    public function giftcardRefund(Transaction $transaction)
    {
        $giftcard = Giftcard::where('code', $transaction->code)->firstOrFail();
        return $giftcard->update(['price' => $giftcard->price->add($transaction->price)]);
    }

    public function couponRefund(Transaction $transaction)
    {
        $coupon = Coupon::where('code', $transaction->code)->firstOrFail();
        $coupon->increment('uses');

        return true;
    }

    public function createRefund(Request $request)
    {
        $validatedData = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'price' => 'required|array',
            'price.amount' => 'required|numeric',
            'price.currency' => 'required|string',
        ]);

        $refundPrice = Price::parsePrice($validatedData['price']);
        $payment = Payment::findOrFail($validatedData['payment_id']);
        $this->order = $payment->transaction->order;
        $transaction = $payment->transaction;
        $user = auth()->user();
        $cash_register_id = $user->open_register->cash_register_id;

        if ($refundPrice->greaterThan($transaction->price->subtract($transaction->payment->change_price))) {
            return response([
                'errors' => [
                    'The amount field cannot exceed the total paid amount of $' . $this->order->total_paid
                ]
            ], 422);
        }

        static::rollbackPayment($payment, true, $refundPrice);
    }

    private function createLinkedRefund(Transaction $transaction, bool $succeed, Money $price = null)
    {
        $user = auth()->user();
        $payment  = $transaction->payment;
        $type = $payment['type_name'];

        if ($price) {
            $transactionPrice = $transaction->price->subtract($payment->change_price);
        } else {
            $transactionPrice = $transaction->price;
        }

        $this->transactionData = Transaction::create([
            'price' => $transactionPrice,
            'status' => $succeed ? 'refund approved' : 'refund failed',
            'cash_register_id' => $user->open_register->cash_register_id,
            'order_id' => $transaction->order->id,
            'created_by_id' => $user->id
        ]);
        $refund = Refund::create([
            'transaction_id' => $this->transactionData->id,
            'payment_id' =>  $transaction->payment->id,
            'type' => "{$type} refund"
        ]);

        $this->transactionData->refund_id = $refund->id;
        $this->transactionData->save();

        return $this->transactionData;
    }

    public function rollbackPayment(Payment $model, bool $setOrderStatus = true, Money $price = null)
    {
        $transaction = $model->transaction;
        if (!$model->refundable_price->isPositive() || $model->refundable_price->isZero()) {
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
        }

        if ($setOrderStatus) {
            $orderStatusController = new OrderStatusController($model->transaction->order);

            if (is_array($result) && array_key_exists('errors', $result)) {
                $refundTransaction = $this->createLinkedRefund($transaction, false, $price);

                $orderStatus = $orderStatusController->updateOrderStatus(true);
                $orderStatus['errors'] = $result['errors'];
                $orderStatus['transaction'] = $refundTransaction;

                return response($orderStatus, 500);
            } else {
                $refundTransaction = $this->createLinkedRefund($transaction, true, $price);

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
