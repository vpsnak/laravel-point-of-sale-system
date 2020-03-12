<?php

namespace App\Http\Controllers;

use App\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function all()
    {
        return response(Coupon::paginate());
    }

    public function getOne(Coupon $model)
    {
        return response();
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'uses' => 'required|numeric',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'discount' => 'required|array',
        ]);

        $coupon = Coupon::create($validatedData);

        return response(['notification' => [
            'msg' => ["Coupon {$coupon->name} created successfully!"],
            'type' => 'success'
        ]]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:coupons,id',
            'name' => 'required|string',
            'code' => 'required|string',
            'uses' => 'required|numeric',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'discount' => 'required|array',
        ]);

        $coupon = Coupon::findOrFail($validatedData['id']);

        $coupon->fill($validatedData);
        $coupon->save();

        return response(['notification' => [
            'msg' => ["Coupon {$coupon->name} updated successfully!"],
            'type' => 'success'
        ]]);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        $columns = ['name', 'code'];
        $query = Coupon::query()->search($columns, $validatedData['keyword']);

        return response($query->paginate());
    }
}
