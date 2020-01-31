<?php

namespace App\Observers;

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
    }
}
