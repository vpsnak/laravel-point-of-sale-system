<?php

namespace App;

class CashRegisterReport extends BaseModel
{
    protected $fillable = [
        'report_name',
        'report_type',
        'created_by',
        'cash_register_id',
        'subtotal',
        'tax',
        'total',
        'cash_total',
        'gift_card_total',
        'credit_card_total',
        'pos_terminal_total',
        'change_total',
        'cash_tax',
        'gift_card_tax',
        'credit_card_tax',
        'pos_terminal_tax',
        'order_count',
        'order_product_count',
        'order_refund_count',
        'order_refund_total',
        'order_complete_count',
        'order_complete_total',
        'order_retail_count',
        'order_in_store_count',
        'order_delivery_count',
    ];
}
