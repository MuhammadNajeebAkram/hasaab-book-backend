<?php

namespace App\Http\Controllers;

use App\Models\AdvanceSalary;
use App\Models\AdvanceSalaryEntry;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanEntry;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    //
    public function saveSalary(Request $request){
        
            DB::beginTransaction();
            try{
                $validated = $request->validate([
                    'type' => 'required|string',
                    'payment_mode' => 'required|in:cash,bank,journal',
                    'payment_account' => 'required|exists:chart_of_accounts,id',
                    'voucher_date' => 'nullable|date',
                    'description' => 'nullable|string',
                    'transaction_no' => 'nullable|string',
                    'employee_id' => 'required|numeric|exists:employees,id',
                    'basic_salary' => 'required|numeric',
                    'house_rent' => 'nullable|numeric',
                    'medical_allowance' => 'nullable|numeric',
                    'travel_allowance' => 'nullable|numeric',
                    'other_allowance' => 'nullable|numeric',
                    'overtime' => 'nullable|numeric',
                    'overtime_hours' => 'nullable|numeric',
                    'dot_x' => 'nullable|numeric',
                    'advance_deduction' => 'nullable|numeric',
                    'loan_deduction' => 'nullable|numeric',
                    'gross_salary' => 'required|numeric',
                    'net_salary' => 'required|numeric',
                    'year' => 'required|numeric',
                    'month' => 'required|numeric',
                    'account_id' => 'required|numeric|exists:chart_of_accounts,id',             // salary account
                    'advance_account' => 'required|numeric|exists:chart_of_accounts,id',
                    'loan_account' => 'required|numeric|exists:chart_of_accounts,id',
                    'overtime_account' => 'required|numeric|exists:chart_of_accounts,id',
                    'other_allowance_account' => 'required|numeric|exists:chart_of_accounts,id',
                    
                ]);

                $exists = Salary::where('employee_id', $validated['employee_id'])
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->exists();

                if($exists){
                    return response()->json([
                        'success' => 0,
                        'message' => 'Salary voucher already has been created',
                        
                    ], 409);
                }

                $entries = [
                    ['account_id' => $validated['payment_account'], 'amount' => $validated['net_salary'], 'description' => $validated['description'], 'type' => 'credit'],
                    ['account_id' => $validated['account_id'], 'amount' => $validated['basic_salary'], 'description' => $validated['description'], 'type' => 'debit'],
                ];

                if($validated['advance_deduction'] > 0){
                    $entries[] = ['account_id' => $validated['advance_account'], 'amount' => $validated['advance_deduction'], 'description' => $validated['description'], 'type' => 'credit'];

                }

                if($validated['loan_deduction'] > 0){
                    $entries[] = ['account_id' => $validated['loan_account'], 'amount' => $validated['loan_deduction'], 'description' => $validated['description'], 'type' => 'credit'];
                }

                if($validated['overtime'] > 0){
                    $entries[] = ['account_id' => $validated['overtime_account'], 'amount' => $validated['overtime'], 'description' => $validated['description'], 'type' => 'debit'];
                }

                if($validated['other_allowance'] > 0){
                    $entries[] = ['account_id' => $validated['other_allowance_account'], 'amount' => $validated['other_allowance'], 'description' => $validated['description'], 'type' => 'debit'];
                }

                $request['entries'] = $entries;
    
                //$validated['payment_date'] = $validated['voucher_date'];
    
                $voucherController = new VoucherController();
                $data = $voucherController->saveDraftVoucher($request);
                $responseData = $data->getData();
                //dd($responseData);
               // $mess = $responseData->message;
                
                $Voucher = $responseData->voucher;
                $validated['voucher_id'] = $Voucher->id;
                $validated['status'] = 'pending';
    
               
               Salary::create($validated);
    
                DB::commit();
    
                return response()->json([
                    'success' => 1,
                    'message' => 'Salary Voucher saved successfully',
                    'entries' => $entries,
                   
                    
                ], 200);

        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateSalary(Request $request){
        DB::beginTransaction();
        try{
            $validated = $request->validate([
                'id' => 'required|exists:vouchers,id',
                'type' => 'required|string',
                    'payment_mode' => 'required|in:cash,bank,journal',
                    'payment_account' => 'required|exists:chart_of_accounts,id',
                    'voucher_date' => 'nullable|date',
                    'description' => 'nullable|string',
                    'transaction_no' => 'nullable|string',
                    'employee_id' => 'required|numeric|exists:employees,id',
                    'basic_salary' => 'required|numeric',
                    'house_rent' => 'nullable|numeric',
                    'medical_allowance' => 'nullable|numeric',
                    'travel_allowance' => 'nullable|numeric',
                    'other_allowance' => 'nullable|numeric',
                    'overtime' => 'nullable|numeric',
                    'overtime_hours' => 'nullable|numeric',
                    'dot_x' => 'nullable|numeric',
                    'advance_deduction' => 'nullable|numeric',
                    'loan_deduction' => 'nullable|numeric',
                    'gross_salary' => 'required|numeric',
                    'net_salary' => 'required|numeric',
                    'year' => 'required|numeric',
                    'month' => 'required|numeric',
                    'account_id' => 'required|numeric|exists:chart_of_accounts,id',             // salary account
                    'advance_account' => 'required|numeric|exists:chart_of_accounts,id',
                    'loan_account' => 'required|numeric|exists:chart_of_accounts,id',
                    'overtime_account' => 'required|numeric|exists:chart_of_accounts,id',
                    'other_allowance_account' => 'required|numeric|exists:chart_of_accounts,id',
            ]);

            $entries = [
                ['account_id' => $validated['payment_account'], 'amount' => $validated['net_salary'], 'description' => $validated['description'], 'type' => 'credit'],
                ['account_id' => $validated['account_id'], 'amount' => $validated['basic_salary'], 'description' => $validated['description'], 'type' => 'debit'],
            ];

            if($validated['advance_deduction'] > 0){
                $entries[] = ['account_id' => $validated['advance_account'], 'amount' => $validated['advance_deduction'], 'description' => $validated['description'], 'type' => 'credit'];

            }

            if($validated['loan_deduction'] > 0){
                $entries[] = ['account_id' => $validated['loan_account'], 'amount' => $validated['loan_deduction'], 'description' => $validated['description'], 'type' => 'credit'];
            }

            if($validated['overtime'] > 0){
                $entries[] = ['account_id' => $validated['overtime_account'], 'amount' => $validated['overtime'], 'description' => $validated['description'], 'type' => 'debit'];
            }

            if($validated['other_allowance'] > 0){
                $entries[] = ['account_id' => $validated['other_allowance_account'], 'amount' => $validated['other_allowance'], 'description' => $validated['description'], 'type' => 'debit'];
            }

            $request['entries'] = $entries;

            $voucherController = new VoucherController();
            $data = $voucherController->updateDraftVoucher($request);
            $responseData = $data->getData();
            //dd($responseData);
           // $mess = $responseData->message;
            
            $Voucher = $responseData->voucher;
            $validated['voucher_id'] = $Voucher->id; 
           
            if ($request->is_posted) {
                $validated['status'] = 'paid';
                $validated['payment_date'] = Carbon::now()->toDateString();
            
                $salary = Salary::where('voucher_id', $validated['voucher_id'])->firstOrFail();
                $salary->update($validated);
                if ($validated['loan_deduction'] > 0) {
                    $empLoans = EmployeeLoan::where('employee_id', $validated['employee_id'])
                        ->where('status', 'active')
                        ->get();
    
                    $remainingDeductionAmount = $validated['loan_deduction']; // Track the amount still to be deducted
    
                    foreach ($empLoans as $empLoan) {
                        if ($remainingDeductionAmount <= 0) {
                            break; // No more deduction needed, exit loop
                        }
    
                        $balance = EmployeeLoanEntry::where('employee_loan_id', $empLoan->id)
                            ->selectRaw("
                                SUM(CASE WHEN payment_type = 'issued' THEN amount ELSE 0 END) AS total_issued,
                                SUM(CASE WHEN payment_type = 'recovered' THEN amount ELSE 0 END) AS total_recovered
                            ")
                            ->first();
    
                        // Corrected typo: total_recovered
                        $loanBalance = $balance->total_issued - $balance->total_recovered;
    
                        if ($loanBalance <= 0) {
                            // This loan is already settled or has no balance, skip it
                            continue;
                        }
    
                        // Determine the amount to recover from the current loan
                        $amountToRecoverFromCurrentLoan = min($remainingDeductionAmount, $loanBalance);
    
                        if ($amountToRecoverFromCurrentLoan > 0) {
                            $loanEntries = [
                                'employee_loan_id' => $empLoan->id,
                                'voucher_id' => $validated['voucher_id'],
                                'payment_type' => 'recovered',
                                'amount' => $amountToRecoverFromCurrentLoan,
                            ];
    
                            EmployeeLoanEntry::create($loanEntries);
    
                            // Reduce the remaining deduction amount
                            $remainingDeductionAmount -= $amountToRecoverFromCurrentLoan;
    
                            // If the current loan is now fully recovered, update its status
                            if ($amountToRecoverFromCurrentLoan == $loanBalance) {
                                $loan = EmployeeLoan::where('id', $empLoan->id);
                                $loan->update(['status' => 'settled']);
                            }
                        }
                    }
                }
               
            if($validated['advance_deduction']){

                $advances = AdvanceSalary::where('employee_id', $validated['employee_id'])
                ->where('is_settled', 0)
                ->get();

                foreach($advances as $advance){

                    $advanceEntries = [
                        'advance_id' => $advance->id,
                        'voucher_id' => $validated['voucher_id'],
                        'payment_type' => 'recovered',
                        'amount' => $advance->amount,
                    ];
                    AdvanceSalaryEntry::create($advanceEntries);

                    $advance->update(['is_settled' => 1]);

                }
               

            }
                
            }
            else{
                $salary = Salary::where('voucher_id', $validated['voucher_id'])->firstOrFail();
                $salary->update($validated);

            }
            

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'Salary Voucher updated successfully',
                'resData' => $responseData,
                'entries' => $entries,
            ], 200);
            


        } catch(\Exception $e){

            DB::rollBack();
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }

    }

    public function getSalaryByVoucher($voucher_id){
        try{
            $salary = Salary::where('voucher_id', $voucher_id)
            ->get()->first();

            return response()->json([
                'success' => 1,
                'message' => 'Salary Voucher retreived successfully',
               'data' => $salary,
                
            ], 200);



        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);

        }
    }
}
