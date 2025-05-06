<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'name',
        'registration_number',
        'vehicle_type',
        'current_meter',
        'ownership_type',
        'is_active',
    ];

    public function marketingCampaignTransports()
    {
        return $this->hasMany(MarketingCampaignTransport::class);
    }
    public function expenses()
    {
        return $this->hasMany(VehicleExpense::class);
    }
}
