<?php

namespace App\Observers;

use App\Jobs\ProcessOrder;
use App\Order;

class OrderObserver
{
    public function updated(Order $order)
    {
        ProcessOrder::dispatch($order);
    }
}
