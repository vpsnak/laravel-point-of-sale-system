<?php

namespace App;

use App\Http\Controllers\CashRegisterReportController;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    public $timestamps = false;

    protected $appends = ['store_name', 'is_open', 'open_session_user'];

    protected $fillable = [
        'name',
        'active',
        'store_id',
        'barcode',
        'pos_terminal_ip',
        'pos_terminal_port',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s'
    ];

    public function earnings()
    {
        if ($this->is_open) {
            return CashRegisterReportController::generateReport($this->getOpenLog()->id);
        }
        return null;
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function getStoreNameAttribute()
    {
        return $this->store->name;
    }

    public function getOpenSessionUserAttribute()
    {
        if ($this->is_open) {
            return $this->getOpenLog()->with('user')->first()->user;
        } else {
            return false;
        }
    }

    public function getIsOpenAttribute()
    {
        if (!empty($this->getOpenLog())) {
            return true;
        }
        return false;
    }

    public function getOpenLog()
    {
        return $this->cashRegisterLogs()->where('status', true)->first();
    }

    public function cashRegisterLogs()
    {
        return $this->hasMany(CashRegisterLogs::class);
    }

    public function getTransactionsAttribute()
    {
        return $this->hasMany(Transaction::class, 'cash_register_id', 'id')->without(['order', 'createdBy'])->get();
    }

    public function logs()
    {
        return $this->hasMany(CashRegisterLogs::class);
    }
}
