<?php

namespace App\Http\Controllers;

use App\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function all()
    {
        $carts = auth()->user()->carts();
        return response($carts->orderBy('id', 'desc')->paginate(3));
    }

    public function getOne(Cart $model)
    {
        if ($model->created_by_id === auth()->user()->id) {
            return response($model);
        } else {
            return response([
                'notification' => [
                    'msg' => ['You cannot retrieve this cart'],
                    'type' => 'error'
                ]
            ], 422);
        }
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

        $carts = $user->carts();
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
        if ($model->created_by_id === auth()->user()->id) {
            $model->delete();
            return response(['notification' => [
                'msg' => 'Cart deleted successfully',
                'type' => 'success'
            ]]);
        } else {
            return response(['notification' => [
                'msg' => 'You cannot delete this cart',
                'type' => 'error'
            ]], 422);
        }
    }
}
