<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all()
    {
        return response(Product::with('stores')->get());
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string',
        ]);

        $response = $this->crud->create(Product::class, $validatedData);

        return $response;
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required'
        ]);

        return response(
            Product::where('sku', 'like', "%{$validatedData['keyword']}%")
                ->orWhere('name', 'like', "%{$validatedData['keyword']}%")
                ->get(),
            200
        );
    }
}
