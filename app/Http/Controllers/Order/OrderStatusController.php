<?php

namespace App\Http\Controllers;

use App\Order;
use App\Payment;
use App\MasOrder;
use App\Status;
use App\OrderStatus;
use Money\Money;
use Money\Currency;

class OrderStatusController extends Controller
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getOrderStatuses(Order $model)
    {
        return response($model->statuses()->with('pivot.processedBy')->get());
    }

    public function updateOrderStatus(Payment $payment = null, bool $refund = false)
    {
        $this->user = auth()->user();

        $remaining = $this->order->total_price->subtract($this->order->paid_price);
        $change = $this->order->paid_price->subtract($this->order->total_price);

        if ($change->isNegative(0)) {
            $change = new Money(0, new Currency($this->order->currency));
        }

        if ($remaining->isNegative(0)) {
            $remaining = new Money(0, new Currency($this->order->currency));
        }

        if ($remaining->isPositive(0)) {
            $this->order->change = new Money(0, new Currency($this->order->currency));
            if ($this->order->status->value !== 'pending_payment') {
                $pendingPaymentStatusId = Status::where('value', 'pending_payment')->firstOrFail('id');
                $this->order->statuses()->attach($pendingPaymentStatusId, ['user_id' => $this->user->id]);
            }
        } else {
            if (!$refund) {
                $payment->price = $payment->price->subtract($change);
                $payment->save();

                $this->order->change = $change;

                if ($this->order->status->value !== 'paid') {
                    $paidPaymentStatusId = Status::where('value', 'paid')->firstOrFail('id');
                    $this->order->statuses()->attach($paidPaymentStatusId, ['user_id' => $this->user->id]);

                    MasOrder::create([
                        'order_id' => $this->order->id,
                        'status' => 'queued'
                    ]);
                }
            } else {
                if (empty($payment)) {
                    $paidPaymentStatusId = Status::where('value', 'paid')->firstOrFail('id');
                    $this->order->statuses()->attach($paidPaymentStatusId, ['user_id' => $this->user->id]);
                }
                // $this->order->change = $change;
            }
        }

        // $this->order->save();

        return [
            'remaining' => $remaining,
            'change' =>  $change,
            'order_status' => $this->order->status->value
        ];
    }
}
