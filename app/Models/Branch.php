<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Branch extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'name',
        'address',
        'contact_no',
        'city_id',
        'salary_account',
        'salary_advance_account',
        'employee_loan_account',
        'bonus_account',
        'overtime_account',
        'other_allowance_account',
        'is_active',
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
    
        public function salaryAccount()
        {
            return $this->belongsTo(ChartOfAccount::class, 'salary_account');
        }
    
        public function salaryAdvanceAccount()
        {
            return $this->belongsTo(ChartOfAccount::class, 'salary_advance_account');
        }
    
        public function employeeLoanAccount()
        {
            return $this->belongsTo(ChartOfAccount::class, 'employee_loan_account');
        }

    // Relationship to City
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Optional: Relationship to Employees (if needed)
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
