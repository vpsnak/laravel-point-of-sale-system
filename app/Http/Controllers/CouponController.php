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
            'uses' => 'nullable|numeric',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);
        $discountData = $request->validate([
            'discount_type' => 'required|string',
            'discount_amount' => 'required|numeric',
        ]);


        $validatedID = $request->validate([
            'id' => 'nullable|exists:coupons,id'
        ]);

        if (!empty($validatedID)) {
            // @TODO fix update
            return response($this->model::updateData($validatedID, $validatedData), 200);
        } else {

            $discount = Discount::store([
                'type' => $discountData['discount_type'],
                'amount' => $discountData['discount_amount'],
            ]);
            $coupon = $discount->coupon()->create($validatedData);

            return response($coupon, 201);
        }
    }
}
