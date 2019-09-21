<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    protected $model = Order::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'numeric|nullable',
            'user_id' => 'required|numeric',
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
            'items' => 'array',
        ]);

        $order = $this->model::store($validatedData);
        if (empty($order)) {
            return response($order, 500);
        }

        $itemController = new OrderProductController();
        foreach ($order_items['items'] as $item) {
            $item['order_id'] = $order->id;
            $itemController->create((new Request())->replace($item));
        }

        return response($this->model::store($validatedData), 201);
    }
}
