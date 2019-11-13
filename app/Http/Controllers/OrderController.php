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

            $this->model::updateData($validatedID, $validatedData);

            return response(Order::findOrFail($validatedID)->first(), 200);
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
            'shipping.method' => 'string|string',
            'shipping.date' => 'string|date',
            'shipping.timeSlotLabel' => 'string|nullable',
            'shipping.cost' => 'numeric',
            'shipping.notes' => 'string|nullable',
            'shipping.location' => 'string|nullable',
            'shipping.occasion' => 'string|nullable',
            'shipping.address' => 'sometimes|nullable',
            'shipping.address.id' => 'numeric|exists:addresses,id|nullable',
        ]);

        $shippingAddressData = $request->validate([
            'shipping.address.first_name' => 'required|string',
            'shipping.address.last_name' => 'required|string',
            'shipping.address.street' => 'required|string',
            'shipping.address.street2' => 'nullable|string',
            'shipping.address.city' => 'required|string',
            'shipping.address.country_id' => 'required|exists:countries,country_id',
            'shipping.address.region' => 'required|exists:regions,region_id',
            'shipping.address.postcode' => 'required|string',
            'shipping.address.phone' => 'required|numeric',
            'shipping.address.company' => 'nullable|string',
            'shipping.address.vat_id' => 'nullable|string',
        ]);

        $validatedData['shipping_type'] = $shippingData['shipping']['method'] ?? null;
        $validatedData['shipping_cost'] = $shippingData['shipping']['cost'] ?? null;
        $validatedData['delivery_date'] = $shippingData['shipping']['timeSlotLabel'] ?? null;
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

        $validatedData = $this->setSubtotal($validatedData, $order_items['products']);
        $validatedData = $this->setTax($validatedData, $validatedData['store_id']);

        $order = $this->model::store($validatedData);
        if (empty($order)) {
            return response($order, 500);
        }

        if (!empty($shippingData['shipping']['address'])) {
            $address = $shippingAddressData['shipping']['address'];
//            $concatAddress = $address['first_name'];
//            $concatAddress .= ' ' . $address['last_name'];
//            $concatAddress .= ' ' . $address['street'];
//            $concatAddress .= ' ' . $address['street2'];
//            $concatAddress .= ' ' . $address['city'];
//            $concatAddress .= ' ' . $address['address_region'];
//            $concatAddress .= ' ' . $address['address_country'];
//            $concatAddress .= ' ' . $address['postcode'];
//            $concatAddress .= ' ' . $address['phone'];
            $shipping_address = $order->shipping_address()->create($address);
            $validatedData['shipping_address'] = $shipping_address->id ?? null;
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
