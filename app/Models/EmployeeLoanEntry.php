<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLoanEntry extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'employee_loan_id',
        'voucher_id',
        'payment_type',
        'amount',
        
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
    public function loan()
    {
        return $this->belongsTo(EmployeeLoan::class, 'employee_loan_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }
}
