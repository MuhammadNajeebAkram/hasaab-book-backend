<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeBonus extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'employee_id',
        'voucher_id',
        'account_id',
        'bonus_id',
        'amount',        
        'description',
        'bonus_date',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function bonus()
    {
        return $this->belongsTo(Bonus::class);
    }
    

}
