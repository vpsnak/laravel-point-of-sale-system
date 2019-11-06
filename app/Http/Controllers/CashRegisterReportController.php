<?php

namespace App\Http\Controllers;

use App\CashRegister;
use App\CashRegisterLogs;
use App\CashRegisterReport;
use App\Order;
use App\Payment;
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
            return response(['message' => 'No logs found'], 404);
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
        $payments = Payment::where('cash_register_id', '=', $cash_register_log->cash_register->id)
            ->whereStatus('approved')
            ->where('created_at', '>=', $cash_register_log->opening_time)
            ->where('updated_at', '<', $cash_register_log->closing_time ?? Carbon::now()->toDateTimeString())
            ->get();

        $orders = Order::where('status', '!=', 'canceled')
            ->where('created_at', '>=', $cash_register_log->opening_time)
            ->where('updated_at', '<', $cash_register_log->closing_time ?? Carbon::now()->toDateTimeString())
            ->get();

        $cash = $payments->where('payment_type', 2);
        $gift_card = $payments->where('payment_type', 6);
        $credit_card = $payments->where('payment_type', 3);
        $pos_terminal = $payments->where('payment_type', 1);
        $canceled_orders = $orders->where('status', 'canceled');
        $complete_orders = $orders->where('status', 'complete');
        return [
            'opening_amount' => $cash_register_log->opening_amount,
            'closing_amount' => $cash_register_log->closing_amount,
            'subtotal' => $orders->sum('subtotal'),
            'tax' => $orders->sum(function ($row) {
                return ($row->tax / 100) * $row->subtotal;
            }),
            'total' => $orders->sum('total'),
            'cash_total' => $cash->sum('amount') + $cash_register_log->opening_amount,
            'gift_card_total' => $gift_card->sum('amount'),
            'credit_card_total' => $credit_card->sum('amount'),
            'pos_terminal_total' => $pos_terminal->sum('amount'),
            'change_total' => round($orders->sum('total_paid') - $orders->sum('total'), 2),
            'cash_tax' => round($cash->sum(function ($row) {
                return ($row->order->tax / 100) * $row->amount;
            }), 2),
            'gift_card_tax' => round($gift_card->sum(function ($row) {
                return ($row->order->tax / 100) * $row->amount;
            }), 2),
            'credit_card_tax' => round($credit_card->sum(function ($row) {
                return ($row->order->tax / 100) * $row->amount;
            }), 2),
            'pos_terminal_tax' => round($pos_terminal->sum(function ($row) {
                return ($row->order->tax / 100) * $row->amount;
            }), 2),
            'order_count' => $orders->count(),
            'order_product_count' => $orders->sum(function ($row) {
                return $row->items->count();
            }),
            'order_refund_count' => $canceled_orders->count(),
            'order_refund_total' => $canceled_orders->sum('total'),
            'order_complete_count' => $complete_orders->count(),
            'order_complete_total' => $complete_orders->sum('total'),
            'order_retail_count' => 0,
            'order_in_store_count' => 0,
            'order_delivery_count' => 0,
        ];
    }
}
