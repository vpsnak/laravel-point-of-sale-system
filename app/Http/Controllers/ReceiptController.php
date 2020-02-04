<?php

namespace App\Http\Controllers;

use App\Receipt;
use App\Order;
use App\User;
use App\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ReceiptController extends Controller
{
    protected $model = Receipt::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:print,email',
            'issued_by' => 'required|integer',
            'content' => 'required',
            'order_id' => 'required|integer',
            'cash_register_id' => 'required|integer',
        ]);
        if ($validatedData['status'] === 'print') {
            $validatedData['print_count'] = 1;
        } elseif ($validatedData['status'] === 'email') {
            $validatedData['email_count'] = 1;
        }
        $order = Order::findOrFail($validatedData['order_id']);
        $user = User::findOrFail($validatedData['issued_by']);
        $cash_register = CashRegister::findOrFail($validatedData['cash_register_id']);
        $validatedData['content'] = [$order, $user, $cash_register];
        $receipt = Receipt::create($validatedData);

        return response(['info' => ['Receipt ' . $receipt->id . ' created successfully!']], 201);
    }

    public function all()
    {
        return response($this->model::paginate(), 200);
    }

    public function get($id)
    {
        return response($this->model::find($id), 200);
    }
}
