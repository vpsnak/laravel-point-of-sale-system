<?php

namespace App\Http\Controllers;

use App\Address;

class AddressController extends BaseController
{
    protected $model = Address::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'address.first_name' => 'required|string',
            'address.last_name' => 'required|string',
            'address.street' => 'required|string',
            'address.city' => 'required|string',
            'address.country_id' => 'nullable|string',
            'address.region' => 'required|string',
            'address.postcode' => 'required|string',
            'address.phone' => 'required|numeric',
        ]);

        return response('HUHUHUHUHU');
    }
}
