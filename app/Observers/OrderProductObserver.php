<?php

namespace App\Observers;

use App\OrderProduct;

class OrderProductObserver
{
    /**
     * Handle the order product "created" event.
     *
     * @param OrderProduct $orderProduct
     * @return void
     */
    public function created(OrderProduct $orderProduct)
    {
        OrderObserver::handleStock($orderProduct, 'remove');
    }
}
