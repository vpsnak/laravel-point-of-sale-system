<?php

namespace App\Http\Controllers;

use App\PaymentType;

class PaymentTypeController extends Controller
{
    public function all()
    {
        $paymentTypes = PaymentType::enabled();

        $this->store = auth()->user()->open_register->cash_register->store;
        if ($this->store->is_phone_center) {
            $phoneCenterPaymentTypes[0] = $paymentTypes[2];
            $phoneCenterPaymentTypes[1] = $paymentTypes[3];
            $phoneCenterPaymentTypes[2] = $paymentTypes[4];
            $phoneCenterPaymentTypes[3] = $paymentTypes[5];
            return response($phoneCenterPaymentTypes);
        } else {
            return response($paymentTypes);
        }
    }
}
