<?php

namespace App\Http\Controllers;

use App\StorePickup;
use Illuminate\Http\Request;

class StorePickupController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'street' => 'required|string',
            'street1' => 'nullable|string',
            'region_id' => 'required|exists:regions,id',
        ]);

        return response($this->model::store($validatedData), 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:store_pickups,id',
            'name' => 'required|string',
            'street' => 'required|string',
            'street1' => 'nullable|string',
            'region_id' => 'required|exists:regions,id',
        ]);

        $storePickup = StorePickup::findOrFail($validatedData['id']);
        $storePickup->fill($validatedData);
        $storePickup->save();

        return response(['notification' => [
            "msg" => "Store pickup: {$storePickup->name} successfully updated!",
            "type" => "success"
        ]]);
    }

    public function all()
    {
        return response(StorePickup::with('region')->paginate());
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['name', 'street', 'street1', 'country_id', 'region_id'];
        $query = StorePickup::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
    }
}
