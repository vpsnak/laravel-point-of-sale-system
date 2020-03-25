<?php

namespace App\Http\Controllers;

use App\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function all()
    {
        return response(Tax::paginate());
    }

    public function getOne(Tax $model)
    {
        return response($model);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'percentage' => 'required|numeric',
            'created_by_id' => 'required|exists:users,id'
        ]);

        $tax = Tax::create($validatedData);

        return response(['notification' => [
            'msg' => ["Tax {$tax->name} created successfully"],
            'type' => 'success'
        ]], 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:taxes,id',
            'name' => 'required|string',
            'percentage' => 'required|numeric',
            'created_by_id' => 'required|exists:users,id'
        ]);
        $tax = Tax::findOrFail($validatedData['id']);
        $tax->update($validatedData);

        return response(['notification' => [
            'msg' => ["Tax {$tax->name} updated successfully"],
            'type' => 'success'
        ]]);
    }
}
