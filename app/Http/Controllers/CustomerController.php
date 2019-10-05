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
            'phone' => 'required|numeric|unique:customers,phone',
            'company_name' => 'required|string',
        ]);

        $address = $request->validate([
            'addresses.area_code_id' => 'required|string',
            'addresses.first_name' => 'required|string',
            'addresses.last_name' => 'required|string',
            'addresses.street' => 'required|string',
            'addresses.city' => 'required|string',
            'addresses.country_id' => 'nullable|string',
            'addresses.region' => 'required|string',
            'addresses.postcode' => 'required|string',
            'addresses.phone' => 'required|numeric',
        ]);

//        $customer = $this->model::store($validatedData);
        $customer = $this->model::find(1);
//        dd($address['addresses']);
        $customer->addresses()->create($address['addresses']);

        return response($customer, 201);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required',
            'per_page' => 'nullable|numeric',
            'page' => 'nullable|numeric',
        ]);

        return $this->searchResult(['first_name', 'last_name', 'email', 'phone'],
            $validatedData['keyword'],
            array_key_exists('per_page', $validatedData) ? $validatedData['per_page'] : 0,
            array_key_exists('page', $validatedData) ? $validatedData['page'] : 0
        );
    }
}
