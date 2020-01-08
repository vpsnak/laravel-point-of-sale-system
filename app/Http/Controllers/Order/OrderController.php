<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Giftcard;
use App\Helper\Price;
use App\Jobs\ProcessOrder;
use App\Order;
use App\OrderAddress;
use App\OrderProduct;
use App\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    protected $model = Order::class;

    public function create(Request $request)
    {
        $validatedID = $request->validate([
            'id' => 'nullable|exists:orders,id'
        ]);

        if (!empty($validatedID)) {
            $validatedData = $request->validate([
                'status' => 'required|in:pending_payment,paid,complete',
                'shipping_type' => 'string|nullable',
                'shipping_cost' => 'numeric|nullable',
            ]);
            Order::updateData($validatedID, $validatedData);
            $order = Order::getOne($validatedID['id']);
            ProcessOrder::dispatchNow($order);

            if ($validatedData['status'] === 'complete') {
                foreach ($order->items as $product) {

                    if (isset($product['sku'])) {
                        $code = explode('-', $product['sku']);
                        if (count($code) > 1 && $code[0] === 'giftCard') {
                            $giftCard = Giftcard::whereCode($code[1])->first();

                            if (!$giftCard) {
                                $giftCard = new Giftcard;
                                $giftCard->name = $product['name'];
                                $giftCard->code = $code[1];
                            }

                            $giftCard->amount += $product->final_price;
                            $giftCard->save();
                        }
                    }
                }
            }

            return response($order, 200);
        }

        $validatedData = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'status' => 'required|in:pending,pending_payment,paid,complete,canceled',
            'discount_type' => 'string|nullable',
            'discount_amount' => 'numeric|nullable',
            'shipping_type' => 'string|nullable',
            'shipping_cost' => 'numeric|nullable',
            'notes' => 'string|nullable',
            'store_id' => 'required|exists:stores,id',
        ]);

        $validatedData['created_by'] = auth()->user()->id;

        $shippingData = $request->validate([
            'shipping.method' => 'required|in:retail,pickup,delivery',
            'shipping.notes' => 'nullable|string',

            'shipping.cost' => 'required_if:shipping.method,pickup,delivery|numeric',
            'shipping.date' => 'required_if:shipping.method,pickup,delivery|date',
            'shipping.timeSlotLabel' => 'nullable|required_if:shipping.method,pickup,delivery|string',
            'shipping.location' => 'nullable|required_if:shipping.method,delivery|numeric',
            'shipping.occasion' => 'nullable|required_if:shipping.method,delivery|numeric',

            'shipping.address' => 'nullable|required_if:shipping.method,delivery',
            'shipping.address.id' => 'nullable|required_if:shipping.method,delivery|numeric|exists:addresses,id',

            'shipping.pickup_point.id' => 'required_if:shipping.method,pickup|numeric',
        ]);

        $shippingAddressData = $request->validate([
            'shipping.address.first_name' => 'required_if:shipping.method,delivery|string',
            'shipping.address.last_name' => 'required_if:shipping.method,delivery|string',
            'shipping.address.street' => 'required_if:shipping.method,delivery|string',
            'shipping.address.street2' => 'nullable|string',
            'shipping.address.city' => 'required_if:shipping.method,delivery|string',
            'shipping.address.country_id' => 'required_if:shipping.method,delivery|exists:countries,country_id',
            'shipping.address.region' => 'required_if:shipping.method,delivery|exists:regions,region_id',
            'shipping.address.postcode' => 'required_if:shipping.method,delivery|string',
            'shipping.address.phone' => 'required_if:shipping.method,delivery|string',
        ]);

        $validatedData['shipping_type'] = $shippingData['shipping']['method'] ?? null;
        $validatedData['store_pickup_id'] = $shippingData['shipping']['pickup_point']['id'] ?? null;
        $validatedData['shipping_cost'] = $shippingData['shipping']['cost'] ?? null;
        $validatedData['delivery_date'] = $shippingData['shipping']['date'] ?? Carbon::today();
        $validatedData['delivery_slot'] = $shippingData['shipping']['timeSlotLabel'] ?? null;
        $validatedData['location'] = $shippingData['shipping']['location'] ?? null;
        $validatedData['occasion'] = $shippingData['shipping']['occasion'] ?? null;
        $validatedData['notes'] = $shippingData['shipping']['notes'] ?? null;

        $order_items = $request->validate([
            'products' => 'required|array',
            'products.*.name' => 'required|string',
            'products.*.sku' => 'required|string',
            'products.*.final_price' => 'required|numeric',
            'products.*.qty' => 'required|numeric',
            'products.*.discount_type' => 'string|nullable',
            'products.*.discount_amount' => 'numeric|nullable',
            'products.*.notes' => 'string|nullable',
        ]);

        $has_tax = true;
        $customer = Customer::getOne($validatedData['customer_id']);
        if (!empty($customer)) {
            $has_tax = $customer->no_tax ? false : true;
        }

        $validatedData = $this->setSubtotal($validatedData, $order_items['products']);
        if ($has_tax) {
            $validatedData = $this->setTax($validatedData, $validatedData['store_id']);
        } else {
            $validatedData['tax'] = 0;
        }

        $order = $this->model::store($validatedData);
        if (empty($order)) {
            return response($order, 500);
        }

        if (!empty($shippingData['shipping']['address'])) {
            $shipping_address = OrderAddress::store($shippingAddressData['shipping']['address']);
            $order->shipping_address_id = $shipping_address->id;
            $order->save();
        }

        foreach ($order_items['products'] as $product) {
            $product['order_id'] = $order->id;
            $product = $this->setProductPrice($product);
            OrderProduct::store($product);
        }
        $order = Order::getOne($order->id);

        ProcessOrder::dispatchNow($order);

        return response($order, 201);
    }

    private function setSubtotal($orderData, $products)
    {
        $subtotal = 0;
        foreach ($products as $product) {
            $total = $product['final_price'] * $product['qty'];
            if (isset($product['discount_type']) && isset($product['discount_amount'])) {
                $total = Price::calculateDiscount($total, $product['discount_type'], $product['discount_amount']);
            }
            $subtotal += $total;
        }
        if (isset($orderData['discount_type']) && isset($orderData['discount_amount'])) {
            $subtotal = Price::calculateDiscount($subtotal, $orderData['discount_type'], $orderData['discount_amount']);
        }
        $orderData['subtotal'] = $subtotal;
        return $orderData;
    }

    private function setTax($orderData, $store_id)
    {
        $store = Store::getOne($store_id);
        $orderData['tax'] = $store->tax->percentage;
        return $orderData;
    }

    private function setProductPrice($product)
    {
        $product['price'] = $product['final_price'];
        unset($product['final_price']);
        return $product;
    }

    public function delete($id)
    {
        $order = $this->model::getOne($id);

        foreach ($order->items as $product) {
            if (isset($product['sku'])) {
                $code = explode('-', $product['sku']);
                if (count($code) > 1 && $code[0] === 'giftCard') {
                    $giftCard = Giftcard::whereCode($code[1])->first();

                    if (!$giftCard) {
                        return response(['errors' => ['Gift Card' => "Gift card with code: $code[1] not found"]]);
                    }

                    $giftCard->amount -= $product->final_price;

                    if ($giftCard->amount >= 0) {
                        $giftCard->save();
                    } else {
                        return response(['errors' =>  ['Order cancellation' => "Gift card with code: $giftCard->code has insufficient balance to cancel the order with id: $id<br>No changes where made"]], 422);
                    }
                }
            }
        }

        $order->status = 'canceled';
        $order->save();

        return response([
            'info' => ['Order' => "Order with id: $order->id successfully canceled!"],
            'data' => $order,
        ], 200);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        return $this->searchResult(
            ['status', 'occasion', 'location'],
            $validatedData['keyword'],
            true
        );
    }
}
