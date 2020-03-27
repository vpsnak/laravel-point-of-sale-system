<?php

namespace App\Http\Controllers;

use App\Order;
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

    public function getIncomeDetails(Order $model)
    {
        $price = [
            'amount' => 0,
            'currency' => $model->currency
        ];

        $paidAmount =
            $this->total_paid =
            $this->cc_pos =
            $this->cc_api =
            $this->cash =
            $this->house_account =
            $this->coupon =
            $this->giftcard = Price::parsePrice($price);

        foreach ($model->transactions as $transaction) {
            if ($transaction->status === 'approved') {
                $paidAmount = $transaction->price;
                switch ($transaction->payment->paymentType->type) {
                    case 'pos-terminal':
                        $this->cc_pos = $this->cc_pos->add($paidAmount);
                        break;
                    case 'cash':
                        $paidAmount = $paidAmount->subtract($transaction->payment->change_price);
                        $this->cash = $this->cash->add($paidAmount);
                        break;
                    case 'card':
                        $this->cc_api = $this->cc_api->add($paidAmount);
                        break;
                    case 'house-account':
                        $this->house_account = $this->house_account->add($paidAmount);
                        break;
                        // case 'coupon':
                        //     $this->coupon = $this->coupon->add($paidAmount);
                        //     break;
                    case 'giftcard':
                        $this->giftcard = $this->giftcard->add($paidAmount);
                        break;
                }
            } else if ($transaction->status === 'refund approved') {
                $refundAmount = $transaction->price;
                switch ($transaction->type_name) {
                    case 'pos-terminal':
                        $this->cc_pos = $this->cc_pos->add($refundAmount);
                        break;
                    case 'Cash refund':
                        $this->cash = $this->cash->subtract($refundAmount);
                        break;
                    case "Credit Card (keyed) refund":
                        $this->cc_api = $this->cc_api->subtract($refundAmount);
                        break;
                    case 'house-account':
                        $this->house_account = $this->house_account->subtract($refundAmount);
                        break;
                        // case 'coupon':
                        //     $this->coupon = $this->coupon->subtract($refundAmount);
                        //     break;
                    case 'Gift Card refund':
                        $this->giftcard = $this->giftcard->subtract($refundAmount);
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
