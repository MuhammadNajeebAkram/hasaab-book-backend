<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCampaignMember extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'marketing_campaign_id',
        'employee_id',
        'role',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
