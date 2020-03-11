<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Money\Money;
use Money\Currency;
use App\Order;
use App\Payment;
use App\Helper\Price;

class OrderAnalysisController extends Controller
{
    private $cc_pos;
    private $cc_api;
    private $cash;
    private $house_account;
    private $coupon;
    private $giftcard;
    private $total_paid;

    public function getPaymentDetails(Order $model)
    {
        $paidAmount =
            $this->total_paid =
            $this->cc_pos =
            $this->cc_api =
            $this->cash =
            $this->house_account =
            $this->coupon =
            $this->giftcard =
            new Money(0, new Currency($model->currency));

        foreach ($model->payments as $payment) {
            if ($payment->status === 'approved') {


                $paidAmount = $payment->price;
                switch ($payment->paymentType->type) {
                    case 'pos-terminal':
                        $this->cc_pos = $this->cc_pos->add($paidAmount);
                        break;
                    case 'cash':
                        $paidAmount = $paidAmount->subtract($payment->change_price);
                        $this->cash = $this->cash->add($paidAmount);
                        break;
                    case 'card':
                        $this->cc_api = $this->cc_api->add($paidAmount);
                        break;
                    case 'house-account':
                        $this->house_account = $this->house_account->add($paidAmount);
                        break;
                    case 'coupon':
                        $this->coupon = $this->coupon->add($paidAmount);
                        break;
                    case 'giftcard':
                        $this->giftcard = $this->giftcard->add($paidAmount);
                        break;
                }
            }
        }

        $this->total_paid = $this->total_paid
            ->add($this->cc_pos)
            ->add($this->cc_api)
            ->add($this->cash)
            ->add($this->house_account)
            ->add($this->coupon)
            ->add($this->giftcard)
            ->add($this->total_paid);

        $response = [
            'card_pos' => $this->cc_pos,
            'card_keyed' => $this->cc_api,
            'cash' => $this->cash,
            'house_account' => $this->house_account,
            'giftcard' => $this->giftcard,
            'coupon' => $this->coupon,
            'total_paid' => $this->total_paid
        ];

        return response()->json($response, 200, [], JSON_NUMERIC_CHECK);
    }
}
