<?php

namespace App;

use Illuminate\Support\Carbon;

class CashRegisterLogs extends BaseModel
{
    protected $appends = ['earnings'];
    protected $with = ['cash_register'];
    protected $fillable = [
        'user_id',
        'cash_register_id',
        'opening_amount',
        'closing_amount',
        'status',
        'opening_time',
        'closing_time',
        'opened_by',
        'closed_by',
        'note',
    ];

    public function cash_register()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function getEarningsAttribute()
    {
        $payments = Payment::where('cash_register_id', '=', $this->cash_register->id)
            ->whereStatus('approved')
            ->where('created_at', '>=', $this->opening_time)
            ->where('updated_at', '<', $this->closing_time ?? Carbon::now()->toDateTimeString())
            ->get();

        $orders = Order::where('status', '!=', 'canceled')
            ->where('created_at', '>=', $this->opening_time)
            ->where('updated_at', '<', $this->closing_time ?? Carbon::now()->toDateTimeString())
            ->get();

        $cash_income = 0;
        $cash_outcome = 0;

        $order_total = 0;
        $order_total_paid = 0;
        foreach ($payments as $payment) {
            if ($payment->paymentType->type == 'cash') {
                $cash_income += $payment->amount;
            }
        }

        foreach ($orders as $order) {
            $cash_outcome += $order->total_paid - $order->total;
            $order_total += $order->total;
            $order_total_paid += $order->total_paid;
        }

        return [
            'opening_amount' => $this->opening_amount,
            'closing_amount' => $this->closing_amount ?? 0,
            'cash_income' => $cash_income,
            'cash_outcome' => $cash_outcome,
            'cash_final' => $this->opening_amount + $cash_income - $cash_outcome,
            'order_total' => $order_total,
            'order_total_paid' => $order_total_paid,
        ];
    }
}
