<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoyaltyPayment extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'professor_id',
        'amount',
        'royalty_period',
        'status',
        'payment_date',
    ];

    public function voucher(){
        return $this->belongsTo(Voucher::class);
    }

    public function professor(){
        return $this->belongsTo(Professor::class);
    }
}
