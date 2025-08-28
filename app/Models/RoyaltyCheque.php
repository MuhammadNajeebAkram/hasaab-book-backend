<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoyaltyCheque extends Model
{
    //
    protected $fillable = [
        'royalty_schedule_id',
        'cheque_no',
        'issue_date',
        'cheque_date',
        'amount',
        'is_active',
    ];

    public function royaltySchedule(){
        return $this->belongsTo(RoyaltyPaymentSchedule::class);
    }


}
