<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;

class CustomerController extends Controller
{
    public function getAll()
    {
        return response(Customer::all());
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
            'email' => 'required|email|unique:customers,email'
        ]);

        $model = new Customer($validatedData);
        $model->save();

        return response('Customer created successfully!', 201);
    }
}
