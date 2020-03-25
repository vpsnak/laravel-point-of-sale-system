<?php

namespace App\Http\Controllers;

use App\CashRegister;
use App\CashRegisterLogs;
use App\Events\CashRegisterLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CashRegisterLogsController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_amount' => 'required|numeric',
            'closing_amount' => 'numeric',
            'status' => 'required|boolean',
            'opening_time' => 'required|date',
            'closing_time' => 'date',
            'note' => 'string',
        ]);
        $validatedData['user_id'] = $validatedData['opened_by_id'] = auth()->user()->id;

        $validatedID = $request->validate([
            'id' => 'nullable|exists:cash_register_logs,id'
        ]);

        if (!empty($validatedID)) {
            return response(CashRegisterLogs::updateData($validatedID, $validatedData));
        } else {
            return response(CashRegisterLogs::store($validatedData), 201);
        }
    }

    public function close(Request $request)
    {
        $validatedData = $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
        ]);

        $validatedData['closing_amount'] = auth()->user()->open_register->cash_register->earnings()['cash_total'];
        $validatedData['closed_by_id'] = auth()->user()->id;
        $validatedData['status'] = 0;
        $validatedData['closing_time'] = Carbon::now();

        auth()->user()->open_register()->update($validatedData);
        $report = CashRegisterReportController::generateReportByCashRegisterId($validatedData['cash_register_id']);

        return response([
            'report' => $report,
            'notification' => [
                'msg' => ['Cash register closed successfully!'],
                'type' => 'success'
            ]
        ]);
    }

    public function logout()
    {
        $user = auth()->user();
        $user = $user->load('open_register');

        if ($user->open_register) {
            $user->open_register->user_id = null;
            $user->open_register->save();

            return response([
                'notification' => [
                    'msg' => ["Logged out successfully"],
                    'type' => 'success',
                ]
            ]);
        } else {
            return response(['errors' => ["{$user->name} wasn't assigned to any cash register"]], 422);
        }
    }

    public function retrieve()
    {
        $user = auth()->user();

        if ($user->open_register) {
            $cash_register =  $user->open_register->cash_register;
            $store = $cash_register->store;

            return response([
                'notification' => [
                    'msg' => ["Your open session with cash register: <b>{$cash_register->name}</b> has been restored"],
                    'type' => 'success'
                ],
                'cash_register' => $cash_register,
                'store_name' => $store->name,
                'tax_percentage' => $store->tax->percentage,
            ]);
        }
    }

    public function amount(CashRegister $model)
    {
        return response(
            $model->logs()->where('status', true)->first()->cash_register->earnings()['cash_total']
        );
    }

    public function open(Request $request)
    {
        $validatedData = $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'opening_amount' => 'nullable|numeric',
        ]);

        $user = auth()->user();
        //        @TODO refactor

        $cash_register = CashRegister::findOrFail($validatedData['cash_register_id']);
        if ($cash_register->is_open) {
            $response = $this->handleOpenRegister($cash_register->getOpenLog());
        } else {
            $validatedData['user_id'] = $user->id;
            $validatedData['opened_by_id'] = $user->id;
            $validatedData['status'] = 1;
            $validatedData['opening_time'] = Carbon::now();

            $cash_register_log = CashRegisterLogs::create($validatedData);
            $cash_register = $cash_register_log->cash_register;
            $store = $cash_register->store;

            $response = [
                'notification' => [
                    'msg' => ['Your session with cash register is active'],
                    'type' => 'success'
                ],
                'cash_register' => $cash_register,
                'store_name' => $store->name,
                'tax_percentage' => $store->tax->percentage,
            ];
        }


        return response($response, 201);
    }

    private function handleOpenRegister(CashRegisterLogs $cash_register_log)
    {
        $user = auth()->user();

        // user isn't assigned to a register
        if (empty($user->open_register)) {

            if ($cash_register_log->user_id !== $user->id) {
                event(new CashRegisterLogin($cash_register_log, $user));
            }

            $cash_register_log->user_id = $user->id;
            $cash_register_log->save();
            $cash_register = $cash_register_log->cash_register;
            $store = $cash_register->store;

            return [
                'notification' => [
                    'msg' => ['Logged in successfully!'],
                    'type' => 'success'
                ],
                'cash_register' => $cash_register,
                'store_name' => $store->name,
                'tax_percentage' => $store->tax->percentage,
            ];
        }

        // user now have already an open register so we remove him
        // so kick remove current user from the previous register to open this one if needed
        else if ($user->open_register->user_id === $user->id) {
            $user->open_register->user_id = null;
            $user->open_register->save();
        }
        $cash_register = $cash_register_log->cash_register;
        $store = $cash_register->store;

        return [
            'notification' => [
                'msg' => ['User cash register changed'],
                'type' => 'success'
            ],
            'cash_register' => $cash_register,
            'store_name' => $store->name,
            'tax_percentage' => $store->tax->percentage,
        ];
    }
}
