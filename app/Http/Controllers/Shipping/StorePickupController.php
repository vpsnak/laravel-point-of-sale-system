<?php

namespace App\Http\Controllers;

use App\StorePickup;
use Illuminate\Http\Request;

class StorePickupController extends BaseController
{
    protected $model = StorePickup::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'street' => 'required|string',
            'street1' => 'nullable|string',
            'country_id' => 'required|exists:countries,country_id',
            'region_id' => 'required|exists:regions,region_id',
        ]);

        $validatedID = $request->validate([
            'id' => 'nullable|exists:store_pickups,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
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

        return $this->searchResult(
            ['name', 'street', 'street1', 'country_id', 'region_id'],
            $validatedData['keyword'],
            true
        );
    }
}
