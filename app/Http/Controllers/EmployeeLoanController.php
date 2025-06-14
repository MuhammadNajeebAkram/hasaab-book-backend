<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeLoanController extends Controller
{
    //
    public function saveLoan(Request $request){
        DB::beginTransaction();
        try{
            $validated = $request->validate([
                'type' => 'required|string',
                'payment_mode' => 'required|in:cash,bank,journal',
                'voucher_date' => 'date',
                'description' => 'nullable|string',
                'transaction_no' => 'nullable|string',
                'employee_id' => 'required|numeric|exists:employees,id',
                'reason' => 'nullable|string',
                'amount' => 'required|numeric',
                'installments' => 'required|numeric',
                'installment_amount' => 'required|numeric',
                'repayment_start_year' => 'required|numeric',
                'repayment_start_month' => 'required|numeric',
                'account_id' => 'required|numeric|exists:chart_of_accounts,id',
            ]);

            $validated['issue_date'] = $validated['voucher_date'];

            $voucherController = new VoucherController();

            $data = $voucherController->saveDraftVoucher($request);
            $responseData = $data->getData();

            $Voucher = $responseData->voucher;
            $validated['voucher_id'] = $Voucher->id;
            $validated['status'] = 'in process';

            EmployeeLoan::create($validated);

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'Employee Loan Voucher saved successfully',               
                
            ], 200);


        }catch(\Exception $e){

            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);

        }
    }

    public function updateLoan(Request $request){

        DB::beginTransaction();
        try{
            $validated = $request->validate([
                'id' => 'required|exists:vouchers,id',
                'payment_mode' => 'required|in:cash,bank,journal',
                'voucher_date' => 'date',
                'description' => 'nullable|string',
                'transaction_no' => 'nullable|string',
                'employee_id' => 'required|numeric|exists:employees,id',
                'reason' => 'nullable|string',
                'amount' => 'required|numeric',
                'installments' => 'required|numeric',
                'installment_amount' => 'required|numeric',
                'repayment_start_year' => 'required|numeric',
                'repayment_start_month' => 'required|numeric',
                'account_id' => 'required|numeric|exists:chart_of_accounts,id',
            ]);

            $validated['issue_date'] = $validated['voucher_date'];

            $voucherController = new VoucherController();
            $data = $voucherController->updateDraftVoucher($request);

            $loan = EmployeeLoan::where('voucher_id', $validated['id'])->firstOrFail();        

            $loan->update($validated);

            if($request->is_posted){

                $validated['employee_loan_id'] = $loan->id;
                $validated['voucher_id'] = $validated['id'];
                $validated['payment_type'] = 'issued';
                $validated['status'] = 'active';

                EmployeeLoanEntry::create($validated);

            }
           

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'Loan updated successfully',
               
                
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

    public function getLoanByVoucher($voucher_id){
        try{
            $data = EmployeeLoan::where('voucher_id', $voucher_id)
            ->first();

            return response()->json([ 
                'success' => 1,
                'message' => 'Emploee Loan retreived successfully',
               'data' => $data,
               'voucher' => $voucher_id,
                
            ], 200);



        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateLoanStatus($employee_id)
{
    $loan = EmployeeLoan::where('employee_id', $employee_id)
        ->where('status', 'active')
        ->first();

    if ($loan) {
        $balance = EmployeeLoanEntry::where('employee_loan_id', $loan->id)
            ->selectRaw("
                SUM(CASE WHEN payment_type = 'issued' THEN amount ELSE 0 END) AS total_issued,
                SUM(CASE WHEN payment_type = 'recovered' THEN amount ELSE 0 END) AS total_recovered
            ")
            ->first();

        $balanceAmount = ($balance->total_issued ?? 0) - ($balance->total_recovered ?? 0);

        if ($balanceAmount == 0) {
            $loan->update(['status' => 'settled']);
        }

        return response()->json([
            'success' => 1,
        ]);
    }

    return response()->json([
        'success' => 0,
    ]);

}

}
