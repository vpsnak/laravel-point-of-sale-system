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

    public function getOne($model)
    {
        return response(Tax::findOrFail($model));
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'percentage' => 'required|numeric',
        ]);

        $tax = Tax::create($validatedData);

        return response(['notification' => [
            'msg' => ["Tax {$tax->name} created successfully!"],
            'type' => 'success'
        ]], 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:taxes,id',
            'name' => 'required|string',
            'percentage' => 'required|numeric',
        ]);
        $tax = Tax::findOrFail($validatedData['id']);

        $tax->fill($validatedData);
        $tax->save();

        return response(['notification' => [
            'msg' => ["Tax {$tax->name} updated successfully!"],
            'type' => 'success'
        ]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['name'];
        $query = Tax::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
    }
}
