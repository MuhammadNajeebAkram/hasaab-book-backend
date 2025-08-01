<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use Illuminate\Http\Request;

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
}
