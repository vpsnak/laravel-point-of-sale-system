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
        if ($payment->order->status != 'complete' && $payment->order->status != 'canceled') {
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

    public function updated(Payment $payment)
    {
        $this->updateOrderStatus($payment);

        if ($payment->status == 'approved' && $payment->refunded == 1) {
            switch ($payment->paymentType->type) {
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
                    return true;
                case 'coupon':
                    $coupon = Coupon::whereCode($payment->code)->firstOrFail();
                    $coupon->uses++;
                    $coupon->save();
                    return true;
                case 'giftcard':
                    $giftcard = Giftcard::whereCode($payment->code)->firstOrFail();
                    $giftcard->amount += $payment['amount'];
                    $giftcard->save();
                    return true;
                case 'cash':
                default:
                    return true;
            }
        }
        return true;
    }
}
