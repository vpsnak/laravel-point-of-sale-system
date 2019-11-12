<?php

namespace App\Observers;

use App\Http\Controllers\Magento\Script\ProductSync;
use App\Order;
use App\OrderProduct;
use App\Product;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    const LOG_PREFIX = 'Handle Stock';

    public function updated(Order $order)
    {
        if ($order->status == 'canceled') {
            foreach ($order->payments as $payment) {
                if ($payment->status === 'approved' && $payment->refunded != 1) {
                    $refund = $payment->replicate();
                    $refund->status = 'refunded';
                    $refund->save();
                    $payment->refunded = 1;
                    $payment->save();
                }
            }

            foreach ($order->items as $item) {
                $this->handleStock($item, 'add');
            }
        }
    }

    public static function handleStock(OrderProduct $item, $action)
    {
        $product = $item->product;
        if (empty($product)) {
            self::log('Empty Product');
            return;
        }
        switch ($action) {
            case 'remove':
                $qty = $product->laravel_stock - $item->qty;
                self::log('Removing Stock ' . $product->sku . ' Qty: ' . $qty . ' from: ' . $product->laravel_stock);
                $product->stores()->syncWithoutDetaching(
                    [Product::LARAVEL_STORE_ID, ['qty' => $qty]]
                );
                break;
            case 'add':
                $qty = $product->laravel_stock + $item->qty;
                self::log('Adding Stock ' . $product->sku . ' Qty: ' . $qty . ' from: ' . $product->laravel_stock);
                $product->stores()->syncWithoutDetaching(
                    [Product::LARAVEL_STORE_ID, ['qty' => $qty]]
                );
                break;
        }
        if (empty($product->magento_id) || empty($product->stock_id)) {
            return;
        }
        self::log('Try Sync Stock Magento ' . $product->sku);
        ProductSync::syncStock($product);
    }

    private static function log($message)
    {
        Log::channel('stock')->info(self::LOG_PREFIX . ': ' . $message);
    }
}
