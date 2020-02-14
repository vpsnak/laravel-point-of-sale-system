<?php

namespace App\Http\Controllers;

use App\Address;
use Illuminate\Http\Request;
use App\Customer;

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
            'is_default_billing' => 'nullable|bool',
            'is_default_shipping' => 'nullable|bool',
            'location' => 'nullable|numeric|between:1,12',
            'location_name' => 'nullable|string'
        ]);

        $validatedID = $request->validate([
            'id' => 'nullable|exists:addresses,id'
        ]);

        $customerID = $request->validate([
            'customer_id' => 'nullable|exists:customers,id'
        ]);

        if (!empty($validatedID)) {
            $address = $this->model::updateData($validatedID['id'], $validatedData);
            $address = Address::findOrFail($validatedID['id']);

            return response(['address' => $address, 'info' => ["Address with id: $address->id successfully updated!"]], 200);
        } else {
            $address = $this->model::store($validatedData);

            if (!empty($customerID)) {
                $customer = Customer::findOrFail($customerID['customer_id']);
                $customer->addresses()->attach($address->id);
            }

            return response(['address' => $address, 'info' => ["Address successfully created!"]], 201);
        }
    }
}
