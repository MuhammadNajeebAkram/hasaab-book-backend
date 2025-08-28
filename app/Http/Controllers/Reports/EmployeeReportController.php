<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\AdvanceSalary;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeReportController extends Controller
{
    //
    public function getSalaryPaidByMonth(Request $request, int $month){
        try{
            $year = $request->input('year');

            $salaryInfo = Salary::with('employee')
            ->where('status', 'paid')
            ->where('month', $month)
            ->where('year', $year)
            // Use a join to be able to order by the employee name
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->select('salaries.*') // Select all columns from the salaries table
            ->orderBy('employees.name') // Order by the name column in the joined table
            ->get();

        // Now, you can transform the data for a cleaner response
        $transformedData = $salaryInfo->map(function ($salary) {
            return [
                'Salary' => $salary->basic_salary,
                'Overtime' => $salary->overtime,
                'OtherAllowance' => $salary->other_allowance,
                'AdvanceDeduction' => $salary->advance_deduction,
                'LoanDeduction' => $salary->loan_deduction,
                'NetSalary' => $salary->net_salary,
                'Employee' => $salary->employee->name, // Access the employee's name from the relationship
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedData
        ], 200);

        }
        catch (\Illuminate\Database\QueryException $e) {
            // Catch database-specific errors (e.g., the SIGNAL you have in the procedure)
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Catch other general errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEmployeesLoanLedgerSummary(){
        try{
            $ledger = DB::table('GetEmployeesLoanLedgerSummaryReport')->get();

            return response()->json([
                'success' => true,
                'data' => $ledger,
            ], 200);

        }
        catch (\Illuminate\Database\QueryException $e) {
            // Catch database-specific errors (e.g., the SIGNAL you have in the procedure)
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Catch other general errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEmployeesAdvanceLedgerSummary(Request $request, $month){
        $year = $request->input('year');
        try{
            $advances = AdvanceSalary::with(['employee', 'voucher'])
            ->whereYear('advance_date', $year)
            ->whereMonth('advance_date', $month)
            ->whereHas('voucher', function ($query) {
                $query->where('is_posted', 1);
            })
            ->get();

          /*  $postedAdvances = $advances->filter(function($advance) {
                return $advance->voucher->is_posted == 1;
            });*/

            if ($advances->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No advance salary data found for the selected month and year with a posted status.'], 404);
            }

            $formattedAdvances = $advances->map(function($advance) {
                return [
                    
                    'amount' => $advance->amount,
                    'advance_date' => $advance->advance_date,
                    'employee_name' => optional($advance->employee)->name, // Use optional() to prevent errors if relationship is null
                    
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedAdvances
            ]);



        }
        catch (\Illuminate\Database\QueryException $e) {
            // Catch database-specific errors (e.g., the SIGNAL you have in the procedure)
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Catch other general errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }

    }
}
