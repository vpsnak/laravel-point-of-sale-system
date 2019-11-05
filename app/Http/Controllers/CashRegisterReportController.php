<?php

namespace App\Http\Controllers;

use App\CashRegisterReport;
use Illuminate\Http\Request;

class CashRegisterReportController extends BaseController
{
    protected $model = CashRegisterReport::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'created_by' => 'required|exists:users,id', // @TODO remove this from here and use auth()
            'cash_register_id' => 'required|exists:cash_registers,id',
            'type' => 'required|in:x,z',
        ]);
//        @TODO calculate report logic in different function
    }
}
