<?php

namespace App\Http\Controllers;

use App\Giftcard;
use Illuminate\Http\Request;

class GiftcardController extends BaseController
{
    protected $model = Giftcard::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'enabled' => 'required|boolean',
            'amount' => 'required|numeric',
        ]);

        $validatedID = $request->validate([
            'id' => 'nullable|exists:giftcards,id'
        ]);

        if (!empty($validatedID)) {
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {
            return response($this->model::store($validatedData), 201);
        }
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        return $this->searchResult(
            ['name','code'],
            $validatedData['keyword'],
            true
        );
    }
}
