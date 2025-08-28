<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoyaltyPaymentScheduleDetail extends Model
{
    //
    protected $fillable = [
        'royalty_schedule_id',
        'payment_date',
        'amount',
        'bank_account_id',
        'cheque_no',
        'status',
        'paid_date',
        'voucher_id',
    ];

    public function royaltySchedule(){
        return $this->belongsTo(RoyaltyPaymentSchedule::class);
    }
}
