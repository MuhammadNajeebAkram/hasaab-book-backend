<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    //
    public function saveSubject(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string',               
            ]);

            Subject::create($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Subject created successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateSubject(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|numeric',
                'name' => 'required|string',                
            ]);

            $subject = Subject::findOrFail($validated['id']);

            $subject->update($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Subject updated successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSubjects(){
        try{
            
            $subjects = Subject::get();
           

            return response()->json([
                'success' => 1,
                'message' => 'Subjects retrived successfully',
                'data' => $subjects,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }



    }

    public function getStatusSubjects($status){
        try{
            
            $subjects = Subject::where('activate', '=', $status)
            ->get();
           

            return response()->json([
                'success' => 1,
                'message' => 'Subjects retrived successfully',
                'data' => $subjects,
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }



    }
}
