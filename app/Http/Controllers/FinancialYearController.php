<?php

namespace App\Http\Controllers;

use App\Models\FinancialYear;
use Illuminate\Http\Request;

class FinancialYearController extends Controller
{
    //
    public function getFinancialYears(){
        try{
            $years = FinancialYear::get();

            return response()->json([
                'success' => true,
                'data' => $years,
            ]);

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
