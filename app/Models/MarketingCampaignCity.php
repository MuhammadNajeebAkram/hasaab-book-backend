<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCampaignCity extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'marketing_campaign_id',
        'city_id',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
