<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    protected $model = Product::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string',
        ]);

        return response($this->model::store($validatedData), 201);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string',
            'per_page' => 'nullable|numeric',
            'page' => 'nullable|numeric',
        ]);

        return $this->searchResult(['sku', 'name'],
            $validatedData['keyword'],
            array_key_exists('per_page', $validatedData) ? $validatedData['per_page'] : 0,
            array_key_exists('page', $validatedData) ? $validatedData['page'] : 0
        );
    }
}
