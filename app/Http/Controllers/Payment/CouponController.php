<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Discount;
use Illuminate\Http\Request;

class CouponController extends BaseController
{
    protected $model = Coupon::class;

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


        $validatedID = $request->validate([
            'id' => 'nullable|exists:coupons,id'
        ]);

        if (!empty($validatedID)) {
            $coupon = $this->model::findOrFail($validatedID['id']);
            $coupon->update($validatedData);

            $coupon->discount->type = $discountData['discount']['type'];
            $coupon->discount->amount = $discountData['discount']['amount'];

            $coupon->discount->save();
            // @TODO fix update
            return response(['info' => ['Coupon updated successfully!']], 200);
        } else {
            $discount = Discount::store([
                'type' => $discountData['discount']['type'],
                'amount' => $discountData['discount']['amount'],
            ]);
            $coupon = $discount->coupon()->create($validatedData);

            return response([
                'info' => ['Coupon created successfully!'],
                $coupon
            ], 201);
        }
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        return $this->searchResult(
            ['name', 'code'],
            $validatedData['keyword'],
            true
        );
    }
}
