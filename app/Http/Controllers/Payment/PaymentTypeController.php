<?php

namespace App\Http\Controllers;

use App\PaymentType;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function paymentTypes()
    {
        return response(PaymentType::getPaymentTypes());
    }

    public function refundTypes()
    {
        return response(PaymentType::getRefundTypes());
    }
}
