<?php

namespace App\Http\Controllers;

use App\CashRegisterLogs;
use Illuminate\Http\Request;

class CashRegisterLogsController extends BaseController
{
    protected $model = CashRegisterLogs::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_amount' => 'required|numeric',
            'closing_amount' => 'numeric',
            'status' => 'required|boolean',
            'opening_time' => 'required|date',
            'closing_time' => 'date',
            'opened_by' => 'required|exists:users,id',
            'closed_by' => 'exists:users,id',
            'note' => 'string',
        ]);

        return response($this->model::store($validatedData), 201);
    }
}
