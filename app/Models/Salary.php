<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'voucher_id',
        'account_id',
        'year',
        'month',
        'basic_salary',
        'house_rent',
        'medical_allowance',
        'travel_allowance',
        'overtime',
        'other_allowance',
        'advance_deduction',
        'loan_deduction',        
        'gross_salary',
        'net_salary',
        'status',
        'payment_date',
    ];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
       
           
            $model->created_at = Carbon::now();
            $model->updated_at = Carbon::now();
        
    });

    static::updating(function ($model) {       
            
            $model->updated_at = Carbon::now();
       
    });
}

    // Relationships

    public function account(){
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }   
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function voucher(){
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }
}
