<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    //
    protected $fillable = [
        'name',
        'start_month',
        'end_month',
    ];

    public function royalySchedules(){
        return $this->hasMany(RoyaltyPaymentSchedule::class, 'financial_year');
    }
}
