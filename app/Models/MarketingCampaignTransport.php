<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCampaignTransport extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'marketing_campaign_id',
        'transport_type',
        'vehicle_id',
        'driver_name',
        'start_meter',
        'end_meter',
        'fuel_expense',
        'rental_cost',
        'remarks',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
