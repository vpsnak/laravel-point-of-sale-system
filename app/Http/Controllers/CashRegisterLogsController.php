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
            // 'user_id' => 'required|exists:users,id',
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_amount' => 'required|numeric',
            'closing_amount' => 'numeric',
            'status' => 'required|boolean',
            'opening_time' => 'required|date',
            'closing_time' => 'date',
            // 'opened_by' => 'required|exists:users,id',
            // 'closed_by' => 'exists:users,id',
            'note' => 'string',
        ]);

        $validatedData['opened_by'] = auth()->user()->id;
        $validatedData['user_id'] = auth()->user()->id;

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
            'cash_register_id' => 'required|exists:cash_registers,id',
            'closing_amount' => 'required|numeric',
        ]);

        $validatedData['closed_by'] = auth()->user()->id;
        $validatedData['status'] = 0;
        $validatedData['closing_time'] = Carbon::now();

        $log = auth()->user()->open_register()->update($validatedData);
        CashRegisterReportController::generateReportByCashRegisterId($validatedData['cash_register_id']);

        return response($log, 200);
    }

    public function open(Request $request)
    {
        $validatedData = $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_amount' => 'required|numeric',
            // 'opened_by' => 'required|exists:users,id',
        ]);

        $validatedData['user_id'] = auth()->user()->id;
        $validatedData['opened_by'] = auth()->user()->id;
        $validatedData['status'] = 1;
        $validatedData['opening_time'] = Carbon::now();
        if (!empty($this->getAlreadyOpenedRegister($validatedData['cash_register_id']))) {
            return response([
                'notification' => [
                    'msg' => 'Cash register already open',
                    'type' => 'error'
                ]
            ], 422);
        }

        $user = User::find($validatedData['user_id']);
        if (!empty($user->open_register)) {
            return response([
                'notification' => [
                    'msg' => 'You already have an open cash register',
                    'type' => 'error'
                ]
            ], 422);
        }

        return response($this->model::store($validatedData), 201);
    }

    public function getAlreadyOpenedRegister($cash_register_id)
    {
        return CashRegisterLogs::whereStatus(1)
            ->whereCashRegisterId($cash_register_id)->first();
    }
}
