<?php

namespace App\Observers;

use App\Coupon;
use App\Giftcard;
use App\Http\Controllers\CreditCardController;
use App\Payment;

class PaymentObserver
{
    public function created(Payment $payment)
    {
        $this->updateOrderStatus($payment);
    }

    public function updateOrderStatus(Payment $payment)
    {
        if ($payment->order->status != 'complete') {
            if ($payment->order->total - $payment->order->total_paid > 0) {
                if ($payment->order->status != 'pending_payment') {
                    $payment->order->status = 'pending_payment';
                    $payment->order->save();
                }
            } else {
                if ($payment->order->status != 'paid') {
                    $payment->order->status = 'paid';
                    $payment->order->save();
                }
            }
        }
    }

    public function deleted(Payment $payment)
    {
        $this->updateOrderStatus($payment);

        switch ($payment->paymentType->type) {
            case 'cash':
                break;
            case 'pos-terminal':
                break;
            case 'card':
                $paymentResponse = CreditCardController::cardPayment(
                    '5472063333333330',
                    '1224',
                    '123',
                    $payment->amount
                );
                if (isset($paymentResponse->errorCode)) {
                    return false;
                }
                break;
            case 'coupon':
                $coupon = Coupon::whereCode($payment->code)->firstOrFail();
                $coupon->uses++;
                $coupon->save();
                break;
            case 'giftcard':
                $giftcard = Giftcard::whereCode($payment->code)->firstOrFail();
                $giftcard->amount += $payment['amount'];
                $giftcard->save();
                break;
            default:
                return false;
        }

        $payment->status = 'refunded';
        $payment->save();

        return false;
    }
}
