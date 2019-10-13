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
            'name' => 'required|string',
            'type' => 'required|boolean',
            'status' => 'required|boolean',
            'is_default' => 'required|boolean',
            'created_by' => 'required|exists:users,id',
        ]);

        $validatedID = $request->validate([
            'id' => 'nullable|exists:payment_types,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
    }
}
