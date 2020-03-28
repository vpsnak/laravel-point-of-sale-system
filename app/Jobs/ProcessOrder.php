<?php

namespace App\Jobs;

use App\Http\Controllers\Magento\Script\ProductSync;
use App\Http\Controllers\MasOrderController;
use App\Order;
use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Str;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const LOG_PREFIX = 'Process Order';

    protected $order;

    /**
     * ProcessOrder constructor.
     *
     * @param Order $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->order->status->value) {
            case 'submitted':
                foreach ($this->order->items as $item) {
                    if ($item['type'] === 'product') {
                        $this->handleStock($item, 'remove');
                    }
                }
                break;
            case 'paid':
                $masOrderController = new MasOrderController();
                $masOrderController->submitToMas($this->order);
                break;
            case 'canceled':
                foreach ($this->order->items as $item) {
                    if ($item['type'] === 'product') {
                        $this->handleStock($item, 'add');
                    }
                }
                break;
        }
    }

    public static function handleStock($item, $action)
    {

        $product = Product::findOrFail($item['id']);
        switch ($action) {
            case 'remove':
                $qty = $product->laravel_stock - $item['qty'];
                self::log("Removing Stock {$product->sku} Qty: {$qty} from: {$product->laravel_stock}");
                $product->stores()->syncWithoutDetaching(
                    [Product::LARAVEL_STORE_ID, ['qty' => $qty]]
                );
                break;
            case 'add':
                $qty = $product->laravel_stock + $item['qty'];
                self::log("Adding Stock {$product->sku} Qty: {$qty} from: {$product->laravel_stock}");
                $product->stores()->syncWithoutDetaching(
                    [Product::LARAVEL_STORE_ID, ['qty' => $qty]]
                );
                break;
        }

        if (!empty($product->magento_id) || !empty($product->stock_id)) {
            self::log("Try Sync Stock Magento {$product->sku}");
            ProductSync::syncStock($product);
        }
    }

    private static function log($message)
    {
        Log::channel('stock')->info(self::LOG_PREFIX . ': ' . $message);
    }
}
