<?php

namespace App\Http\Controllers;

use App\Cart;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CartController extends BaseController
{
    protected $model = Cart::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            // 'customer_id' => 'required|exists:id,customers',
            'name' => 'string',
            'cart' => 'required|array',
        ]);
        $validatedData['cart'] = json_encode($validatedData['cart']);
        $validatedData['customer_id'] = 1;

        if (empty($validatedData['name'])) {
            $validatedData['name'] = 'Operator - ' . Carbon::now()->toDateString();
        }
//        dd($validatedData);

        return response($this->model::store($validatedData), 201);
    }

    public function getHold(Request $request)
    {
        $validatedData = $request->validate([
            'user' => 'required|int'
        ]);

        return response($this->model::getAll('customer_id', $validatedData['user']));
    }
}
