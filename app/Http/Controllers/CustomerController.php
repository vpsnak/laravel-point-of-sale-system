<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class CustomerController extends BaseController
{
    protected $model = Customer::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:customers,email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        $validatedExtra = $request->validate([
            'id' => 'nullable|exists:customers,id',
            'address' => 'nullable|array',
        ]);

        $customer = $this->getCustomer($validatedExtra['id'] ?? null, $validatedData);

        if (!empty($validatedExtra['address'])) {
            $addressData = $request->validate([
                'address.first_name' => 'required|string',
                'address.last_name' => 'required|string',
                'address.street' => 'required|string',
                'address.street2' => 'nullable|string',
                'address.city' => 'required|string',
                'address.country_id' => 'required|exists:countries,country_id',
                'address.region' => 'required|exists:regions,region_id',
                'address.postcode' => 'required|string',
                'address.phone' => 'required|numeric',
                'address.company' => 'nullable|string',
                'address.vat_id' => 'nullable|string',
                'address.billing' => 'nullable|bool',
                'address.shipping' => 'nullable|bool',
            ]);

            $customer->addresses()->create($addressData['address']);
        }

        // @TODO fix update
        return response($customer, 201);
    }

    private function getCustomer($id, $data)
    {
        if (!empty($id)) {
            $this->model::updateData($id, $data);
            $model = $this->model::getOne($id);
        } else {
            $model = $this->model::store($data);
        }
        return $model;
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required'
        ]);

        return $this->searchResult(
            ['first_name', 'last_name', 'email'],
            $validatedData['keyword']
        );
    }
}
