<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;

class StoreController extends BaseController
{
    protected $model = Store::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'taxable' => 'required|boolean',
            'is_default' => 'required|boolean',
            'tax_id' => 'required|exists:taxes,id',
            'created_by' => 'required|exists:users,id',
        ]);

        $validatedID = $request->validate([
            'id' => 'nullable|exists:stores,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
    }
}
