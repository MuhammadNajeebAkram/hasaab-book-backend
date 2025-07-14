<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    //
    public function getLedgerReport(Request $request, int $accountId){
         // Get the start and end dates from the request.
         $startDate = $request->input('start_date');
         $endDate = $request->input('end_date');
         
         // You might want to add input validation here
         if (empty($startDate) || empty($endDate)) {
             return response()->json([
                 'success' => false,
                 'message' => 'Start date and end date are required.'
             ], 400);
         }
 
         try {
             // Call the stored procedure using DB::select()
             // The parameters are passed as an array to the second argument.
             $ledger = DB::select('CALL GetAccountLedger(?, ?, ?)', [
                 $accountId,
                 $startDate,
                 $endDate
             ]);
             
             // The stored procedure also handles errors, but we can catch them here too.
             if (!empty($ledger) && isset($ledger[0]->Message) && $ledger[0]->Message === 'Error: Account ID not found.') {
                  return response()->json([
                     'success' => false,
                     'message' => $ledger[0]->Message
                 ], 404); // Not Found
             }
 
             return response()->json([
                 'success' => true,
                 'message' => 'Account ledger retrieved successfully.',
                 'data' => $ledger
             ]);
 
         } catch (\Illuminate\Database\QueryException $e) {
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

    public function getDailyReport(Request $request, int $accountId){
        // Get the start and end dates from the request.
        $_Date = $request->input('_date');
        
        
        // You might want to add input validation here
        if (empty($_Date)) {
            return response()->json([
                'success' => false,
                'message' => 'Date is required.'
            ], 400);
        }

        try {
            $statement = DB::getPDO()->prepare("CALL GetDailyCashReport(?, ?)");
            $statement->execute([$accountId, $_Date]);
        
        // Fetch the first result set (Summary)
        $summary = $statement->fetchAll(DB::getPDO()::FETCH_ASSOC);

        // Advance to the next result set (Transactions)
        $statement->nextRowset();
        $transactions = $statement->fetchAll(DB::getPDO()::FETCH_ASSOC);

        // Close the statement
        $statement->closeCursor();

            return response()->json([
                'success' => true,
                'message' => 'Account ledger retrieved successfully.',
                'data' => ['summary' => $summary[0], 'transactions' => $transactions],
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
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
