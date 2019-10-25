<?php

namespace App\Observers;

use App\Http\Controllers\Magento\Script\ProductSync;
use App\Order;
use App\OrderProduct;

class OrderObserver
{
    public function updated(Order $order)
    {
        if ($order->status == 'canceled') {
            $order->payments()->delete();
        }
        if ($order->status == 'canceled' || $order->status == 'pending_payment') {
            foreach ($order->items as $item) {
                $this->handleStock($item, $order->status);
            }
        }
    }

    public function handleStock(OrderProduct $item, $order_status)
    {
        $product = $item->product;
        if (empty($product)) {
            return;
        }
        switch ($order_status) {
            case 'pending_payment':
                $qty = $product->laravel_stock - $item->qty;
                $product->stores()->syncWithoutDetaching(
                    [$product->laravelStore()->id, ['qty' => $qty]]
                );
                break;
            case 'canceled':
                $qty = $product->laravel_stock + $item->qty;
                $product->stores()->syncWithoutDetaching(
                    [$product->laravelStore()->id, ['qty' => $qty]]
                );
                break;
        }
        if (empty($product->magento_id) || empty($product->stock_id)) {
            return;
        }
        ProductSync::syncStock($product);
    }
}
