<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Card;

class CardController extends Controller
{
    public function all()
    {
        return response(Card::paginate());
    }

    public function getOne(Card $model)
    {
        return response($model);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'cardable_id' => 'required|int',
            'cardable_type' => 'required|string'
        ]);

        $this->user = auth()->user();
        $validatedData['created_by'] = $this->user->id;

        $card = Card::create($validatedData);

        return response(['notification' => [
            'msg' => ["Card {$card->name} created successfully!"],
            'type' => 'success'
        ]], 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:cards,id',
            'title' => 'required|string',
            'content' => 'required|string',
            'cardable_id' => 'required|int',
            'cardable_type' => 'required|string'
        ]);
        $card = Card::findOrFail($validatedData['id']);

        $card->fill($validatedData);
        $card->save();

        return response(['notification' => [
            'msg' => ["Card {$card->name} updated successfully!"],
            'type' => 'success'
        ]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['title'];
        $query = Card::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
    }
}
