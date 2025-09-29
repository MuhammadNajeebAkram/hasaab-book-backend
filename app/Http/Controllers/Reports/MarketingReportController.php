<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MarketingReportController extends Controller
{
    //
    public function getMarketingReport(Request $request, $is_summary){
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
            $report = \DB::select('CALL GetMarketingExpensesReport(?, ?, ?)', [
                $startDate,
                $endDate,
                $is_summary
            ]);
            
            // The stored procedure also handles errors, but we can catch them here too.
            if (!empty($report) && isset($report[0]->Message) && $report[0]->Message === 'Error: No data found.') {
                 return response()->json([
                    'success' => false,
                    'message' => $report[0]->Message
                ], 404); // Not Found
            }

            return response()->json([
                'success' => true,
                'message' => 'Marketing report retrieved successfully.',
                'data' => $report
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
