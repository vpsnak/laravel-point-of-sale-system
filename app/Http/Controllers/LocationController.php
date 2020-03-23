<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function all()
    {
        return response(Location::paginate());
    }

    public function getOne($model)
    {
        return response(Location::findOrFail($model));
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'icon' => 'nullable|string',
        ]);

        $location = Location::create($validatedData);

        return response(['notification' => [
            'msg' => ["Location {$location->name} created successfully!"],
            'type' => 'success'
        ]], 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:locations,id',
            'name' => 'required|string',
            'icon' => 'nullable|string',
        ]);
        $location = Location::findOrFail($validatedData['id']);

        $location->fill($validatedData);
        $location->save();

        return response(['notification' => [
            'msg' => ["Location {$location->name} updated successfully!"],
            'type' => 'success'
        ]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['name'];
        $query = Location::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
    }
}
