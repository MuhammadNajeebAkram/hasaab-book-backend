<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleExpense extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'vehicle_id',
        'voucher_id',
        'chart_of_account_id',
        'amount',
        'expense_type',
        'expense_date',
        'receipt',
        'notes',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}
