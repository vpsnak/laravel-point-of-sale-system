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
            'user_id' => 'required|exists:users,id',
            'discount_type' => 'string|nullable',
            'discount' => 'numeric|nullable',
            'shipping_type' => 'string|nullable',
            'shipping_cost' => 'numeric|nullable',
            'tax' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'note' => 'string|nullable',
        ]);
    
        $order_items = $request->validate([
            'items.*.name' => 'required|string',
            'items.*.sku' => 'required|string',
            'items.*.final_price' => 'required|numeric',
            'items.*.qty' => 'required|numeric',
            'items.*.discount_type' => 'string|nullable',
            'items.*.discount' => 'numeric|nullable',
        ]);
    
        $order = $this->model::store($validatedData);
        if (empty($order)) {
            return response($order, 500);
        }
    
        foreach ($order_items['items'] as $item) {
            $item['order_id'] = $order->id;
            $item['price'] = $item['final_price'];
            unset($item['final_price']);
            OrderProduct::store($item);
        }
    
        return response($order, 201);
    }
}
