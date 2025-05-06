<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Division extends Model
{
    use HasFactory;
    //

    protected $fillable = ['province_id', 'name', 'is_active']; // Add columns that can be mass assigned

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

    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function districts(){
        return $this->hasMany(District::class);
    }
}
