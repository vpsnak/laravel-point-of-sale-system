<?php

namespace App\Http\Controllers;

use App\Giftcard;
use Illuminate\Http\Request;

class GiftcardController extends Controller
{
    public function all()
    {
        return response(Giftcard::with('createdBy')->paginate(10));
    }

    public function getOne(Giftcard $model)
    {
        return response($model);
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:giftcards,code',
            'enabled' => 'required|boolean',
            'price' => 'required|array',
            'price.amount' => 'required|integer',
            'price.currency' => 'required|string|size:3',
            'bulk_action' => 'required|boolean',
            'qty' => 'required|numeric|min:1',
        ]);
        $validatedData['original_price'] = $validatedData['price'];
        $validatedData['created_by_id'] = auth()->user()->id;
        $bulk = $validatedData['bulk_action'];
        $validatedData['enabled_at'] = $validatedData['enabled'] ? now() : null;
        $qty = $validatedData['qty'];

        unset($validatedData['bulk_action']);
        unset($validatedData['enabled']);
        unset($validatedData['qty']);


        if ($bulk) {
            $giftcardCollection = [];
            $j = substr($validatedData['code'], -1);
            for ($i = 0; $i < $qty; $i++) {
                $giftcardCollection[$i] = $validatedData;
                $giftcardCollection[$i]['price'] = json_encode($giftcardCollection[$i]['price']);
                $start_pos = -1 * strlen($j);
                $giftcardCollection[$i]['code'] = substr($validatedData['code'], 0, $start_pos);
                $giftcardCollection[$i]['code'] .= $j;
                $j++;
            }

            Giftcard::insert($giftcardCollection);
        } else {
            Giftcard::create($validatedData);
        }
        return response(['notification' => [
            'msg' => ["Giftcard created successfully"],
            'type' => 'success'
        ]]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:giftcards,id',
            'name' => 'required|string',
            'code' => "required|string|unique:giftcards,code,{$request->id}",
            'enabled' => 'required|boolean',
            'price' => 'required|array'
        ]);
        $giftcard = Giftcard::findOrFail($validatedData['id']);
        $validatedData['enabled_at'] = $validatedData['enabled'] ? now() : null;
        unset($validatedData['enabled']);

        $giftcard->update($validatedData);

        return response(['notification' => [
            'msg' => ["Gift card {$giftcard->name} updated successfully"],
            'type' => 'success'
        ]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string',
            'items' => 'nullable|integer|min:1'
        ]);

        $columns =  ['name', 'code'];
        $query = Giftcard::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate($validatedData['items'] ?? null));
    }
}
