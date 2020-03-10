<?php

namespace App\Http\Controllers;

use App\RefundType;
use Illuminate\Http\Request;

class RefundTypeController extends Controller
{
    public function all()
    {
        $refundTypes = RefundType::enabled();

        // @TODO pick methods for phonecenters
        $this->store = auth()->user()->open_register->cash_register->store;
        if ($this->store->is_phone_center) {
            //     $phoneCenterPaymentTypes[0] = $refundTypes[2];
            //     $phoneCenterPaymentTypes[1] = $refundTypes[3];
            //     $phoneCenterPaymentTypes[2] = $refundTypes[4];
            //     $phoneCenterPaymentTypes[3] = $refundTypes[5];
            //     return response($phoneCenterPaymentTypes);
        } else {
            return response($refundTypes);
        }
    }
}
