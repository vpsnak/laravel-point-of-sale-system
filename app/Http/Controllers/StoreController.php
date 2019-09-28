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
            'name' => 'required|alpha',
            'taxable' => 'required|boolean',
            'is_default' => 'required|boolean',
            'tax_id' => 'required|exists:taxes,id',
            'created_by' => 'required|exists:users,id',
        ]);

        return response($this->model::store($validatedData), 201);
    }
}
