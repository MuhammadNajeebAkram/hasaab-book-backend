<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCampaignActivityLog extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'campaign_id',
        'activity',
        'activity_date',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }
}
