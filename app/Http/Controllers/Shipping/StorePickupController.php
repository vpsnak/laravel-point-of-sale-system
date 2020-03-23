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
            'street2' => 'nullable|string',
            'city' => 'required|string',
            'region_id' => 'required|exists:regions,id',
            'postcode' => 'required|numeric|size:5',
            'phone' => 'required|string',
            'company' => 'required|string',
            'location_id' => 'nullable|numeric|between:1,12',
        ]);

        $storePickup = StorePickup::create($validatedData);

        return response(['notification' => [
            'msg' => ["Store pickup {$storePickup->name} created successfully!"],
            'type' => 'success'
        ]]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:store_pickups,id',
            'name' => 'required|string',
            'street' => 'required|string',
            'street2' => 'nullable|string',
            'city' => 'required|string',
            'region_id' => 'required|exists:regions,id',
            'postcode' => 'required|numeric|size:5',
            'phone' => 'required|string',
            'company' => 'required|string',
            'location_id' => 'nullable|numeric|between:1,12',
        ]);

        $storePickup = StorePickup::findOrFail($validatedData['id']);
        $storePickup->update($validatedData);

        return response(['notification' => [
            "msg" => "Store pickup: {$storePickup->name} successfully updated!",
            "type" => "success"
        ]]);
    }

    public function all()
    {
        return response(StorePickup::paginate());
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

    public function getOne(StorePickup $model)
    {
        return response($model);
    }
}
