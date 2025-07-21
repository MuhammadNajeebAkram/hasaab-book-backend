<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\RoyaltyPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoyaltyPaymentController extends Controller
{
    //
    public function saveRoyaltyPayment(Request $request){
        
        DB::beginTransaction();
        try{
            $validated = $request->validate([
                'type' => 'required|string',
                'payment_mode' => 'required|in:cash,bank',
                'payment_account' => 'required|exists:chart_of_accounts,id',
                'voucher_date' => 'nullable|date',
                'description' => 'nullable|string',
                'transaction_no' => 'nullable|string',
                'professor_id' => 'required|numeric|exists:professors,id',
                'amount' => 'required|numeric',                
                'royalty_period' => 'required|numeric',                
                'account_id' => 'required|numeric|exists:chart_of_accounts,id',             // royalty account
               
                
            ]);

            
            $entries = [
                ['account_id' => $validated['payment_account'], 'amount' => $validated['amount'], 'description' => $validated['description'], 'type' => 'credit'],
                ['account_id' => $validated['account_id'], 'amount' => $validated['amount'], 'description' => $validated['description'], 'type' => 'debit'],
            ];
            
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

           
           RoyaltyPayment::create($validated);

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

public function updateRoyaltyPayment(Request $request){
    DB::beginTransaction();
    try{
        $validated = $request->validate([
            'id' => 'required|exists:vouchers,id',
            'type' => 'required|string',
                'payment_mode' => 'required|in:cash,bank',
                'payment_account' => 'required|exists:chart_of_accounts,id',
                'voucher_date' => 'nullable|date',
                'description' => 'nullable|string',
                'transaction_no' => 'nullable|string',
                'professor_id' => 'required|numeric|exists:professors,id',
                'amount' => 'required|numeric',                
                'royalty_period' => 'required|numeric',                
                'account_id' => 'required|numeric|exists:chart_of_accounts,id',             // royalty account
        ]);

        $entries = [
            ['account_id' => $validated['payment_account'], 'amount' => $validated['amount'], 'description' => $validated['description'], 'type' => 'credit'],
            ['account_id' => $validated['account_id'], 'amount' => $validated['amount'], 'description' => $validated['description'], 'type' => 'debit'],
        ];
       

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
            $validated['payment_date'] = Carbon::parse($validated['voucher_date']);       
           
            
        }

        $royalty = RoyaltyPayment::where('voucher_id', $validated['voucher_id'])->firstOrFail();
        $royalty->update($validated); 
        

        DB::commit();

        return response()->json([
            'success' => 1,
            'message' => 'Royalty Payment updated successfully',
           
        ], 200);
        


    } catch(\Exception $e){ 

        DB::rollBack();
        return response()->json([
            'success' => -1,
            'message' => $e->getMessage(),
        ], 500);
    }

}

public function getRoyaltyPaymentByVoucher($voucher_id){

    try{
        $royalty = RoyaltyPayment::where('voucher_id', '=', $voucher_id)
        ->get();

        return response()->json([
            'success' => 1,
            'message' => 'Royalty Payment retreived successfully',
            'data' => $royalty,
            
        ], 200);

    }catch(\Exception $e){
        return response()->json([
            'success' => -1,
            'message' => $e->getMessage(),
        ], 500);
    }

}




}
