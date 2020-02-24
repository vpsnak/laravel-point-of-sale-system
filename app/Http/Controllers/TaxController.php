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

    public function get($model)
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
        
        return response(['info' => ['Tax ' . $tax->name . ' created successfully!']], 201);

        $validatedID = $request->validate([
            'id' => 'nullable|exists:taxes,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
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

        return response(['info' => ["Tax {$tax->name} updated successfully!"]]);
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
