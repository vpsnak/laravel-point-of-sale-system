<?php

namespace App\Http\Controllers;

use App\Helper\Price;
use App\Order;
use App\OrderProduct;
use App\Store;
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

            return response($this->model::updateData($validatedID, $validatedData), 200);
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
            'created_by' => 'required|exists:users,id',
        ]);

        $shippingData = $request->validate([
            'shipping.method' => 'string|string',
            'shipping.date' => 'string|date',
            'shipping.timeSlotLabel' => 'string|nullable',
            'shipping.timeSlotCost' => 'numeric',
            'shipping.notes' => 'string|nullable',
            'shipping.address' => 'sometimes|nullable',
            'shipping.address.id' => 'numeric|exists:addresses,id|nullable',
        ]);

        if (!empty($shippingData['shipping']['address'])) {
            $address = $shippingData['shipping']['address'];
            $concatAddress = $address['first_name'];
            $concatAddress .= ' ' . $address['last_name'];
            $concatAddress .= ' ' . $address['street'];
            $concatAddress .= ' ' . $address['street2'];
            $concatAddress .= ' ' . $address['city'];
            $concatAddress .= ' ' . $address['address_region'];
            $concatAddress .= ' ' . $address['address_country'];
            $concatAddress .= ' ' . $address['postcode'];
            $concatAddress .= ' ' . $address['phone'];
        }

        $validatedData['shipping_type'] = $shippingData['shipping']['method'] ?? null;
        $validatedData['shipping_cost'] = $shippingData['shipping']['timeSlotCost'] ?? null;
        $validatedData['shipping_address'] = $concatAddress ?? null;
        $validatedData['delivery_date'] = $shippingData['shipping']['timeSlotLabel'] ?? null;
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

        $validatedData = $this->setSubtotal($validatedData, $order_items['products']);
        $validatedData = $this->setTax($validatedData, $validatedData['store_id']);

        $order = $this->model::store($validatedData);
        if (empty($order)) {
            return response($order, 500);
        }

        foreach ($order_items['products'] as $product) {
            $product['order_id'] = $order->id;
            $product = $this->setProductPrice($product);
            OrderProduct::store($product);
        }

        return response($this->model::getOne($order->id), 201);
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
        $order->status = 'canceled';
        $order->save();
        return response($order, 200);
    }
}
