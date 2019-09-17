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
}
