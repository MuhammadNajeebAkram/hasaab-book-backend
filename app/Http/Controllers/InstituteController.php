<?php

namespace App\Http\Controllers;

use App\Models\Institute;
use Illuminate\Http\Request;

class InstituteController extends Controller
{
    //
    public function saveInstitute(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string',
                'address' => 'nullable|string',
                'contact_no' => 'nullable|string',
                'city_id' => 'required|exists:cities,id',               
            ]);

            Institute::create($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Institute created successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateInstitute(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|numeric',
                'name' => 'required|string',  
                'address' => 'nullable|string',
                'contact_no' => 'nullable|string',
                'city_id' => 'required|exists:cities,id',                
            ]);

            $institute = Institute::findOrFail($validated['id']);

            $institute->update($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Institute updated successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getInstitutes($city_id){
        try{
            
            $institutes = Institute::where('institutes.city_id', $city_id) // Specify table for city_id for clarity
                ->join('cities', 'institutes.city_id', '=', 'cities.id') // Join with the cities table
                ->select(
                    'institutes.*', // Select all columns from the institutes table
                    'cities.name as city_name' // Select city's name and alias it as 'city_name'
                )
                ->get();
           

            return response()->json([
                'success' => 1,
                'message' => 'Institutes retrived successfully',
                'data' => $institutes,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }



    }
}
