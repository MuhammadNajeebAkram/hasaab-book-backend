<?php

namespace App\Http\Controllers;

use App\Models\EmployeeBonus;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeBonusController extends Controller
{
    //
    public function saveEmployeeBonus(Request $request){

        DB::beginTransaction(); 
        try{
            $validated = $request->validate([
                'type' => 'required|string',
                'payment_mode' => 'required|in:cash,bank,journal',
                'voucher_date' => 'nullable|date',
                'description' => 'nullable|string',
                'transaction_no' => 'nullable|string',
                'employee_id' => 'required|numeric|exists:employees,id',
                'bonus_id' => 'required|numeric|exists:bonuses,id',
                'reason' => 'nullable|string',
                'amount' => 'required|numeric',
                'status' => 'required|in:pending,paid',
                'account_id' => 'required|numeric|exists:chart_of_accounts,id',
            ]);

            $validated['advance_date'] = $validated['voucher_date'];

            $voucherController = new VoucherController();
            $data = $voucherController->saveDraftVoucher($request);
            $responseData = $data->getData();
            //dd($responseData);
           // $mess = $responseData->message;
           
            $Voucher = $responseData->voucher;
            $validated['voucher_id'] = $Voucher->id;

            $validated['bonus_date'] = $validated['voucher_date'];

           
            EmployeeBonus::create($validated);

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'Employee bonus saved successfully',
               'data' => $responseData,
                
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

    public function updateEmployeeBonus(Request $request){

        DB::beginTransaction();
        try{
            $validated = $request->validate([
                'id' => 'required|exists:vouchers,id',
                'type' => 'required|string',
                'payment_mode' => 'required|in:cash,bank,journal',
                'voucher_date' => 'nullable|date',
                'description' => 'nullable|string',
                'transaction_no' => 'nullable|string',
                'employee_id' => 'required|numeric|exists:employees,id',
                'reason' => 'nullable|string',
                'amount' => 'required|numeric',
                'status' => 'required|in:pending,paid',
                'account_id' => 'required|numeric|exists:chart_of_accounts,id',
                'bonus_id' => 'required|numeric|exists:bonuses,id',
            ]);

            $validated['bonus_date'] = $validated['voucher_date'];

            $voucherController = new VoucherController();
            $data = $voucherController->updateDraftVoucher($request);
           

            
            $bonus = EmployeeBonus::where('voucher_id', $validated['id'])->firstOrFail();

            $bonus->update($validated);

            if($request->is_posted){

               

                $bonus->update($validated);
               

            }

           

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'Advance Voucher updated successfully',
               
                
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

    public function getBonusByVoucher($voucher_id){
        try{
            $data = EmployeeBonus::where('voucher_id', $voucher_id)
            ->first();

            return response()->json([
                'success' => 1,
                'message' => 'Employee Bonus Voucher retrieved successfully',
               'data' => $data,
                
            ], 200);



        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
