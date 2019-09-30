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
            'customer_id' => 'nullable|exists:customers,id',
            'status' => 'required|in:pending,pending_payment,paid,complete',
            'discount_type' => 'string|nullable',
            'discount_amount' => 'numeric|nullable',
            'shipping_type' => 'string|nullable',
            'shipping_cost' => 'numeric|nullable',
            'tax' => 'required|numeric',
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
    
        $validatedData['subtotal'] = 0;
        foreach ($order_items['products'] as $product) {
            $validatedData['subtotal'] += $product['final_price'];
        }
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
}
