<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeLoanEntryController extends Controller
{
    //
    public function getInstallment($employee_id)
    {
        try {
          /*  $balance = EmployeeLoanEntry::whereHas('loan', function ($query) use ($employee_id) {
                $query->where('status', 'active')
                      ->where('employee_id', $employee_id);
            })
            ->selectRaw("
                SUM(CASE WHEN payment_type = 'issued' THEN amount ELSE 0 END) AS total_issued,
                SUM(CASE WHEN payment_type = 'recovered' THEN amount ELSE 0 END) AS total_recovered
            ")
            ->first();

            $balanceAmount = ($balance->total_issued ?? 0) - ($balance->total_recovered ?? 0);
*/
            $installment = 0;

            $empLoan = EmployeeLoan::where('employee_id', $employee_id)
            ->where('status', 'active')
            ->get()
            ->first();
            

            if($empLoan){
                if(Carbon::now()->year >= $empLoan->repayment_start_year && Carbon::now()->month >= $empLoan->repayment_start_month){
                    $installment = $empLoan->installment_amount;

                }

            }

            
    
            return [
                'success' => 1,
                'data' => $installment,
            ];
        } catch (\Exception $e) {
            // \Log::error('Failed to get advance salary balance: ' . $e->getMessage());
            return [
                'success' => -1,
                'message' => $e->getMessage(),                
                'data' => 0,
            ];
        }
    }
}
