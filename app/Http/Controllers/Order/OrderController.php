<?php

namespace App\Http\Controllers;

use App\Address;
use App\Payment;
use App\Customer;
use App\Giftcard;
use App\Helper\Price;
use App\Jobs\ProcessOrder;
use App\Order;
use App\StorePickup;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function all()
    {
        return response(Order::without([
            'items',
            'payments',
        ])->paginate());
    }

    public function get(Order $model)
    {
        return response($model);
    }

    public function update(Request $request, Order $order)
    {
        // $user = auth()->user();

        // $validatedID = $request->validate([
        //     'id' => 'nullable|exists:orders,id'
        // ]);

        // if (!empty($validatedID)) {
        //     $validatedData = $request->validate([
        //         'status' => 'required|in:pending_payment,paid,complete',
        //         'shipping_type' => 'string|nullable',
        //         'shipping_cost' => 'numeric|nullable',
        //     ]);
        //     $order = Order::findOrFail($validatedID['id']);
        //     $order->fill($validatedData);

        //     ProcessOrder::dispatchNow($order);

        //     if ($validatedData['status'] === 'complete') {
        //         foreach ($order->items as $product) {

        //             if (isset($product['sku'])) {
        //                 $code = explode('-', $product['sku']);
        //                 if (count($code) > 1 && $code[0] === 'giftCard') {
        //                     $giftCard = Giftcard::whereCode($code[1])->first();

        //                     if (!$giftCard) {
        //                         $giftCard = new Giftcard;
        //                         $giftCard->name = $product['name'];
        //                         $giftCard->code = $code[1];
        //                     }
        //                     if (!$giftCard->enabled) {
        //                         $giftCard->enabled = true;
        //                         $giftCard->amount = $product->final_price;
        //                     } else {
        //                         $giftCard->amount += $product->final_price;
        //                     }
        //                     $giftCard->save();
        //                 }
        //             }
        //         }
        //     }

        //     return response($order, 200);
        // }
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'nullable|required_if:method,pickup,delivery|exists:customers,id',
            'discount_type' => 'string|nullable',
            'discount_amount' => 'numeric|nullable',
            'shipping_cost' => 'numeric|nullable',
            'method' => 'required|in:retail,pickup,delivery',
            'notes' => 'string|nullable',
            // items (products)
            'products' => 'required',
            'products.*.id' => 'nullable|numeric',
            'products.*.name' => 'required|string',
            'products.*.sku' => 'required|string',
            'products.*.final_price' => 'required|numeric',
            'products.*.qty' => 'required|numeric',
            'products.*.discount_type' => 'nullable|string',
            'products.*.discount_amount' => 'nullable|numeric',
            'products.*.notes' => 'nullable|string',
            // billing
            'billing_address_id' => 'nullable|required_if:method,delivery|exists:addresses,id',
            // delivery
            'delivery' => 'nullable|required_if:method,pickup,delivery|array',
            'delivery.date' => 'nullable|required_if:method,pickup,delivery|date',
            'delivery.time' => 'nullable|required_if:method,pickup,delivery|string',
            'delivery.occasion' => 'nullable|required_if:method,delivery|numeric',
            // delivery address (shipping address)
            'delivery.address_id' => 'nullable|required_if:method,delivery|exists:addresses,id',
            // store_pickup
            'delivery.store_pickup_id' => 'nullable|required_if:method,pickup|exists:store_pickups,id',
        ]);

        $user = auth()->user();
        $store = $user->open_register->cash_register->store;

        $validatedData['created_by'] = $user->id;
        $validatedData['store_id'] = $store->id;

        if (isset($validatedData['billing_address_id'])) {
            $billing_address =  Address::findOrFail($validatedData['billing_address_id']);
            unset($validatedData['billing_address_id']);
        }

        if (isset($validatedData['delivery']) && isset($validatedData['delivery']['address_id'])) {
            $delivery_address =  Address::findOrFail($validatedData['delivery']['address_id']);
            unset($validatedData['delivery']['address_id']);
        }

        if (isset($validatedData['delivery']) && isset($validatedData['delivery']['store_pickup_id'])) {
            $store_pickup =  StorePickup::findOrFail($validatedData['delivery']['store_pickup_id']);
            unset($validatedData['delivery']['store_pickup_id']);
        }

        $has_tax = true;
        if (isset($validatedData['customer_id'])) {
            $customer = Customer::findOrFail($validatedData['customer_id']);
            $has_tax = $customer->no_tax ? false : true;
        }

        $validatedData['subtotal'] = $this->setSubtotal($validatedData);
        if ($has_tax) {
            $validatedData['tax'] = $store->tax->percentage;
        } else {
            $validatedData['tax'] = 0;
        }

        $order = new Order($validatedData);

        $products = [];
        foreach ($validatedData['products'] as $product) {
            array_push($products, $this->parseProduct($product));
        }


        $order->items = $products;
        $order->billing_address = $billing_address ?? null;

        $delivery = [];
        if (isset($delivery_address)) {
            $delivery['address'] = $delivery_address;
        } else if (isset($pickup_point)) {
            $delivery['store_pickup'] = $store_pickup;
        }
        $order->delivery = $delivery;

        $order->save();

        ProcessOrder::dispatchNow($order);

        return response([
            'order_id' => $order->id,
            'order_status' => 'created',
            'order_total' => $order->total,
            'order_total_without_tax' => $order->total_without_tax,
            'order_total_tax' => $order->total_tax,
        ], 201);
    }

    private function setSubtotal(array $validatedData)
    {
        $subtotal = 0;
        foreach ($validatedData['products'] as $product) {
            $total = $product['final_price'] * $product['qty'];

            if (isset($product['discount_type']) && isset($product['discount_amount'])) {
                $total = Price::calculateDiscount($total, $product['discount_type'], $product['discount_amount']);
            }

            $subtotal += $total;
        }
        if (isset($validatedData['discount_type']) && isset($validatedData['discount_amount'])) {
            $subtotal = Price::calculateDiscount($subtotal, $validatedData['discount_type'], $validatedData['discount_amount']);
        }

        return $subtotal;
    }

    private function parseProduct($product)
    {
        $product['price'] = $product['final_price'];

        unset($product['stores']);
        unset($product['stock_id']);
        unset($product['categories']);
        unset($product['created_at']);
        unset($product['updated_at']);
        unset($product['description']);
        unset($product['laravel_stock']);
        unset($product['magento_stock']);
        unset($product['plantcare_pdf']);

        if (
            !array_key_exists('discount_type', $product) ||
            !array_key_exists('discount_amount', $product)
        ) {
            $product['discount_type'] = $product['discount_amount'] = null;
        } else if ($product['discount_type'] && $product['discount_amount']) {
            $product['final_price'] = Price::calculateDiscount(
                $product['price'],
                $product['discount_type'],
                $product['discount_amount']
            );
        }

        return $product;
    }

    public function rollbackOrder(Order $model)
    {
        foreach ($model->items as $product) {
            if (isset($product['sku'])) {
                $code = explode('-', $product['sku']);
                if (count($code) > 1 && $code[0] === 'giftCard') {
                    $giftCard = Giftcard::whereCode($code[1])->first();

                    if (!$giftCard) {
                        return response(['errors' => ['Gift Card' => "Gift card with code: $code[1] not found"]], 422);
                    }

                    $giftCard->amount -= $product->final_price;

                    if ($giftCard->amount >= 0) {
                        $giftCard->save();
                    } else {
                        return response(['errors' =>  ['Order cancellation' => "Gift card with code: {$giftCard->code} has insufficient balance to cancel the order with id: $model->id<br>No changes where made"]], 422);
                    }
                }
            }
        }

        $results = $this->rollbackPayments($model);

        if (!$results || (is_array($results) && array_key_exists('errors', $results))) {
            return response(['errors' => $results['errors']], 500);
        }


        $model->status = 'canceled';
        $model->save();

        return response([
            'info' => ['Order' => "Order with id: $model->id successfully canceled!"],
            'data' => $model,
        ], 200);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        return Order::without([
            'items',
            'payments',
            'store_pickup'
        ])
            ->orWhere('status',  'LIKE', "%{$validatedData['keyword']}%")
            ->orWhere('occasion', 'LIKE', "%{$validatedData['keyword']}%")
            ->orWhere('location', 'LIKE', "%{$validatedData['keyword']}%")
            ->paginate();
    }

    public static function updateOrderStatus(Payment $payment, bool $refund = false)
    {
        $order = $payment->order;
        $change = $order->total - $order->total_paid;

        if ($change === 0) {
            if ($order->status !== 'pending_payment') {
                $order->change = 0;
                $order->status = 'pending_payment';
            }
        } else {
            // change is negative so fix payment amount and save the change to order
            if (!$refund) {
                $payment->amount = abs($payment->amount) + (float) $change;
                $payment->save();

                if ($order->status !== 'paid') {
                    $order->change = abs($change);
                    $order->status = 'paid';
                }
            } else {
                $order->change = abs($change);
                $order->save();

                if ($order->status !== ('pending_payment' || 'pending')) {
                    if ($order->total - $order->total_paid > 0) {
                        $order->change = 0;
                        $order->status = 'pending_payment';
                    }
                }
            }
        }

        $order->save();

        $order = $order->refresh();
        return [
            'remaining' => $order->total - $order->total_paid,
            'change' =>  $order->total - $order->total_paid,
            'order_status' => $order->status
        ];
    }

    public function rollbackPayments(Order $order)
    {
        $paymentController = new PaymentController();
        $results = [];

        foreach ($order->payments as $payment) {
            if ($payment->status === 'approved' && !$payment->refunded) {
                $result = $paymentController->refundPayment($payment, false);

                if (is_array($result) && array_key_exists('errors', $result)) {
                    if (!array_key_exists('errors', $results)) {
                        $results['errors'] = [];
                    }
                    array_push($results['errors'], $result['errors']);
                }
            }
        }

        return $results ? $results : true;
    }
}
