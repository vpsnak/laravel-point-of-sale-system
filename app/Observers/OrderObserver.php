<?php

namespace App\Observers;

use App\Http\Controllers\Magento\Script\ProductSync;
use App\Order;

class OrderObserver
{
    public function updated(Order $order)
    {
        if ($order->status == 'canceled') {
            $order->payments()->delete();
        }
        if ($order->status == 'complete' || $order->status == 'pending_payment') {
            foreach ($order->items as $item) {
                $product = $item->product;
                if (empty($product) || empty($product->magento_id) || empty($product->stock_id)) {
                    continue;
                }
                ProductSync::syncStock($product);
            }
        }
    }
}
