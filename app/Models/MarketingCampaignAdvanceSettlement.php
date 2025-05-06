<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCampaignAdvanceSettlement extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'campaign_id',
        'voucher_id',
        'chart_of_account_id',
        'amount',
        'description',
        'expense_date',
        'attachment',
    ];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}
