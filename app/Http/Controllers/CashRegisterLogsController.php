<?php

namespace App\Http\Controllers;

use App\CashRegisterLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

        $validatedID = $request->validate([
            'id' => 'nullable|exists:cash_register_logs,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
    }

    public function close(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'closing_amount' => 'required|numeric',
            'closed_by' => 'required|exists:users,id',
        ]);
        $validatedData['status'] = 0;
        $validatedData['closing_time'] = Carbon::now();

        $user = User::find($validatedData['user_id']);
        $log = $user->open_register()->update($validatedData);
        return response($log, 200);
    }

    public function open(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_amount' => 'required|numeric',
            'opened_by' => 'required|exists:users,id',
        ]);
        $validatedData['status'] = 1;
        $validatedData['opening_time'] = Carbon::now();
        if (!empty($this->getAlreadyOpenedRegister($validatedData['cash_register_id']))) {
            return response('Cash register already open', 500);
        }

        $user = User::find($validatedData['user_id']);
        if (!empty($user->open_register)) {
            return response('You already have an open cash register', 500);
        }
        return response($this->model::store($validatedData), 201);
    }

    public function getAlreadyOpenedRegister($cash_register_id)
    {
        return CashRegisterLogs::whereStatus(1)
            ->whereCashRegisterId($cash_register_id)->first();
    }
}
