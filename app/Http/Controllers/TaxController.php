<?php

namespace App\Http\Controllers;

use App\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function all()
    {
        return response(Tax::paginate(10));
    }

    public function getOne(Tax $model)
    {
        return response($model);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'percentage' => 'required|numeric|min:0|max:50',
        ]);
        $validatedData['created_by_id'] = auth()->user();
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
            'percentage' => 'required|numeric|min:0|max:50',
        ]);
        $tax = Tax::findOrFail($validatedData['id']);
        $tax->update($validatedData);

        return response(['notification' => [
            'msg' => ["Tax {$tax->name} updated successfully"],
            'type' => 'success'
        ]]);
    }
}
