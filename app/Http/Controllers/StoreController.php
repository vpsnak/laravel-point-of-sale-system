<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreController extends BaseController
{
    protected $model = Store::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'tax_id' => 'required|exists:taxes,id',
            'company_id' => 'required|exists:companies,id',
            'phone' => 'required|string',
            'street' => 'required|string',
            'postal_code' => 'required|string',
            'city' => 'required|string',
        ]);

        $validatedData['created_by'] = auth()->user()->id;

        $validatedID = $request->validate([
            'id' => 'nullable|exists:stores,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
    }

    /**
     * @return ResponseFactory|Response
     */
    public function all()
    {
        if (!isset($this->model)) {
            return response('Model not found', 404);
        }

        return response($this->model::with(['cash_registers'])->paginate(), 200);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        return $this->searchResult(
            ['name', 'phone', 'street', 'postal_code'],
            $validatedData['keyword'],
            true
        );
    }
}
