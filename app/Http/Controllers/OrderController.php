<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderProduct;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    protected $model = Order::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'numeric|nullable',
            'user_id' => 'required|exists:id,users',
            'discount_type' => 'required|string',
            'discount' => 'required|numeric',
            'shipping_type' => 'required|string',
            'shipping_cost' => 'required|numeric',
            'tax' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'note' => 'required|string',
        ]);
        $validatedData['user_id'] = 1;

        $order_items = $request->validate([
            'items.*.name' => 'required|string',
            'items.*.sku' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.qty' => 'required|numeric',
            'items.*.discount_type' => 'required|string',
            'items.*.discount' => 'required|numeric',
        ]);

        $order = $this->model::store($validatedData);
        if (empty($order)) {
            return response($order, 500);
        }

        foreach ($order_items['items'] as $item) {
            $item['order_id'] = $order->id;
            OrderProduct::store($item);
        }

        return response($order, 201);
    }
}
