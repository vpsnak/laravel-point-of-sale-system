<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    protected $model = Category::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'in_product_listing' => 'required|boolean',
        ]);

        $validatedID = $request->validate([
            'id' => 'nullable|exists:categories,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
    }

    public function productListingCategories()
    {
        return response(Category::paginate(), 200);
    }

    public function productsByCategory(Category $category)
    {
        $products = Product::whereHas('categories', function ($query) use ($category) {
            $query->where('id', $category->id);
        })->paginate();

        return response($products, 200);
    }
}
