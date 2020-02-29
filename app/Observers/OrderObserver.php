<?php

namespace App\Observers;

use App\Jobs\ProcessOrder;
use App\Order;
use Log;

class OrderObserver
{
    public function updated(Order $order)
    {
        ProcessOrder::dispatch($order);
    }
}
