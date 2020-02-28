<?php

namespace App\Http\Controllers;

use App\Cart;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $model = Cart::class;

    public function all()
    {
        return response(Cart::paginate());
    }

    public function getOne(Cart $model)
    {
        return response($model);
    }

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
            $validatedData['name'] = Carbon::now()->toDateString() . " (" .  $validatedData['product_count'] . " Products) - " . "$" . $validatedData['total_price'];
        }

        return response(Cart::create($validatedData), 201);
    }

    public function getHold(Request $request)
    {
        $validatedData = $request->validate([
            'cash_register_id' => 'required|int'
        ]);
        return response(Cart::where("cash_register_id", $validatedData['cash_register_id'])->get());
    }

    public function delete(Cart $model)
    {
        $model->delete();
        return response(['notification' => [
            'msg' => 'Success!',
            'type' => 'success'
        ]]);
    }
}
