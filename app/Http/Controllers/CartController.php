<?php

namespace App\Http\Controllers;

use App\Cart;
use Illuminate\Http\Request;

class CartController extends BaseController
{
    protected $model = Cart::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            // 'customer_id' => 'required|exists:id,customers',
            // 'name' => 'required|string',
            'data' => 'required|json',
        ]);

        // var_dump($request->all());
        // die;

        $validatedData['customer_id'] = 1;
        $validatedData['name'] = 'asdasd';


        return response($this->model::store($validatedData), 201);
    }

    public function getHold(Request $request)
    {
        $validatedData = $request->validate([
            'user' => 'required|int'
        ]);

        return response($this->model::getAll('customer_id', $validatedData['user']));
    }

    public function delete($id)
    {
        $deleted = $this->model::deleteData($id);
        return response($deleted, 200);
    }
}
