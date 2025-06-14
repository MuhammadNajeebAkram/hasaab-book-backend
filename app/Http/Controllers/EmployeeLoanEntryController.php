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
        $installment = 0;
        $balanceAmount = 0;

        $empLoan = EmployeeLoan::where('employee_id', $employee_id)
            ->where('status', 'active')
            ->first();

        if ($empLoan) {
            $balance = EmployeeLoanEntry::where('employee_loan_id', $empLoan->id)
                ->selectRaw("
                    SUM(CASE WHEN payment_type = 'issued' THEN amount ELSE 0 END) AS total_issued,
                    SUM(CASE WHEN payment_type = 'recovered' THEN amount ELSE 0 END) AS total_recovered
                ")
                ->first();

            $totalIssued = $balance->total_issued ?? 0;
            $totalRecovered = $balance->total_recovered ?? 0;

            $balanceAmount = $totalIssued - $totalRecovered;

            $now = Carbon::now();

            if (
                $now->year > $empLoan->repayment_start_year ||
                ($now->year == $empLoan->repayment_start_year && $now->month >= $empLoan->repayment_start_month)
            ) {
                $installment = $empLoan->installment_amount;
            }
        }

        return [
            'success' => 1,
            'data' => [
                'installment' => $installment,
                'balance_amount' => $balanceAmount,
            ],
        ];
    } catch (\Exception $e) {
        return [
            'success' => -1,
            'message' => $e->getMessage(),
            'data' => null,
        ];
    }
}

}
