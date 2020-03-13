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
            'price' => 'required|array',
            'bulk_action' => 'boolean',
            'qty' => 'numeric',
        ]);

        if ($validatedData['enabled']) {
            $validatedData['enabled'] = now();
        } else {
            $validatedData['enabled'] = null;
        }

        if ($validatedData['bulk_action'] && $validatedData['qty'] != 0) {
            for ($i = 1; $i <= $validatedData['qty']; $i++) {
                if ($i != 1) {
                    $validatedData['code']++;
                }
                $giftcard = Giftcard::create($validatedData);
            }
        } else {
            $giftcard = Giftcard::create($validatedData);
        }
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
            'enabled' => 'required',
            'price' => 'required|array'
        ]);

        $giftcard = Giftcard::findOrFail($validatedData['id']);
        $validatedData['enabled'] = now();

        if ($validatedData['enabled'] && !$giftcard['enabled']) {
            $validatedData['enabled'] = now();
        }

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
