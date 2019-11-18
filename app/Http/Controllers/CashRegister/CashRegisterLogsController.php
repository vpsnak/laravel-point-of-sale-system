<?php

namespace App\Http\Controllers;

use App\CashRegister;
use App\CashRegisterLogs;
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
            'opening_amount' => 'nullable|numeric',
        ]);

        $user = auth()->user();
//        @TODO refactor
        if (empty($user)) {
            return response('Unauthorized!', 401);
        }

        $cash_register = CashRegister::getOne($validatedData['cash_register_id']);
        if ($cash_register->is_open) {
            $validatedData['user_id'] = $user->id;
            return $this->handleOpenRegister($cash_register->getOpenLog());
        } else {
            $validatedData['user_id'] = $user->id;
            $validatedData['opened_by'] = $user->id;
            $validatedData['status'] = 1;
            $validatedData['opening_time'] = Carbon::now();

            $log = $this->model::store($validatedData);

            return response([
                'info' => 'Cash register opened successfully',
                'cashRegister' => $this->model::getOne($log->id)
            ], 201);
        }
    }

    private function handleOpenRegister(CashRegisterLogs $cash_register_log)
    {
        // an other user is online on the requested register
        if (empty($cash_register_log->user_id)) {
            return response([
                'error' => 'An other user is using this register atm',
                'cashRegister' => $cash_register_log
            ], 400);
        }

        $user = auth()->user();
        // user isn't assigned to a register
        if (empty($user->open_register)) {
            $cash_register_log->user_id = $user->id;
            $cash_register_log->save();
            return response([
                'info' => 'User assigned to this cash register',
                'cashRegister' => $cash_register_log
            ], 200);
        }

        // if user already have open register and its the same as this one return it
        if ($user->open_register->id == $cash_register_log->id) {
            return response([
                'info' => 'User is already assigned to this cash register',
                'cashRegister' => $user->open_register
            ], 200);
        }

        // user now have already an open register so we remove him
        // so kick remove current user from the previous register to open this one if needed
        if ($user->open_register->user_id == $user->id) {
            $user->open_register->user_id = null;
            $user->open_register->save();
        }

        // open cash requested cash register
        $cash_register_log->user_id = $user->id;
        $cash_register_log->save();

        return response([
            'info' => 'User cash register changed',
            'cashRegister' => $cash_register_log
        ], 200);
    }
}