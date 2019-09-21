<?php

namespace App\Http\Controllers;

use App\OrderProduct;
use Illuminate\Http\Request;

class OrderProductController extends BaseController
{
    protected $model = OrderProduct::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|numeric',
            'name' => 'required|string',
            'sku' => 'required|string',
            'price' => 'required|numeric',
            'qty' => 'required|numeric',
            'discount_type' => 'required|string',
            'discount' => 'required|numeric',
        ]);

        return response($this->model::store($validatedData), 201);
    }
}
