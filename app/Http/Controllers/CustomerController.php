<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // protected $crud;

    // public function __construct(\CRUDController $CRUDController)
    // {
    //     $this->crud = $CRUDController;
    // }

    public function getAll()
    {
        return response(Customer::allData());
    }

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
