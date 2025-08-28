<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoyaltyPaymentSchedule extends Model
{
    //
    protected $fillable = [
        'professor_id',        
        'financial_year', 
        'instructions',      
        'is_active',

    ];

    public function financialYear(){
        return $this->belongsTo(FinancialYear::class, 'financial_year');
    }

    public function scheduleDetails(){
        return $this->hasMany(RoyaltyPaymentScheduleDetail::class, 'royalty_schedule_id');
    }
    
    public function professor(){
        return $this->belongsTo(Professor::class, 'professor_id');
    }
}
