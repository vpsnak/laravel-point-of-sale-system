<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function all()
    {
        return response(Store::with(['cash_registers'])->get());
    }

    public function getOne(Store $model)
    {
        return response($model->load('cash_registers'));
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
        ]);
        $validatedData['created_by'] = auth()->user()->id;

        $store = Store::create($validatedData);

        return response([
            'data' => $store,
            'notification' => [
                'msg' => "Store {$store->name} created successfully!",
                'type' => 'success'
            ]
        ], 201);
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
        ]);
        $validatedData['created_by'] = auth()->user()->id;

        $store = Store::findOrFail($validatedData['id']);
        $store->fill($validatedData);
        $store->save();

        return response([
            'data' => $store,
            'notification' => [
                'msg' => "Store {$store->name} updated successfully!",
                'type' => 'success'
            ]
        ]);
    }

    // @TODO Search?
    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        return $this->searchResult(
            ['name', 'phone', 'street', 'postcode'],
            $validatedData['keyword'],
            true
        );
    }
}
