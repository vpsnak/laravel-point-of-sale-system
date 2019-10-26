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
            'customer_id' => 'sometimes|exists:customers,id',
            'email' => 'required|email|unique:customers,email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        // $address = $request->validate([
        //     'address.first_name' => 'required|string',
        //     'address.last_name' => 'required|string',
        //     'address.street' => 'required|string',
        //     'address.city' => 'required|string',
        //     'address.country_id' => 'nullable|string',
        //     'address.region' => 'required|string',
        //     'address.postcode' => 'required|string',
        //     'address.phone' => 'required|numeric',
        // ]);

        if (array_key_exists('customer_id', $validatedData)) {
            $customer = $this->model::find($validatedData['customer_id']);
            unset($validatedData['customer_id']);
        } else {
            $customer = new Customer($validatedData);
        }

        $customer->save();

        // @TODO fix update
        return response($customer, 201);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required',
            'per_page' => 'nullable|numeric',
            'page' => 'nullable|numeric',
        ]);

        return $this->searchResult(
            ['first_name', 'last_name', 'email', 'phone'],
            $validatedData['keyword'],
            array_key_exists('per_page', $validatedData) ? $validatedData['per_page'] : 0,
            array_key_exists('page', $validatedData) ? $validatedData['page'] : 0
        );
    }
}
