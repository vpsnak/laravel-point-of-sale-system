<?php

namespace App\Http\Controllers;

use App\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderProductController extends BaseController
{
    protected $model = OrderProduct::class;

    public function create(Request $request)
    {
        $response = $this->insert($request->all());

        if ($response) {
            return response(true, 200);
        } else {
            return response(false, 500);
        }
    }

    public function insert($data)
    {
        $validatedData = Validator::make($data, [
            'order_id' => 'required|numeric',
            'name' => 'required|string',
            'sku' => 'required|string',
            'price' => 'required|numeric',
            'qty' => 'required|string',
            'discount_type' => 'required|string',
            'discount' => 'required|numeric',
        ]);

        $insert = OrderProduct::create($validatedData);

        if (!empty($insert)) {
            return true;
        } else {
            return false;
        }
    }
}
