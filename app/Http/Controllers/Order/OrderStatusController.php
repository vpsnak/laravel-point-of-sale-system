<?php

namespace App\Http\Controllers;

use App\Order;
use App\Payment;
use App\MasOrder;
use App\Helper\Price;
use App\Status;
use App\OrderStatus;

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

        $remaining = Price::numberPrecision($this->order->total - $this->order->total_paid);
        $change = Price::numberPrecision($this->order->total_paid - $this->order->total);

        if ($change < 0) {
            $change = 0;
        }

        if ($remaining < 0) {
            $remaining = 0;
        }

        if ($remaining > 0) {
            $this->order->change = 0;
            if ($this->order->status->value !== 'pending_payment') {
                $pendingPaymentStatusId = Status::where('value', 'pending_payment')->firstOrFail('id');
                $this->order->statuses()->attach($pendingPaymentStatusId, ['user_id' => $this->user->id]);
            }
        } else {
            if (!$refund) {
                $payment->amount = Price::numberPrecision($payment->amount - $change);
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
                $this->order->change = $change;
            }
        }

        $this->order->save();

        return [
            'remaining' => $remaining,
            'change' =>  $change,
            'order_status' => $this->order->status
        ];
    }
}
