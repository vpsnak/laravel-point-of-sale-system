<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Payment;
use App\Helper\Price;

class OrderAnalysisController extends Controller
{
    private $cc_pos;
    private $cc_api;
    private $card_totals;
    private $cash;
    private $house_account;
    private $coupon;
    private $giftcard;

    private function parseUnlinkedRefunds(array $unlinked_refunds)
    {
        foreach ($unlinked_refunds as $unlinked_refund) {
            switch ($unlinked_refund->paymentType->type) {
                case 'refund-api':
                    $this->cc_api['total_paid'] = Price::numberPrecision($this->card_totals['total_paid'] + $unlinked_refund->amount);
                    $this->cc_api['total_refunded'] = Price::numberPrecision($this->card_totals['total_refunded'] - $unlinked_refund->amount);
                    $this->card_totals['total_paid'] = Price::numberPrecision($this->card_totals['total_paid'] + $unlinked_refund->amount);
                    $this->card_totals['total_refunded'] = Price::numberPrecision($this->card_totals['total_refunded'] - $unlinked_refund->amount);
                case 'refund-pos':
                    $this->cc_pos['total_paid'] = Price::numberPrecision($this->card_totals['total_paid'] + $unlinked_refund->amount);
                    $this->cc_pos['total_refunded'] = Price::numberPrecision($this->card_totals['total_refunded'] - $unlinked_refund->amount);
                    $this->card_totals['total_paid'] = Price::numberPrecision($this->card_totals['total_paid'] + $unlinked_refund->amount);
                    $this->card_totals['total_refunded'] = Price::numberPrecision($this->card_totals['total_refunded'] - $unlinked_refund->amount);
                    break;
                case 'refund-giftcard-new':
                case 'refund-giftcard-existing':
                    $gc_total_paid = $this->giftcard['total_paid'] = Price::numberPrecision($this->cash['total_paid'] + $unlinked_refund->amount);
                    $this->giftcard['total_refunded'] = Price::numberPrecision($this->cash['total_refunded'] - $unlinked_refund->amount);
                    if ($gc_total_paid < 0) {
                        $this->giftcard['total_refunded'] = Price::numberPrecision($this->cash['total_refunded'] + $gc_total_paid);

                        // implement pool priorities
                        $gc_total_paid;
                    }
                    break;
                case 'refund-cash':
                    $this->cash['total_paid'] = Price::numberPrecision($this->cash['total_paid'] + $unlinked_refund->amount);
                    $this->cash['total_refunded'] = Price::numberPrecision($this->cash['total_refunded'] - $unlinked_refund->amount);
                    break;
            }
        }
    }

    private function fixCardTotals(float $amount)
    {
        if ($amount === $this->cc_pos['total_paid']) {
            $this->cc_pos['total_paid'] = 0;
            $this->cc_pos['total_refunded'] = Price::numberPrecision($this->cc_pos['total_refunded'] - $amount);
        } else if ($amount === $this->cc_api['total_paid']) {
            $this->cc_api['total_paid'] = 0;
            $this->cc_api['total_refunded'] = Price::numberPrecision($this->cc_api['total_refunded'] - $amount);
        } else if ($this->card_totals['total_paid'] === $amount) {
            $this->cc_pos['total_refunded'] = $this->cc_pos['total_paid'];
            $this->cc_api['total_refunded'] = $this->cc_api['total_paid'];
            $this->cc_pos['total_paid'] = $this->cc_api['total_paid'] = 0;
        } else {
            if ($amount < $this->cc_pos['total_paid']) {
            } else if ($amount < $this->cc_api['total_paid']) {
            } else {
            }
        }
    }

    private function generatePaymentDetails(Payment $payment, array $payment_details)
    {
        if ($payment->refunded) {
            $payment_details['total_refunded'] = Price::numberPrecision($payment_details['total_refunded'] + $payment->amount);
        } else {
            $payment_details['total_paid'] = Price::numberPrecision($payment_details['total_paid'] + $payment->amount);
        }

        return $payment_details;
    }

    public function getPaymentDetails(Order $model)
    {
        $this->cc_pos = $this->cc_api = $this->cash = $this->house_account = $this->coupon = $this->giftcard = [
            'total_paid' => 0,
            'total_refunded' => 0,
        ];

        $unlinked_refunds = [];

        foreach ($model->payments as $payment) {
            switch ($payment->paymentType->type) {
                case 'pos-terminal':
                    $this->cc_pos = $this->generatePaymentDetails($payment, $this->cc_pos);
                    break;
                case 'cash':
                    $this->cash = $this->generatePaymentDetails($payment, $this->cash);
                    break;
                case 'card':
                    $this->cc_api = $this->generatePaymentDetails($payment, $this->cc_api);
                    break;
                case 'house-account':
                    $this->house_account = $this->generatePaymentDetails($payment, $this->house_account);
                    break;
                case 'coupon':
                    $this->coupon = $this->generatePaymentDetails($payment, $this->coupon);
                    break;
                case 'giftcard':
                    $this->giftcard = $this->generatePaymentDetails($payment, $this->giftcard);
                    break;
                default:
                    array_push($unlinked_refunds, $payment);
                    break;
            }
        }

        $this->card_totals = [
            'total_paid' => $this->cc_pos['total_paid'] + $this->cc_api['total_paid'],
            'total_refunded' => $this->cc_pos['total_refunded'] + $this->cc_api['total_refunded']
        ];

        $this->parseUnlinkedRefunds($unlinked_refunds);

        $response = [
            'pos-terminal' => $this->cc_pos,
            'card' => $this->cc_api,
            'cash' => $this->cash,
            'house-account' => $this->house_account,
            'coupon' => $this->coupon,
            'giftcard' => $this->giftcard,
            'card_totals' => [$this->card_totals]
        ];

        foreach ($model->statuses as $status) {
            return response($status->pivot->processedBy()->without('roles')->first());
        }

        return $response;
    }
}
