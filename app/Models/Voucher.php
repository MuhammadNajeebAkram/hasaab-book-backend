<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Voucher extends Model
{
    use HasFactory;
    //

    protected $fillable = [
        'voucher_no',
        'type',
        'payment_mode',
        'voucher_date',
        'description',
        'transaction_no',
        'is_posted',
        'created_by',
        'updated_by',
        'posted_by',
        'posted_at',
        'payment_account',
        
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'posted_at' => 'datetime',
        'is_posted' => 'boolean',
    ];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        $user = Auth::guard('api')->user();
        if ($user) {
            $model->created_by = $user->id;
            $model->updated_by = $user->id;
            $model->created_at = now();
            $model->updated_at = now();
        }
    });

    static::updating(function ($model) {
        $user = Auth::guard('api')->user();
        if ($user) {
            $model->updated_by = $user->id;
            $model->updated_at = now();
        }
    });
}

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
    public function account(){
        return $this->belongsTo(ChartOfAccount::class, 'payment_account');
    }
    
/*
    public function voucherable()
    {
        return $this->morphTo();
    }
*/
}
