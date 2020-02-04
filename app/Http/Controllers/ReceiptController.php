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
            'order_id' => 'required|integer',
        ]);
        $validatedData['print_count'] = 1;
        $validatedData['email_count'] = 1;
        $validatedData['issued_by'] = auth()->user()->id;
        $validatedData['cash_register_id'] = auth()->user()->open_register->cash_register->id;
        $order = Order::findOrFail($validatedData['order_id']);
        $user = User::findOrFail($validatedData['issued_by']);
        $cash_register = CashRegister::findOrFail($validatedData['cash_register_id']);
        $validatedData['content'] = [$order, $user, $cash_register];
        $receipt = Receipt::create($validatedData);

        return response(['info' => ['Receipt ' . $receipt->id . ' for order ' . $order->id . ' created successfully!']], 201);
    }

    public function print_receipt(Order $order)
    {
        return view('receipt')->with($order);
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
