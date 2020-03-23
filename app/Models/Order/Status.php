<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;

class Status extends Model
{
    use EagerLoadPivotTrait;
    public $timestamps = false;

    protected $fillable = [
        'color',
        'icon',
        'can_checkout',
        'can_edit_order_options',
        'can_edit_order_items',
        'can_receipt',
        'can_mas_upload',
        'can_mas_reupload',
        'can_refund',
        'can_cancel',
    ];

    protected $casts = [
        'can_checkout'  => 'boolean',
        'can_edit_order_options' => 'boolean',
        'can_edit_order_items' => 'boolean',
        'can_receipt' => 'boolean',
        'can_mas_upload' => 'boolean',
        'can_mas_reupload' => 'boolean',
        'can_refund' => 'boolean',
        'can_cancel' => 'boolean',
    ];

    public function orders()
    {
        return $this
            ->belongsToMany(Order::class)
            ->using(OrderStatus::class)
            ->withPivot('id')
            ->withPivot('user_id')
            ->withTimestamps(['created_at']);
    }
}
