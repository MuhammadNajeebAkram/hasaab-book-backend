<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    //
    public function saveDivision(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string',
                'province_id' => 'required|numeric|exists:provinces,id',
            ]);

            Division::create($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Division created successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateDivision(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|numeric',
                'name' => 'required|string',
                'province_id' => 'required|numeric|exists:provinces,id',
            ]);

            $division = Division::findOrFail($validated['id']);

            $division->update($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Division updated successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getDivisions($province){
        try{
            $divisions = Division::where('province_id', $province)
            ->with('province:id,name')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'name' => $entry->name,
                    'province_id' => $entry->province_id,
                    'province_name' => $entry->province->name,
                ];
            });

            return response()->json([
                'success' => 1,
                'message' => 'Divisions retrived successfully',
                'data' => $divisions,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }



    }

}
