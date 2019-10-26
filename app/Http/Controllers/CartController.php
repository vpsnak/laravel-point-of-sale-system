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
            'cash_register_id' => 'required|exists:cash_registers,id',
            'name' => 'nullable|string',
            'cart' => 'required|array',
            'product_count' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0'
        ]);

        $validatedData['cart'] = json_encode($validatedData['cart']);

        if (empty($validatedData['name'])) { 
            $validatedData['name'] = Carbon::now()->toDateString() . " (" .  $validatedData['product_count'] . " Products) - " . "$" .$validatedData['total_price']; 
        }
        return response($this->model::store($validatedData), 201);  
    }

    public function getHold(Request $request)
    {
        $validatedData = $request->validate([
            'cash_register_id' => 'required|int'
        ]);

        return response($this->model::getAll('cash_register_id', $validatedData['cash_register_id']));
    }
}
