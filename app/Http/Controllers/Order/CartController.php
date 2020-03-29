<?php

namespace App\Http\Controllers;

use App\Cart;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function all()
    {
        $user = auth()->user();
        return response(Cart::getUserCarts($user)->paginate());
    }

    public function getOne(Cart $model)
    {
        return response($model);
    }

    public function count()
    {
        $user = auth()->user();
        return response(Cart::getUserCarts($user)->count());
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'name' => 'nullable|string',
            'cart' => 'required|array',
        ]);
        $user = auth()->user();
        $store = $user->open_register->cash_register->store;
        $validatedData['store_id'] = $store->id;
        $validatedData['created_by_id'] = $user->id;
        Cart::create($validatedData);

        $notification = [
            'msg' => "Cart added on hold list",
            'type' => "info"
        ];

        return response([
            'notification' => $notification,
            'count' => Cart::getUserCarts($user)->count()
        ], 201);
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
            'msg' => 'Cart deleted successfully',
            'type' => 'success'
        ]]);
    }
}
