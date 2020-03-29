<?php

namespace App\Http\Controllers;

use App\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function all()
    {
        $carts = auth()->user()->carts();
        return response($carts->paginate());
    }

    public function getOne(Cart $model)
    {
        return response($model);
    }

    public function count()
    {
        $carts = auth()->user()->carts();
        return response($carts->count());
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'cart' => 'required|array',
        ]);
        $user = auth()->user();
        $validatedData['created_by_id'] = $user->id;
        Cart::create($validatedData);

        $carts = auth()->user()->carts();
        $notification = [
            'msg' => "Cart added on hold list",
            'type' => "info"
        ];

        return response([
            'count' => $carts->count(),
            'notification' => $notification
        ], 201);
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
