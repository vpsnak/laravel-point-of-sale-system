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

        return response($this->model::store($validatedData), 201);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required'
        ]);

        return $this->searchResult(['name', 'email', 'phone'], $validatedData['keyword']);
    }
}
