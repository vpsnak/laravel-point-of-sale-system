<?php

namespace App;

use App\Http\Controllers\CashRegisterReportController;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $appends = ['is_open', 'earnings', 'open_session_user'];

    protected $fillable = [
        'name',
        'active',
        'store_id',
        'user_id',
        'barcode',
        'pos_terminal',
        'printer',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => "datetime:m/d/Y H:i:s",
        'updated_at' => "datetime:m/d/Y H:i:s"
    ];

    public function getEarningsAttribute()
    {
        if ($this->is_open) {
            return CashRegisterReportController::generateReport($this->getOpenLog()->id);
        }
        return null;
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
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

    public function getPaymentsAttribute()
    {
        return $this->hasMany(Payment::class, 'cash_register_id', 'id')->without(['order', 'user_id'])->get();
    }

    public function logs()
    {
        return $this->hasMany(CashRegisterLogs::class);
    }
}
