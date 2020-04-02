<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function all()
    {
        return response(Store::with(['cashRegisters'])->paginate(10));
    }

    public function getOne(Store $model)
    {
        return response($model->load('cashRegisters'));
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'tax_id' => 'required|exists:taxes,id',
            'company_id' => 'required|exists:companies,id',
            'phone' => 'required|string',
            'street' => 'required|string',
            'postcode' => 'required|string',
            'city' => 'required|string',
            'active' => 'required|boolean',
            'default_currency' => 'required|string|size:3',
            'is_phone_center' => 'required|boolean'

        ]);
        $validatedData['created_by_id'] = auth()->user();
        $store = Store::create($validatedData);

        return response(['notification' => [
            'msg' => ["Store {$store->name} created successfully"],
            'type' => 'success'
        ]]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'nullable|exists:stores,id',
            'name' => 'required|string',
            'tax_id' => 'required|exists:taxes,id',
            'company_id' => 'required|exists:companies,id',
            'phone' => 'required|string',
            'street' => 'required|string',
            'postcode' => 'required|string',
            'city' => 'required|string',
            'active' => 'required|boolean',
            'default_currency' => 'required|string|size:3',
            'is_phone_center' => 'required|boolean'
        ]);
        $validatedData['user_id'] = auth()->user()->id;

        $store = Store::findOrFail($validatedData['id']);
        $store->update($validatedData);

        return response(['notification' => [
            'msg' => ["Store {$store->name} updated successfully"],
            'type' => 'success'
        ]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['name', 'phone', 'street', 'postcode'];
        $query = Store::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate(10));
    }
}
