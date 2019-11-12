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
            'street2' => 'nullable|string',
            'city' => 'required|string',
            'country_id' => 'required|exists:countries,country_id',
            'region' => 'required|exists:regions,region_id',
            'postcode' => 'required|string',
            'phone' => 'required|numeric',
            'company' => 'nullable|string',
            'vat_id' => 'nullable|string',
            'billing' => 'nullable|bool',
            'shipping' => 'nullable|bool',
        ]);

        $validatedID = $request->validate([
            'id' => 'nullable|exists:addresses,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
    }
}
