<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'name',
        'purpose',
        'type',
        'start_date',
        'end_date',
        'estimated_budget',
        'actual_cost',
        'created_by',
        'status',
    ];

    // Relationships (add as needed)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(MarketingCampaignMember::class);
    }
    public function campaignAdvances()
    {
        return $this->hasMany(MarketingCampaignAdvance::class);
    }
    public function activityLogs()
    {
        return $this->hasMany(MarketingCampaignActivityLog::class);
    }
    public function advanceSettlements()
    {
        return $this->hasMany(MarketingCampaignAdvanceSettlement::class);
    }
    public function campaignCities()
    {
        return $this->hasMany(City::class);
    }
    public function campaignTransports()
    {
        return $this->hasMany(MarketingCampaignTransport::class);
    }

   
}
