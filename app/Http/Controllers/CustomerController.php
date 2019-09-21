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
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
            'email' => 'required|email|unique:customers,email'
        ]);

        $response = $this->crud->create(Customer::class, $validatedData);

        return $response;
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required'
        ]);

        return response(Product::where('name', 'like', "%{$validatedData['keyword']}%")
            ->orWhere('email', 'like', "%{$validatedData['keyword']}%")
            ->orWhere('phone', 'like', "%{$validatedData['keyword']}%")
            ->get(), 200);
    }
}
