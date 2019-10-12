<?php

namespace App\Observers;

use App\Order;

class OrderObserver
{
    public function updated(Order $order)
    {
        if ($order->status != 'canceled') {
            $order->payments()->delete();
        }
    }
}
