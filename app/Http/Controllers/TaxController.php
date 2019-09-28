<?php

namespace App\Http\Controllers;

use App\Tax;
use Illuminate\Http\Request;

class TaxController extends BaseController
{
    protected $model = Tax::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|alpha',
            'percentage' => 'required|numeric',
            'is_default' => 'required|boolean',
        ]);

        return response($this->model::store($validatedData), 201);
    }
}
