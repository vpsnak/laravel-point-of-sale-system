<?php

namespace App\Http\Controllers;

use App\CashRegister;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    public function all()
    {
        return response(CashRegister::all());
    }

    public function getOne(CashRegister $model)
    {
        return response($model);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'store_id' => 'required|exists:stores,id',
            'active' => 'required|boolean'
        ]);
        $validatedData['user_id'] = auth()->user()->id;

        return response(CashRegister::create($validatedData), 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'nullable|exists:cash_registers,id',
            'name' => 'required|string',
            'store_id' => 'required|exists:stores,id',
            'active' => 'required|boolean'
        ]);
        $validatedData['user_id'] = auth()->user()->id;
        $cashRegister = CashRegister::findOrFail($validatedData['id']);

        return response([
            'data' => $cashRegister,
            'notification' => [
                'msg' => "Cash register: {$cashRegister->name} updated successfully!"
            ]
        ]);
    }
}
