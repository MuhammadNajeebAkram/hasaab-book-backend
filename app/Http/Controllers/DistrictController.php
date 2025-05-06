<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    //
    public function saveDistrict(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string',
                'division_id' => 'required|numeric|exists:divisions,id',
            ]);

            District::create($validated);

            return response()->json([
                'success' => 1,
                'message' => 'District created successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateDistrict(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|numeric',
                'name' => 'required|string',
                'division_id' => 'required|numeric|exists:divisions,id',
            ]);

            $district = District::findOrFail($validated['id']);

            $district->update($validated);

            return response()->json([
                'success' => 1,
                'message' => 'District updated successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDistricts($division){
        try{
            $districts = District::where('division_id', $division)
            ->with('division:id,name')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'name' => $entry->name,
                    'division_id' => $entry->division_id,
                    'division_name' => $entry->division->name,
                ];
            });

            return response()->json([
                'success' => 1,
                'message' => 'Districts retrived successfully',
                'data' => $districts,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }



    }
}
