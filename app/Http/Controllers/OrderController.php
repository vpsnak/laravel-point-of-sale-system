<?php

namespace App\Http\Controllers;

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
                'notes' => 'string|nullable',
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
            $product['price'] = $product['final_price'];
            unset($product['final_price']);
            OrderProduct::store($product);
        }

        return response($this->model::getOne($order->id), 201);
    }

    private function setSubtotal($orderData, $products)
    {
        $orderData['subtotal'] = 0;
        foreach ($products as $product) {
            $orderData['subtotal'] += $product['final_price'] * $product['qty'];
        }
        return $orderData;
    }

    private function setTax($orderData, $store_id)
    {
        $store = Store::getOne($store_id);
        $orderData['tax'] = $store->tax->percentage;
        return $orderData;
    }

    public function delete($id)
    {
        $order = $this->model::getOne($id);
        $order->status = 'canceled';
        // @TODO refund before update order
        $order->save();
        return response($order, 200);
    }
}
