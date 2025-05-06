<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    //
    public function saveBranch(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'contact_no' => 'nullable|string',
                'city_id' => 'required|exists:cities,id',
                'salary_account' => 'required|exists:chart_of_accounts,id',
                'salary_advance_account' => 'required|exists:chart_of_accounts,id',
                'employee_loan_account' => 'required|exists:chart_of_accounts,id',
            ]);

            Branch::create($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Branch created successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function updateBranch(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|exists:branches,id',
                'name' => 'required|string',
                'address' => 'required|string',
                'contact_no' => 'nullable|string',
                'city_id' => 'required|exists:cities,id',
                'salary_account' => 'required|exists:chart_of_accounts,id',
                'salary_advance_account' => 'required|exists:chart_of_accounts,id',
                'employee_loan_account' => 'required|exists:chart_of_accounts,id',
            ]);

            $branch = Branch::findOrFail($validated['id']);
            $branch->update($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Branch updated successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function getBranches(){
        try{
            $branches = Branch::get();
            
            return response()->json([
                'success' => 1,
                'message' => 'Branches retreived successfully',
                'data' => $branches,
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
