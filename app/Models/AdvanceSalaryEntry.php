<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceSalaryEntry extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'advance_id',
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
    public function advance()
    {
        return $this->belongsTo(AdvanceSalary::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
