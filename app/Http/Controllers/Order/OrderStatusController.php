<?php

namespace App\Http\Controllers;

use App\Giftcard;
use App\Order;
use App\MasOrder;
use App\Status;
use App\Helper\Price;

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

    public function updateOrderStatus(bool $refund = false)
    {
        $this->user = auth()->user();
        $remaining = $this->order->remaining_price;

        if ($remaining->isPositive()) {
            $this->order->change = Price::parsePrice();
            if ($this->order->status->value !== 'payment_pending') {
                $paymentPendingStatusId = Status::where('value', 'payment_pending')->firstOrFail('id');
                $this->order->statuses()->attach($paymentPendingStatusId, ['processed_by_id' => $this->user->id]);
            }
        } else if ($refund && $remaining->isNegative()) {
            if ($this->order->status->value !== 'refund_pending') {
                $refundPendingStatusId = Status::where('value', 'refund_pending')->firstOrFail('id');
                $this->order->statuses()->attach($refundPendingStatusId, ['processed_by_id' => $this->user->id]);
            }
        } else {
            if (!$refund) {
                if ($this->order->status->value !== 'paid') {
                    $paidPaymentStatusId = Status::where('value', 'paid')->firstOrFail('id');
                    $this->order->statuses()->attach($paidPaymentStatusId, ['processed_by_id' => $this->user->id]);

                    $this->handleGiftcards();

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

    private function handleGiftcards()
    {
        foreach ($this->order->items as $item) {
            if ($item['type'] === 'giftcard') {
                $gc = Giftcard::where('code', $item['code'])->firstOrFail();

                if (!$gc->enabled_at) {
                    $gc->update(['enabled_at' => now()]);
                } else {
                    $gc->price = $gc->price->add(Price::parsePrice($item['price']));
                    $gc->save();
                }
            }
        }
    }
}
