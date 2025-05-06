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
                    'advance_deduction' => 'nullable|numeric',
                    'loan_deduction' => 'nullable|numeric',
                    'gross_salary' => 'required|numeric',
                    'net_salary' => 'required|numeric',
                    'year' => 'required|numeric',
                    'month' => 'required|numeric',
                    'account_id' => 'required|numeric|exists:chart_of_accounts,id',             // salary account
                    'advance_account' => 'required|numeric|exists:chart_of_accounts,id',
                    'loan_account' => 'required|numeric|exists:chart_of_accounts,id',
                ]);

                $exists = Salary::where('employee_id', $validated['employee_id'])
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->exists();

                if($exists){
                    return response()->json([
                        'success' => 0,
                        'message' => 'Salary voucher already has been created',
                        
                    ]);
                }

                $entries = [
                    ['account_id' => $validated['payment_account'], 'amount' => $validated['net_salary'], 'description' => 'Payment Account', 'type' => 'credit'],
                    ['account_id' => $validated['account_id'], 'amount' => $validated['basic_salary'], 'description' => $validated['description'], 'type' => 'debit'],
                ];

                if($validated['advance_deduction'] > 0){
                    $entries[] = ['account_id' => $validated['advance_account'], 'amount' => $validated['advance_deduction'], 'description' => $validated['description'], 'type' => 'credit'];

                }

                if($validated['loan_deduction'] > 0){
                    $entries[] = ['account_id' => $validated['loan_account'], 'amount' => $validated['loan_deduction'], 'description' => $validated['description'], 'type' => 'credit'];
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
                //'voucher_date' => 'nullable|date',
                'description' => 'nullable|string',
                'transaction_no' => 'nullable|string',
                'employee_id' => 'required|numeric|exists:employees,id',
                'basic_salary' => 'required|numeric',
                'house_rent' => 'nullable|numeric',
                'medical_allowance' => 'nullable|numeric',
                'travel_allowance' => 'nullable|numeric',
                'other_allowance' => 'nullable|numeric',
                'overtime' => 'nullable|numeric',
                'advance_deduction' => 'nullable|numeric',
                'loan_deduction' => 'nullable|numeric',
                'gross_salary' => 'required|numeric',
                'net_salary' => 'required|numeric',
                'year' => 'required|numeric',
                'month' => 'required|numeric',
                'account_id' => 'required|numeric|exists:chart_of_accounts,id',             // salary account
                'advance_account' => 'required|numeric|exists:chart_of_accounts,id',
                'loan_account' => 'required|numeric|exists:chart_of_accounts,id',
            ]);

            $entries = [
                ['account_id' => $validated['payment_account'], 'amount' => $validated['net_salary'], 'description' => 'Payment Account', 'type' => 'credit'],
                ['account_id' => $validated['account_id'], 'amount' => $validated['basic_salary'], 'description' => $validated['description'], 'type' => 'debit'],
            ];

            if($validated['advance_deduction'] > 0){
                $entries[] = ['account_id' => $validated['advance_account'], 'amount' => $validated['advance_deduction'], 'description' => $validated['description'], 'type' => 'credit'];

            }

            if($validated['loan_deduction'] > 0){
                $entries[] = ['account_id' => $validated['loan_account'], 'amount' => $validated['loan_deduction'], 'description' => $validated['description'], 'type' => 'credit'];
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
            if($validated['loan_deduction'] > 0){

                $empLoan = EmployeeLoan::where('employee_id', $validated['employee_id'])
                ->where('status', 'active')
                ->first();
        
            if ($empLoan) {
                $loanEntries = [
                    'employee_loan_id' => $empLoan->id,
                    'voucher_id' => $validated['voucher_id'],
                    'payment_type' => 'recovered',
                    'amount' => $validated['loan_deduction'],
                ];
        
                EmployeeLoanEntry::create($loanEntries);
        
                // Update loan status if fully recovered
                (new EmployeeLoanController())->updateLoanStatus($validated['employee_id']);
            }

            }
               
            if($validated['advance_deduction']){

                $advance = AdvanceSalary::where('employee_id', $validated['employee_id'])
                ->where('is_settled', 0)
                ->firstOrFail();

                if($advance){
                    $advanceEntries = [
                        'advance_id' => $advance->id,
                        'voucher_id' => $validated['voucher_id'],
                        'payment_type' => 'recovered',
                        'amount' => $validated['advance_deduction'],
                    ];
                    AdvanceSalaryEntry::create($advanceEntries);

                    $advance->update(['is_settled' => 1]);
                }

            }
                
            }
            

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'Salary Voucher updated successfully',
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
