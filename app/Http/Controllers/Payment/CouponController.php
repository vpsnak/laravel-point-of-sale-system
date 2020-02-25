<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Discount;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function all()
    {
        return response(Coupon::paginate());
    }

    public function get($model)
    {
        return response(Coupon::findOrFail($model));
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'uses' => 'required|numeric',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $discountData = $request->validate([
            'discount.type' => 'required|in:flat,percentage',
            'discount.amount' => 'required|numeric',
        ]);

        $discount = Discount::store([
            'type' => $discountData['discount']['type'],
            'amount' => $discountData['discount']['amount'],
        ]);

        $coupon = $discount->coupon()->create($validatedData);

        return response(['info' => ['Coupon ' . $coupon->name . ' created successfully!']], 201);
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
        ]);

        $discountData = $request->validate([
            'discount.type' => 'required|in:flat,percentage',
            'discount.amount' => 'required|numeric',
        ]);

        $coupon = Coupon::findOrFail($validatedData['id']);

        $coupon->discount->type = $discountData['discount']['type'];
        $coupon->discount->amount = $discountData['discount']['amount'];

        $coupon->discount->save();

        $coupon->fill($validatedData);
        $coupon->save();

        return response(['info' => ["Coupon {$coupon->name} updated successfully!"]]);
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
