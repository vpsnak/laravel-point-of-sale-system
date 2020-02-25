<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function all()
    {
        return response(Category::paginate());
    }

    public function getOne($model)
    {
        return response(Category::findOrFail($model));
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'in_product_listing' => 'required|boolean',
        ]);

        $category = Category::create($validatedData);

        return response(['notification' => [
            'msg' => ["Category {$category->name} created successfully!"],
            'type' => 'success'
        ]]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'in_product_listing' => 'required|boolean',
        ]);
        $category = Category::findOrFail($validatedData['id']);

        $category->fill($validatedData);
        $category->save();

        return response(['notification' => [
            'msg' => ["Category {$category->name} updated successfully!"],
            'type' => 'success'
        ]]);
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

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['name', 'in_product_listing'];
        $query = Category::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
    }
}
