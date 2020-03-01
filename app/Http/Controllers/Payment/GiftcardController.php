<?php

namespace App\Http\Controllers;

use App\Giftcard;
use Illuminate\Http\Request;

class GiftcardController extends Controller
{
    public function all()
    {
        return response(Giftcard::paginate());
    }

    public function getOne(Giftcard $model)
    {
        return response($model);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'enabled' => 'required|boolean',
            'amount' => 'required|numeric',
        ]);

        $giftcard = Giftcard::create($validatedData);

        return response(['notification' => [
            'msg' => ["Gift card {$giftcard->name} created successfully!"],
            'type' => 'success'
        ]]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:giftcards,id',
            'name' => 'required|string',
            'code' => 'required|string',
            'enabled' => 'required|boolean',
            'amount' => 'required|numeric',
        ]);
        $giftcard = Giftcard::findOrFail($validatedData['id']);

        $giftcard->fill($validatedData);
        $giftcard->save();

        return response(['notification' => [
            'msg' => ["Gift card {$giftcard->name} updated successfully!"],
            'type' => 'success'
        ]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns =  ['name', 'code'];
        $query = Giftcard::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
    }
}
