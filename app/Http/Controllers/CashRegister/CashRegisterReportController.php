<?php

namespace App\Http\Controllers;

use App\CashRegister;
use App\CashRegisterLogs;
use App\CashRegisterReport;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CashRegisterReportController extends BaseController
{
    protected $model = CashRegisterReport::class;

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            //            'type' => 'required|in:x,z', // @TODO eval if needed
        ]);

        $report = self::generateReportByCashRegisterId($validatedData['cash_register_id']);
        if (!empty($report)) {
            return response($report, 201);
        } else {
            return response(['errors' => ['Logs' => 'No logs found']], 404);
        }
    }

    public static function generateReportByCashRegisterId($id)
    {
        $cash_register = CashRegister::getOne($id);
        $open_log = $cash_register->logs->where('status', 1)->first();
        if (!empty($open_log)) {
            $report = self::generateReport($open_log->id);
            $report['created_by'] = auth()->user()->id;
            $report['cash_register_id'] = $id;
            $report['report_type'] = 'x';
            $report['report_name'] = 'Report ' . strtoupper($report['report_type']) . ' ' . now();
            return CashRegisterReport::store($report);
        } else {
            $log = $cash_register->logs()->latest()->first();
            if (!empty($log)) {
                $report = self::generateReport($log->id);
                $report['created_by'] = auth()->user()->id;
                $report['cash_register_id'] = $id;
                $report['report_type'] = 'z';
                $report['report_name'] = 'Report ' . strtoupper($report['report_type']) . ' ' . now();

                return CashRegisterReport::store($report);
            }
            return null;
        }
    }

    public static function generateReport($id)
    {
        $cash_register_log = CashRegisterLogs::findOrFail($id);
        $orders = Order::where('updated_at', '>=', $cash_register_log->opening_time)
            ->where('updated_at', '<', $cash_register_log->closing_time ?? Carbon::now()->toDateTimeString())
            ->get();

        $canceled_orders = $orders->where('status', 'canceled');
        $complete_orders = $orders->where('status', 'complete');
        $cash = 0;
        $cash_tax = 0;
        $gift_card = 0;
        $gift_card_tax = 0;
        $house_account = 0;
        $house_account_tax = 0;
        $credit_card = 0;
        $credit_card_tax = 0;
        $pos_terminal = 0;
        $pos_terminal_tax = 0;
        foreach ($complete_orders as $order) {
            $payments = $order->payments()->where('cash_register_id', '=', $cash_register_log->cash_register->id)
                ->whereStatus('approved')
                ->where('created_at', '>=', $cash_register_log->opening_time)
                ->where('updated_at', '<', $cash_register_log->closing_time ?? Carbon::now()->toDateTimeString())
                ->get();

            $tax = $order->tax / 100;

            if ($order->total_paid > $order->total) {
                $amount = $order->total_paid - $order->total;
                $cash += -$amount;
                $cash_tax += -$amount * $tax;
            }

            foreach ($payments as $payment) {
                $amount = $payment->amount;
                if ($payment->refunded == 1) {
                    $amount = 0;
                }
                switch ($payment->paymentType->type) {
                    case 'cash':
                        $cash += $amount;
                        $cash_tax += $amount * $tax;
                        break;
                    case 'card':
                        $credit_card += $amount;
                        $credit_card_tax += $amount * $tax;
                        break;
                    case 'pos-terminal':
                        $pos_terminal += $amount;
                        $pos_terminal_tax += $amount * $tax;
                        break;
                    case 'house-account':
                        $house_account += $amount;
                        $house_account_tax += $amount * $tax;
                        break;
                    case 'giftcard':
                        $gift_card += $amount;
                        $gift_card_tax += $amount * $tax;
                        break;
                }
            }
        }
        return [
            'opening_amount' => $cash_register_log->opening_amount,
            'closing_amount' => $cash_register_log->closing_amount ?? 0,
            'subtotal' => $complete_orders->sum('subtotal'),
            'tax' => $complete_orders->sum(function ($row) {
                return ($row->tax / 100) * $row->subtotal;
            }),
            'total' => $complete_orders->sum('total'),
            'cash_total' => $cash + $cash_register_log->opening_amount,
            'gift_card_total' => $gift_card,
            'credit_card_total' => $credit_card,
            'pos_terminal_total' => $pos_terminal,
            'change_total' => round($complete_orders->sum('total_paid') - $complete_orders->sum('total'), 2),
            'cash_tax' => $cash_tax,
            'gift_card_tax' => $gift_card_tax,
            'credit_card_tax' => $credit_card_tax,
            'pos_terminal_tax' => $pos_terminal_tax,
            'order_count' => $orders->count(),
            'order_product_count' => $orders->sum(function ($row) {
                return $row->items->count();
            }),
            'order_refund_count' => $canceled_orders->count(),
            'order_refund_total' => $canceled_orders->sum('total'),
            'order_complete_count' => $complete_orders->count(),
            'order_complete_total' => $complete_orders->sum('total'),
            'order_retail_count' => $orders->sum(function ($row) {
                if ($row->shipping_type == 'retail') {
                    return 1;
                }
                return 0;
            }),
            'order_in_store_count' => $orders->sum(function ($row) {
                if ($row->shipping_type == 'pickup') {
                    return 1;
                }
                return 0;
            }),
            'order_delivery_count' => $orders->sum(function ($row) {
                if ($row->shipping_type == 'delivery') {
                    return 1;
                }
                return 0;
            }),
        ];
    }

    public function checkCurrent(CashRegister $cashRegister)
    {
        $report = self::generateReportByCashRegisterId($cashRegister->id);

        return response($report);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string'
        ]);

        return $this->searchResult(
            ['report_name', 'report_type'],
            $validatedData['keyword'],
            true
        );
    }
}
