<?php

namespace App\Jobs;

use App\Customer;
use App\Order;
use App\OrderAddress;
use App\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchMagentoOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const LOG_PREFIX = 'Order Sync';

    protected $order;

    /**
     * FetchMagentoOrder constructor.
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        Order::unsetEventDispatcher();
        if (Order::where('magento_id', $this->order->entity_id)->exists()) {
            Order::where('magento_id', $this->order->entity_id)->update([
                'customer_id' => Customer::where('magento_id', $this->order->customer_id)->first()->id ?? null,
                'status' => $this->getOrderStatus($this->order->status),
                'tax' => $this->order->tax_rate ?? 0,
                'subtotal' => $this->order->subtotal,
                'shipping_type' => 'eshop',
                'shipping_cost' => $this->order->shipping_amount,
            ]);
            return true;
        }
        $billing_id = 0;
        $shipping_id = 0;
        foreach ($this->order->addresses as $address) {
            if (empty($address->country_id)) {
                self::log('order_failed:' . $this->order->entity_id);
                self::log('address with no country_id');
                return;
            }
            if ($address->address_type == 'shipping' || $address->address_type == 'billing') {
                $order_address = OrderAddress::getInsertedId([
                    'first_name' => $address->firstname,
                    'last_name' => $address->lastname,
                    'street' => substr($address->street, 0, 98) ?? '-',
                    'street2' => null,
                    'city' => $address->city,
                    'country_id' => $address->country_id,
                    'region' => $address->region ?? '-',
                    'postcode' => $address->postcode ?? '-',
                    'phone' => $address->telephone,
                    'company' => substr($address->company, 0, 98),
                ]);
                if ($address->address_type == 'shipping') {
                    $shipping_id = $order_address;
                }
                if ($address->address_type == 'billing') {
                    $billing_id = $order_address;
                }
            }
        }

        $order = Order::create([
            'magento_id' => $this->order->entity_id,
            'customer_id' => Customer::where('magento_id', $this->order->customer_id)->first()->id ?? null,
            'store_id' => 1,
            'created_by' => 1,
            'status' => $this->getOrderStatus($this->order->status),
            'tax' => $this->order->tax_rate ?? 0,
            'subtotal' => $this->order->subtotal,
            'change' => 0,
            'magento_shipping_address_id' => $shipping_id,
            'magento_billing_address_id' => $billing_id,
            'shipping_type' => 'eshop',
            'shipping_cost' => $this->order->shipping_amount,
        ]);

        foreach ($this->order->order_items as $item) {
            $order->items()->updateOrCreate([
                'sku' => $item->sku,
            ], [
                'order_id' => $order->id,
                'name' => $item->name,
                'price' => $item->row_total,
                'qty' => $item->qty_ordered,
            ]);
        }
        return true;
    }

    /**
     * @param $status
     * @return string
     */
    private function getOrderStatus($status)
    {
        switch ($status) {
            case 'transmitted':
            case 'complete':
                return 'paid';
            case 'pending_payment':
                return 'pending_payment';
            case 'canceled':
            case 'paypal_reversed':
            case 'paypal_canceled_reversal':
            case 'fraud':
            case 'closed':
                return 'canceled';
            case 'holded':
                return 'pending';
            default:
                dd($status);
        }
    }

    /**
     * @param $message
     */
    private static function log($message)
    {
        Log::channel('connector')->info(self::LOG_PREFIX . ': ' . $message);
    }
}
