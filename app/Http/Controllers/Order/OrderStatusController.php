<?php

namespace App\Http\Controllers;

use App\Order;
use App\Transaction;
use App\MasOrder;
use App\Status;
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

    public function updateOrderStatus(Transaction $payment = null, bool $refund = false)
    {
        $this->user = auth()->user();

        $remaining = $this->order->remaining_price;

        if ($remaining->isPositive()) {
            $this->order->change = new Money(0, new Currency($this->order->currency));
            if ($this->order->status->value !== 'pending_payment') {
                $pendingPaymentStatusId = Status::where('value', 'pending_payment')->firstOrFail('id');
                $this->order->statuses()->attach($pendingPaymentStatusId, ['processed_by_id' => $this->user->id]);
            }
        } else {
            if (!$refund) {
                if ($this->order->status->value !== 'paid') {
                    $paidPaymentStatusId = Status::where('value', 'paid')->firstOrFail('id');
                    $this->order->statuses()->attach($paidPaymentStatusId, ['processed_by_id' => $this->user->id]);

                    MasOrder::create([
                        'order_id' => $this->order->id,
                        'status' => 'queued'
                    ]);
                }
            } else if (empty($payment) && $this->order->status->value !== 'paid') {
                $paidPaymentStatusId = Status::where('value', 'paid')->firstOrFail('id');
                $this->order->statuses()->attach($paidPaymentStatusId, ['processed_by_id' => $this->user->id]);
            }
        }

        return [
            'remaining' => $remaining,
            'status' => $this->order->status
        ];
    }
}
