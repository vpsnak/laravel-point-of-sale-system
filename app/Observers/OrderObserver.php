<?php

namespace App\Observers;

use App\Http\Controllers\Magento\Script\ProductSync;
use App\Order;
use App\OrderProduct;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function created(Order $order)
    {
        foreach ($order->items as $item) {
            $this->handleStock($item, $order->status);
        }

        return true;
    }

    public function handleStock(OrderProduct $item, $order_status)
    {
        $product = $item->product;
        if (empty($product)) {
            return;
        }
        switch ($order_status) {
            case 'pending':
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

    public function updated(Order $order)
    {
        if ($order->status == 'canceled') {
            $order->payments()->delete();
        }
        if (
            $order->status == 'canceled'
            || $order->status == 'pending'
        ) {
            foreach ($order->items as $item) {
                $this->handleStock($item, $order->status);
            }
        }
    }
}
