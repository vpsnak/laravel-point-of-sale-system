<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function all()
    {
        return response(Company::paginate());
    }

    public function getOne($model)
    {
        return response(Company::findOrFail($model));
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'tax_number' => 'required|string',
        ]);

        $company = Company::create($validatedData);

        return response(['notification' => [
            'msg' => ["Company {$company->name} created successfully!"],
            'type' => 'success'
        ]], 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:companies,id',
            'name' => 'required|string',
            'tax_number' => 'required|string',
        ]);
        $company = Company::findOrFail($validatedData['id']);

        $company->fill($validatedData);
        $company->save();

        return response(['notification' => [
            'msg' => ["Company {$company->name} updated successfully!"],
            'type' => 'success'
        ]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['name'];
        $query = Company::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
    }
}
