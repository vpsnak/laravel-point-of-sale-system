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
            'name' => 'required|alpha',
            'code' => 'required|string',
            'uses' => 'nullable|numeric',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);
        $discountData = $request->validate([
            'discount_type' => 'required|string',
            'discount_amount' => 'required|numeric',
        ]);
        
        $discount = Discount::store([
            'type' => $discountData['discount_type'],
            'amount' => $discountData['discount_amount'],
        ]);
        $coupon = $discount->coupon()->create($validatedData);
        
        return response($coupon, 201);
    }
}
