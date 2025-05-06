<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeBonus extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'employee_id',
        'voucher_id',
        'amount',
        'type',
        'description',
        'bonus_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
    

}
