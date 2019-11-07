<?php

namespace App\Http\Controllers;

use App\Coupon;
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
            'created_by' => 'required|exists:users,id',

            // test case for API/SDK
            'test_case' => 'sometimes|string',

            // card validation
            'card.number' => 'nullable|required_if:payment_type,card|numeric',
            'card.cvc' => 'nullable|required_if:payment_type,card|digits_between:3,4',
            'card.exp_date' => 'nullable|required_if:payment_type,card|after_or_equal:today',

            // coupon/giftcard validation
            'code' => 'required_if:payment_type,coupon|required_if:payment_type,giftcard'
        ]);

        $newPayment = $validatedData;
        $newPayment['payment_type'] = PaymentType::getFirst('type', $validatedData['payment_type'])->id;
        $newPayment['status'] = 'pending';

        $payment = $this->model::store($newPayment);

        switch ($validatedData['payment_type']) {
            default:
                $payment->status = 'failed';
                $payment->save();

                return response(['msg' => 'This payment method does not exist. ', 'status' => 'error'], 500);
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
                if (isset($paymentResponse->errorCode)) {
                    $payment->status = 'failed';
                    $payment->save();

                    return response([
                        'errors' => ["Error $paymentResponse->errorCode" => ["$paymentResponse->errorName"]],
                        'message' => $paymentResponse
                    ], 500);
                }

                break;

            case 'coupon':
                $coupon = Coupon::getFirst('code', $validatedData['code']);

                if (empty($coupon)) {
                    $payment->status = 'failed';
                    $payment->save();

                    return response([
                        'errors' => ['Coupon' => ['Coupon does not exist']],
                        'message' => 'Coupon does not exist'
                    ], 404);
                }

                if (date('Y-m-d H:i:s') > $coupon->to || $coupon->uses === 0) {
                    $payment->status = 'failed';
                    $payment->save();

                    return response([
                        'errors' => ['Coupon' => ['This coupon has expired']],
                        'message' => 'This coupon has expired'
                    ], 403);
                } else {
                    if ($coupon->from > date('Y-m-d H:i:s')) {
                        $payment->status = 'failed';
                        $payment->save();

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

                $validatedData['amount'] = $order->subtotal - Price::calculateDiscount(
                    $order->subtotal,
                    $coupon->discount->type,
                    $coupon->discount->amount
                );

                $coupon->uses--;
                $coupon->save();

                break;

            case 'giftcard':
                $giftcard = Giftcard::getFirst('code', $validatedData['code']);

                if (!$giftcard->enabled) {
                    $payment->status = 'failed';
                    $payment->save();

                    return response([
                        'errors' => ['Gift card' => ['This gift card is inactive']],
                        'message' => 'This gift card is inactive'
                    ], 403);
                } else {
                    if ($giftcard->amount < $validatedData['amount']) {
                        $payment->status = 'failed';
                        $payment->save();

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

            case 'pos-terminal':
                $posTerminalController = new PosTerminalController($payment, array_key_exists('test_case', $validatedData) ? $validatedData['test_case'] : null);
                $paymentResponse = $posTerminalController->posPayment($validatedData['amount']);

                if (array_key_exists('errors', $paymentResponse)) {
                    $payment->status = 'failed';
                    $payment->save();

                    return response([
                        'errors' => [
                            'POS Terminal' => [
                                $paymentResponse['errors']
                            ]
                        ],
                        'message' => $paymentResponse['errors']
                    ], 403);
                }

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
        $refund->save();
        $payment->refunded = 1;
        $payment->save();

        return response(['msg' => 'Refund completed successfully!', 'status' => 'success'], 200);
    }
}
