<?php

namespace App;

use App\Http\Controllers\CashRegisterReportController;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $appends = ['is_open', 'earnings'];

    protected $fillable = [
        'name',
        'store_id',
        'created_by',
        'barcode',
        'pos_terminal',
        'printer',
    ];

    protected $casts = [
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

    public function getIsOpenAttribute()
    {
        if (!empty($this->getOpenLog())) {
            return true;
        }
        return false;
    }

    public function getOpenLog()
    {
        return $this->hasMany(CashRegisterLogs::class)->whereStatus(1)->first();
    }

    public function getPaymentsAttribute()
    {
        return $this->hasMany(Payment::class, 'cash_register_id', 'id')->without(['order', 'created_by'])->get();
    }

    public function logs()
    {
        return $this->hasMany(CashRegisterLogs::class);
    }
}
