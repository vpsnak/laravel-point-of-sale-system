<?php

namespace App\Http\Controllers;

use App\Occasion;
use Illuminate\Http\Request;

class OccasionController extends Controller
{
    protected $model = Occasion::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
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
        return response($this->model::all());
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
