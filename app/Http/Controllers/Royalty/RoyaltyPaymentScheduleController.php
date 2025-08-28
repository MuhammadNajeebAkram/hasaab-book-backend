<?php

namespace App\Http\Controllers\Royalty;

use App\Http\Controllers\Controller;
use App\Models\RoyaltyCheque;
use App\Models\RoyaltyPaymentSchedule;
use App\Models\RoyaltyPaymentScheduleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoyaltyPaymentScheduleController extends Controller
{
    //
    public function saveSchedule(Request $request){

        $validated = $request->validate([
            'professor_id' => 'required|exists:professors,id',            
            'financial_year' => 'required|numeric',
            'instructions' => 'nullable',         


        ]);

        try{
            DB::beginTransaction();
            $schedule = RoyaltyPaymentSchedule::create($validated);

            

            $details = $request->details;
                
                foreach($details as $detail){
                    $data = [
                        'royalty_schedule_id' => $schedule->id,
                        'payment_date' => $detail['payment_date'],
                        'bank_account_id' => $detail['bank_account_id'],
                        'cheque_no' => $detail['cheque_no'],
                        'amount' => $detail['amount'],
                        'status' => 'pending',

                    ];

                    RoyaltyPaymentScheduleDetail::create($data);

                }
            

            DB::commit();

        }
        catch (\Illuminate\Database\QueryException $e) {
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            // Catch other general errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }

        
    }

    public function updateData(Request $request){

       
       $validated = $request->validate([
            
            'professor_id' => 'required|exists:professors,id',
            'financial_year' => 'required|numeric',
            'instructions' => 'nullable',
            'details.*.id' => 'required|numeric',
            'details.*.payment_date' => 'required|date',
            'details.*.bank_account_id' => 'required|numeric|exists:chart_of_accounts,id',
            'details.*.cheque_no' => 'nullable|string',
            'details.*.amount' => 'required|numeric',
            'details.*.flag' => 'nullable|numeric',
            'details.*.paid_date' => 'nullable|date',
    
        ]);        
        
       
    
        try {
            DB::beginTransaction();
            $id = $request->id;
    
            $schedule = RoyaltyPaymentSchedule::find($id);
    
           
            $schedule->update($validated);
    
            $details = $request->details;
    
            foreach ($details as $detail) {
                $detail = (object) $detail; // Cast to object for cleaner access
    
                if ($detail->id == 0) {
                    // Case 1: Create a new detail record.
                    $data = [
                        'royalty_schedule_id' => $schedule->id,
                        'payment_date' => $detail->payment_date,
                        'bank_account_id' => $detail->bank_account_id,
                        'cheque_no' => $detail->cheque_no,
                        'amount' => $detail->amount,
                        'status' => 'pending',
                    ];
    
                    RoyaltyPaymentScheduleDetail::create($data);
                } else {
                    // Case 2 & 3: Handle existing detail records.
                    // Find the detail record by its ID.
                    $d = RoyaltyPaymentScheduleDetail::find($detail->id);
    
                    if ($d) { // Check if the detail record exists
                        if ($detail->flag == 1) {
                            // Case 2: The flag is 1, so delete the record.
                            $d->delete();
                        } else {
                            // Case 3: The flag is not 1 (it's 0 or null), so update the record.
                            $d->update([
                                'payment_date' => $detail->payment_date,
                                'bank_account_id' => $detail->bank_account_id,
                                'cheque_no' => $detail->cheque_no,
                                'amount' => $detail->amount,
                                //'paid_date' => $detail->paid_date, // Add this line to handle the paid_date
                            ]);
                        }
                    }
                }
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
            ], 200);
    
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    

public function updateSchedule(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|numeric',
        'professor_id' => 'required|exists:professors,id',
        'financial_year' => 'required|numeric',
        'instructions' => 'nullable',
        'details.*.id' => 'required|numeric',
        'details.*.payment_date' => 'required|date',
        'details.*.bank_account_id' => 'required|numeric|exists:chart_of_accounts,id',
        'details.*.cheque_no' => 'nullable|string',
        'details.*.amount' => 'required|numeric',
        'details.*.flag' => 'nullable|numeric',
        'details.*.paid_date' => 'nullable|date',

    ]);

    return response()->json([
        'success' => false,
    ]);

   
/*
    try {
        DB::beginTransaction();
        $id = $request->id;

        $schedule = RoyaltyPaymentSchedule::find($id);

        $data = [
            'professor_id' => $validated['professor_id'],
            'instructions' => $validated['instructions'],
        ];
        $schedule->update($data);

        $details = $request->details;

        foreach ($details as $detail) {
            $detail = (object) $detail; // Cast to object for cleaner access

            if ($detail->id == 0) {
                // Case 1: Create a new detail record.
                $data = [
                    'royalty_schedule_id' => $schedule->id,
                    'payment_date' => $detail->payment_date,
                    'bank_account_id' => $detail->bank_account_id,
                    'cheque_no' => $detail->cheque_no,
                    'amount' => $detail->amount,
                    'status' => 'pending',
                ];

                RoyaltyPaymentScheduleDetail::create($data);
            } else {
                // Case 2 & 3: Handle existing detail records.
                // Find the detail record by its ID.
                $d = RoyaltyPaymentScheduleDetail::find($detail->id);

                if ($d) { // Check if the detail record exists
                    if ($detail->flag == 1) {
                        // Case 2: The flag is 1, so delete the record.
                        $d->delete();
                    } else {
                        // Case 3: The flag is not 1 (it's 0 or null), so update the record.
                        $d->update([
                            'payment_date' => $detail->payment_date,
                            'bank_account_id' => $detail->bank_account_id,
                            'cheque_no' => $detail->cheque_no,
                            'amount' => $detail->amount,
                            //'paid_date' => $detail->paid_date, // Add this line to handle the paid_date
                        ]);
                    }
                }
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
        ], 200);

    } catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ], 500);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred: ' . $e->getMessage()
        ], 500);
    }*/
}

    public function getRoyaltySchedules(Request $request){
        try{
            $schedules = RoyaltyPaymentSchedule::with(['professor:id,name', 'financialYear'
            ])
            ->get();

            $transform = $schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'name' => $schedule->professor->name,
                    'year' => $schedule->financialYear->name,
                    'professor_id' => $schedule->professor_id,
                    'year_id' => $schedule->financial_year,
                    'is_active' => $schedule->is_active,
                    'instructions' => $schedule->instructions,
                ]; 


            });

            return response()->json([
                'success' => true,
                'data' => $transform,
            ], 200);

        }
        catch (\Illuminate\Database\QueryException $e) {
            
            
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

    public function getScheduleWithDetail($id){
        try{
            $detail = RoyaltyPaymentSchedule::with('scheduleDetails')->find($id);

            return response()->json([
                'success' => true,
                'data' => $detail,
            ], 200);

        }
        catch (\Illuminate\Database\QueryException $e) {
            
            
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
