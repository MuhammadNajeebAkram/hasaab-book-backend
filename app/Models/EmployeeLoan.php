<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class EmployeeLoan extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'employee_id',
        'voucher_id',
        'account_id',
        'amount',
        'installments',
        'installment_amount',
        'issue_date',
        'repayment_start_year',
        'repayment_start_month',
        'reason',
        'status',
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

    public function loans(){
        return $this->hasMany(EmployeeLoanEntry::class);
    }
}
