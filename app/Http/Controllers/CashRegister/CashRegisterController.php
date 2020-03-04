<?php

namespace App\Http\Controllers;

use App\CashRegister;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    public function all()
    {
        return response(CashRegister::paginate());
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
            'active' => 'required|boolean',
            'barcode' => 'string',
            'pos_terminal_ip' => 'required|ip',
            'pos_terminal_port' => 'required|string'

        ]);
        $validatedData['user_id'] = auth()->user()->id;

        $cashRegister = CashRegister::create($validatedData);

        return response(['notification' => [
            'msg' => ["Cash register {$cashRegister->name} created successfully!"],
            'type' => 'success'
        ]]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'nullable|exists:cash_registers,id',
            'name' => 'required|string',
            'store_id' => 'required|exists:stores,id',
            'active' => 'required|boolean',
            'barcode' => 'string',
            'pos_terminal_ip' => 'required|ip',
            'pos_terminal_port' => 'required|string'
        ]);
        $validatedData['user_id'] = auth()->user()->id;
        $cashRegister = CashRegister::findOrFail($validatedData['id']);

        $cashRegister->fill($validatedData);
        $cashRegister->save();

        return response(['notification' => [
            'msg' => ["Cash register {$cashRegister->name} updated successfully!"],
            'type' => 'success'
        ]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['name', 'barcode'];
        $query = CashRegister::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
    }
}
