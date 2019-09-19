<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $response = $this->insert($request->all());

        if ($response) {
            return response(true, 200);
        } else {
            return response(false, 200);
        }
    }

    public function insert($data)
    {
        $validatedData = Validator::make($data, [
            'customer_id' => 'required|numeric|nullable',
            'user_id' => 'required|numeric',
            'discount_type' => 'required|string',
            'discount' => 'required|string',
            'shipping_type' => 'required|string',
            'shipping_cost' => 'required|numeric',
            'tax' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'note' => 'required|string',
        ]);

        $insert = Order::create($validatedData);

        if (!empty($insert)) {
            return true;
        } else {
            return false;
        }
    }
}
