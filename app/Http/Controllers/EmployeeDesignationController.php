<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDesignation;
use Illuminate\Http\Request;

class EmployeeDesignationController extends Controller
{
    //
    public function saveDesignation(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string',
                'description' => 'nullable|string',
            ]);

            EmployeeDesignation::create($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Designation created successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateDesignation(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|numeric',
                'name' => 'required|string',
                'description' => 'nullable',
            ]);

            $designation = EmployeeDesignation::findOrFail($validated['id']);

            $designation->update($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Designation updated successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDesignations(){
        try{
            
            $designation = EmployeeDesignation::get();
           

            return response()->json([
                'success' => 1,
                'message' => 'Designations retrived successfully',
                'data' => $designation,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }



    }
}
