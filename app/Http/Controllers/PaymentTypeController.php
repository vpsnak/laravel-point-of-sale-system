<?php

namespace App\Http\Controllers;

use App\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends BaseController
{
    protected $model = PaymentType::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|alpha',
            'type' => 'required|boolean',
            'status' => 'required|boolean',
            'is_default' => 'required|boolean',
            'created_by' => 'required|exists:users,id',
        ]);

        return response($this->model::store($validatedData), 201);
    }
}
