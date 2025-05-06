<?php

namespace App\Http\Controllers;

use App\Models\AdvanceSalaryEntry;
use Illuminate\Http\Request;

class AdvanceSalaryEntryController extends Controller
{
    //

    public function getBalance($employee_id)
    {
        try {
            $balance = AdvanceSalaryEntry::whereHas('advance', function ($query) use ($employee_id) {
                $query->where('is_settled', 0)
                      ->where('employee_id', $employee_id);
            })
            ->selectRaw("
                SUM(CASE WHEN payment_type = 'issued' THEN amount ELSE 0 END) AS total_issued,
                SUM(CASE WHEN payment_type = 'recovered' THEN amount ELSE 0 END) AS total_recovered
            ")
            ->first();
    
            return [
                'success' => 1,
                'total_issued' => $balance->total_issued ?? 0,
                'total_recovered' => $balance->total_recovered ?? 0,
                'data' => ($balance->total_issued ?? 0) - ($balance->total_recovered ?? 0),
            ];
        } catch (\Exception $e) {
            // \Log::error('Failed to get advance salary balance: ' . $e->getMessage());
            return [
                'success' => -1,
                'message' => $e->getMessage(),
                'total_issued' => 0,
                'total_recovered' => 0,
                'data' => 0,
            ];
        }
    }
    
    
}
