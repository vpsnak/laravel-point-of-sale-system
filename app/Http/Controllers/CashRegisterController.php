<?php

namespace App\Http\Controllers;

use App\CashRegister;
use Illuminate\Http\Request;

class CashRegisterController extends BaseController
{
    protected $model = CashRegister::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|alpha',
            'store_id' => 'required|exists:stores,id',
            'created_by' => 'required|exists:users,id',
        ]);

        return response($this->model::store($validatedData), 201);
    }
}
