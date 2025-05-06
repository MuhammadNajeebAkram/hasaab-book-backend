<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\VoucherController;
use App\Models\AdvanceSalary;
use App\Models\AdvanceSalaryEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdvanceSalaryController extends Controller
{
    //

    public function saveAdvances(Request $request){

        DB::beginTransaction(); 
        try{
            $validated = $request->validate([
                'type' => 'required|string',
                'payment_mode' => 'required|in:cash,bank,journal',
                'voucher_date' => 'nullable|date',
                'description' => 'nullable|string',
                'transaction_no' => 'nullable|string',
                'employee_id' => 'required|numeric|exists:employees,id',
                'reason' => 'nullable|string',
                'amount' => 'required|numeric',
            ]);

            $validated['advance_date'] = $validated['voucher_date'];

            $voucherController = new VoucherController();
            $data = $voucherController->saveDraftVoucher($request);
            $responseData = $data->getData();
            //dd($responseData);
           // $mess = $responseData->message;
           
            $Voucher = $responseData->voucher;
            $validated['voucher_id'] = $Voucher->id;

            foreach($request->entries as $Entry){
                if($Entry['is_advance'] === 1){
                    $validated['account_id'] = $Entry['account_id'];
                    
                    break;

                }
            }

            AdvanceSalary::create($validated);

            DB::commit();

            return response()->json([
                'success' => 1,
                'message' => 'Advance Voucher saved successfully',
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

    public function updateAdvances(Request $request){

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
            ]);

            $validated['advance_date'] = $validated['voucher_date'];

            $voucherController = new VoucherController();
            $data = $voucherController->updateDraftVoucher($request);
           

            foreach($request->entries as $Entry){
                if($Entry['is_advance'] === 1){
                    $validated['account_id'] = $Entry['account_id'];
                    
                    break;

                }
            }

            $advance = AdvanceSalary::where('voucher_id', $validated['id'])->firstOrFail();

            $advance->update($validated);

            if($request->is_posted){

                $validated['advance_id'] = $advance->id;
                $validated['voucher_id'] = $validated['id'];
                $validated['payment_type'] = 'issued';

                AdvanceSalaryEntry::create($validated);
               

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

    public function getAdvanceSalaryByVoucher($voucher_id){
        try{
            $data = AdvanceSalary::where('voucher_id', $voucher_id)
            ->first();

            return response()->json([
                'success' => 1,
                'message' => 'Advance Salaries saved successfully',
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
