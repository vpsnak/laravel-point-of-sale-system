<?php

namespace App\Http\Controllers;

use App\Tax;
use Illuminate\Http\Request;

class TaxController extends BaseController
{
    protected $model = Tax::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'percentage' => 'required|numeric',
        ]);


        $validatedID = $request->validate([
            'id' => 'nullable|exists:taxes,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
    }
}
