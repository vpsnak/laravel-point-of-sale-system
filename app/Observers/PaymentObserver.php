<?php

namespace App\Observers;

use App\Coupon;
use App\Customer;
use App\Giftcard;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\ElavonSdkPaymentController;
use App\Payment;

class PaymentObserver
{
    public function created(Payment $payment)
    {
        $this->updateOrderStatus($payment);
    }

    public function updateOrderStatus(Payment $payment)
    {
        $order = $payment->order()->firstOrFail();
        if ($order->status != 'complete' && $order->status != 'canceled') {
            $change = $order->total - $order->total_paid;
            if ($change > 0) {
                if ($order->status != 'pending_payment') {
                    $order->change = 0;
                    $order->status = 'pending_payment';
                    $order->save();
                }
            } else {
                // change is negative so fix payment amount and save the change to order
                $payment->increment('amount', $change);
                if ($order->status != 'paid') {
                    $order->change = abs($change);
                    $order->status = 'paid';
                    $order->save();
                }
            }
        }
    }

    public function updated(Payment $payment)
    {
        $this->updateOrderStatus($payment);

        if ($payment->status == 'approved' && $payment->refunded == 1) {
            switch ($payment->paymentType->type) {
                case 'pos-terminal':
                    $elavonSdkPaymentController = new ElavonSdkPaymentController;

                    $elavonSdkPaymentController->selected_transaction = 'VOID';
                    $elavonSdkPaymentController->originalTransId = $payment->code;
                    $paymentResponse = $elavonSdkPaymentController->posPayment();

                    if (isset($paymentResponse['error'])) {
                        return false;
                    }
                    return true;
                case 'card':
                    $paymentResponse = (new CreditCardController)->cardRefund($payment->code);
                    if (isset($paymentResponse['error'])) {
                        return false;
                    }
                    return true;
                case 'coupon':
                    $coupon = Coupon::whereCode($payment->code)->firstOrFail();
                    $coupon->increment('uses');
                    return true;
                case 'giftcard':
                    $giftcard = Giftcard::whereCode($payment->code)->firstOrFail();
                    $giftcard->increment('amount', $payment->amount);
                    return true;
                case 'house-account':
                    $customer = Customer::where('house_account_number', $payment->code)->firstOrFail();
                    $customer->increment('house_account_limit', $payment->amount);
                    return true;
                case 'cash':
                default:
                    return true;
            }
        }
        return true;
    }
}
