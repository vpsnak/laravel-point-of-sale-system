<?php

namespace App\Http\Controllers;

use App\Address;
use Illuminate\Http\Request;

class AddressController extends BaseController
{
    protected $model = Address::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'street' => 'required|string',
            'city' => 'required|string',
            'country_id' => 'nullable|string',
            'region' => 'required|string',
            'postcode' => 'required|string',
            'phone' => 'required|numeric',
        ]);

        $validatedID = $request->validate([
            'id' => 'nullable|exists:address,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
    }
}
