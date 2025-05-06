<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'cnic', 'email', 'address', 'city_id', 'contact_no', 'designation_id', 'branch_id',
        'joining_date', 'salary', 'house_rent', 'travel_allownce', 'medical_allownce',
        'gender', 'dob', 'emergency_contact', 'photo'
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


    public function designation() {
        return $this->belongsTo(EmployeeDesignation::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function salaries() {
        return $this->hasMany(Salary::class);
    }

    public function advances() {
        return $this->hasMany(AdvanceSalary::class);
    }

    public function loans() {
        return $this->hasMany(EmployeeLoan::class);
    }

    public function bonuses() {
        return $this->hasMany(EmployeeBonus::class);
    }

    public function marketingCampaigns() {
        return $this->hasMany(MarketingCampaignMember::class);
    }
}
