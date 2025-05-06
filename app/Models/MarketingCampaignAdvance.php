<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCampaignAdvance extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'campaign_id',
        'amount',
        'voucher_id',
        'advance_date',
        'remarks',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }
}
