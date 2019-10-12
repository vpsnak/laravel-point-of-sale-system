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

        $validatedID = $request->validate([
            'id' => 'nullable|exists:cash_registers,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
    }
}
