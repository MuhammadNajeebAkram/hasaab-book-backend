<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ChartOfAccount extends Model
{
    use HasFactory;
    //

    protected $fillable = [
        'code',
        'account_name',
        'parent_id',
        'has_child',
        'opening_balance',
        'type',
        'report_type',
        'level',
        'is_active',
        'description',
        'normal_balance',
        'is_cash_account',
        'is_bank_account',
        'is_default',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        $user = Auth::guard('api')->user();
        if ($user) {
            $model->created_by = $user->id;
            $model->updated_by = $user->id;
            $model->created_at = Carbon::now();
            $model->updated_at = Carbon::now();
        }
    });

    static::updating(function ($model) {
        $user = Auth::guard('api')->user();
        if ($user) {
            $model->updated_by = $user->id;
            $model->updated_at = Carbon::now();
        }
    });
}

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function accountRegisters(){
        return $this->hasMany(AccountRegister::class);
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    // Optional: To get parent account
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    
}
