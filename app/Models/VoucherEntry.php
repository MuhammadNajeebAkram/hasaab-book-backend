<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VoucherEntry extends Model
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

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Relationship: Each voucher entry belongs to an account (chart_of_accounts)
     */
    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }
}
