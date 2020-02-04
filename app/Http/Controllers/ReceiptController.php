<?php

namespace App\Http\Controllers;

use App\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ReceiptController extends Controller
{
    protected $model = Receipt::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'print_count' => 'required|integer',
            'email_count' => 'required|integer',
            'issued_by' => 'required|integer',
            'content' => 'required',
            'order_id' => 'required|integer',
            'cash_register_id' => 'required|integer',
        ]);

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
