<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    //
    public function saveCity(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string',
                'district_id' => 'required|numeric|exists:districts,id',
            ]);

            City::create($validated);

            return response()->json([
                'success' => 1,
                'message' => 'City created successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateCity(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|numeric',
                'name' => 'required|string',
                'district_id' => 'required|numeric|exists:districts,id',
            ]);

            $city = City::findOrFail($validated['id']);

            $city->update($validated);

            return response()->json([
                'success' => 1,
                'message' => 'City updated successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCities($district){
        try{
            if($district == -1){
                $cities = City::select('id', 'name')->where('is_active', 1)->get();

                return response()->json([
                    'success' => 1,
                    'message' => 'All Cities retrived successfully',
                    'data' => $cities,
                ], 200);

            }
            $cities = City::where('district_id', $district)
            ->with('district:id,name')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'name' => $entry->name,
                    'district_id' => $entry->district_id,
                    'district_name' => $entry->district->name,
                ];
            });

            return response()->json([
                'success' => 1,
                'message' => 'Cities retrived successfully',
                'data' => $cities,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }



    }
}
