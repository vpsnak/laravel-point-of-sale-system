<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Giftcard;
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
            'created_by' => 'required|exists:users,id',

            // card validation
            'card.number' => 'nullable|required_if:payment_type,card|numeric',
            'card.cvc' => 'nullable|required_if:payment_type,card|digits_between:3,4',
            'card.exp_date' => 'nullable|required_if:payment_type,card|after_or_equal:today',
            'card.holder' => 'nullable|required_if:payment_type,card|string',

            // coupon/giftcard validation
            'code' => 'required_if:payment_type,coupon|required_if:payment_type,giftcard'
        ]);

        switch ($validatedData['payment_type']) {
            case 'cash':
                // nothing to do here, move on
                break;

            case 'card':
                $paymentResponse = CreditCardController::cardPayment(
                    $validatedData['card']['number'],
                    $validatedData['card']['exp_date'],
                    $validatedData['card']['cvc'],
                    $validatedData['card']['holder'],
                    $validatedData['amount']
                );
                if (isset($paymentResponse->errorCode)) {
                    return response([
                        'errors' => ["Error $paymentResponse->errorCode" => ["$paymentResponse->errorName"]],
                        'message' => $paymentResponse
                    ], 500);
                }
                // @TODO fix error and success display with notification @ payments
                return response([
                    'errors' => ['Credit Card' => [json_encode($paymentResponse)]],
                    'message' => $paymentResponse
                ], 200);
                break;

            case 'coupon':
                $coupon = Coupon::getFirst('code', $validatedData['code']);

                if (empty($coupon)) {
                    return response([
                        'errors' => ['Coupon' => ['Coupon does not exist']],
                        'message' => 'Coupon does not exist'
                    ], 404);
                }

                if (date('Y-m-d H:i:s') > $coupon->to) {
                    return response([
                        'errors' => ['Coupon' => ['This coupon has expired']],
                        'message' => 'This coupon has expired'
                    ], 403);
                } else {
                    if ($coupon->from > date('Y-m-d H:i:s')) {
                        return response([
                            'errors' => [
                                'Coupon' => [
                                    'Coupon activates at ' . date("m-d-Y", strtotime($coupon->from))
                                ]
                            ],
                            'message' => 'Coupon activates at ' . date("m-d-Y", strtotime($coupon->from))
                        ], 403);
                    }
                }

                $order = Order::findOrFail($validatedData['order_id']);

                $validatedData['amount'] = self::calcDiscount($order->subtotal, $coupon->discount->amount,
                    $coupon->discount->type);
                break;

            case 'giftcard':
                $giftcard = Giftcard::getFirst('code', $validatedData['code']);

                if (!$giftcard->enabled) {
                    return response([
                        'errors' => ['Gift card' => ['This gift card is inactive']],
                        'message' => 'This gift card is inactive'
                    ], 403);
                } else {
                    if ($giftcard->amount < $validatedData['amount']) {
                        return response([
                            'errors' => ['Gift card' => ['This gift card has insufficient balance to complete the transaction']],
                            'message' => 'This gift card has insufficient balance to complete the transaction'
                        ], 403);
                    } else {
                        // subtract the payed amount from giftcard

                        $giftcard->amount -= $validatedData['amount'];

                        $giftcard->save();
                    }
                }

                break;
        }

        $validatedData['payment_type'] = PaymentType::getFirst('type', $validatedData['payment_type'])->id;

        $payment = $this->model::store($validatedData);

        if (!empty($payment)) {
            return response([
                'total' => $payment->order->total,
                'total_paid' => $payment->order->total_paid,
                'payment' => $payment
            ], 201);
        }
    }

    private static function calcDiscount($price, $amount, $type)
    {
        if ($type === 'flat') {
            return $price - $amount;
        } else {
            return $price * $amount / 100;
        }
    }

    // @TODO: maybe you want to move this function

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

    public function delete($id)
    {
        if (!isset($this->model)) {
            return response('Model not found', 404);
        }

        $payment = Payment::findOrFail($id);

        switch ($payment->type) {
            default:
            case 'cash':
                break;
            case 'card':
                die('Missing refund for cards');
                break;
            case 'coupon':
                die('Missing refund for coupon');
                break;
            case 'giftcard':
                die('Missing refund for giftcard');
                break;
        }

        $this->model::deleteData($id);

        return response(['msg' => 'Refund completed successfully!', 'status' => 'success'], 200);
    }
}
