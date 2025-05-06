<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountRegister extends Model
{
    use HasFactory;
    //

    protected $fillable = [
        'voucher_id',
        'account_id',
        'amount',
        'type',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    
}
