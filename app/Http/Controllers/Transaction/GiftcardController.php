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
            'bulk' => 'required|boolean',
            'qty' => 'required|numeric|min:1',
        ]);
        $validatedData['created_by_id'] = auth()->user()->id;
        $bulk = $validatedData['bulk'];
        $qty = $validatedData['qty'];

        if ($validatedData['enabled']) {
            $validatedData['enabled_at'] = now();
        }
        unset($validatedData['bulk']);
        unset($validatedData['enabled']);
        unset($validatedData['qty']);


        if ($bulk) {
            $giftcardCollection = [];
            for ($i = 0; $i < $qty; $i++) {
                $giftcardCollection[$i] = $validatedData;
                $start_pos = -1 * strlen($i);
                $giftcardCollection[$i]['code'] = substr($validatedData['code'], 0, $start_pos);
                $giftcardCollection[$i]['code'] .= $i;
            }
            Giftcard::insert($giftcardCollection);
        } else {
            Giftcard::create($validatedData);
        }
        return response(['notification' => [
            'msg' => ["Gift card created successfully!"],
            'type' => 'success'
        ]]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'enabled' => 'required|boolean',
            'price' => 'required|array'
        ]);

        $giftcard = Giftcard::findOrFail($validatedData['id']);
        $validatedData['enabled'] = now();

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
