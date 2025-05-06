<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceSalary extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'employee_id', 
        'voucher_id',
        'account_id',       
        'amount',
        'advance_date',
        'reason',
        'is_settled',
    ];

    // Relationships

    public function account(){
        return $this->belongsTo(ChartOfAccount::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
