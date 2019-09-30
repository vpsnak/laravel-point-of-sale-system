<?php

namespace App\Http\Controllers;

use App\Category;

class CategoryController extends BaseController
{
    protected $model = Category::class;
    
    public function productListingCategories()
    {
        return response(Category::all(), 200);
        return response(Category::getAllPaginate('in_product_listing', 1, 2), 200);
    }
}
