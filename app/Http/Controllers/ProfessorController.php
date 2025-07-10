<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfessorController extends Controller
{
    //
    public function saveProfessor(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string',
                'address' => 'nullable|string',
                'contact_no' => 'nullable|string',
                'city_id' => 'required|exists:cities,id',
                'institute_id' => 'required|exists:institutes,id',               
                'subject_id' => 'required|exists:subjects,id',
                'is_author' => 'required',
            ]);

            Professor::create($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Professor created successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfessor(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|numeric',
                'name' => 'required|string',  
                'address' => 'nullable|string',
                'contact_no' => 'nullable|string',
                'city_id' => 'required|exists:cities,id',   
                'institute_id' => 'required|exists:institutes,id',               
                'subject_id' => 'required|exists:subjects,id',
                'is_author' => 'required',             
            ]);

            $professor = Professor::findOrFail($validated['id']);

            $professor->update($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Professor updated successfully',
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProfessors(string $columnName, string $searchKeyword)
    {
        try {
            // Define a comprehensive whitelist of allowed columns for searching.
            // This now explicitly includes the *aliases* for the joined table names.
            $allowedSearchColumns = [
                'id',
                'name',          // Professor's own name
                'address',
                'contact_no',
                'city_id',       // Can still search by ID if needed by client
                'institute_id',  // Can still search by ID if needed by client
                'subject_id',    // Can still search by ID if needed by client
                'city_name',     // Search by city name
                'institute_name',// Search by institute name
                'subject_name'   // Search by subject name
            ];

            // Validate the provided column name against the whitelist
            if (!in_array($columnName, $allowedSearchColumns)) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Invalid column name for search. Allowed columns are: ' . implode(', ', $allowedSearchColumns),
                    'data' => []
                ], 400); // Bad Request status
            }

            // Start building the query with necessary joins
            $query = Professor::query()
                ->join('cities', 'professors.city_id', '=', 'cities.id')
                ->join('institutes', 'professors.institute_id', '=', 'institutes.id')
                ->join('subjects', 'professors.subject_id', '=', 'subjects.id')
                // Select all columns from the professors table
                ->select('professors.*')
                // Add select clauses for the names from joined tables using aliases
                ->addSelect('cities.name as city_name')
                ->addSelect('institutes.name as institute_name')
                ->addSelect('subjects.name as subject_name');

            // Dynamically apply the WHERE clause based on the $columnName
            switch ($columnName) {
                case 'city_name':
                    $query->where('cities.name', 'like', '%' . $searchKeyword . '%');
                    break;
                case 'institute_name':
                    $query->where('institutes.name', 'like', '%' . $searchKeyword . '%');
                    break;
                case 'subject_name':
                    $query->where('subjects.name', 'like', '%' . $searchKeyword . '%');
                    break;
                default:
                    // For all other columns (id, name, address, contact_no, etc., that belong to professors table itself)
                    $query->where('professors.' . $columnName, 'like', '%' . $searchKeyword . '%');
                    break;
            }

            // Execute the query
            $professors = $query->get();

            // Check if any records were found
            if ($professors->isEmpty()) {
                return response()->json([
                    'success' => 0,
                    'message' => 'No professors found matching your criteria.',
                    'data' => []
                ], 200); // Return 200 OK for successful search, even if no results
            }

            return response()->json([
                'success' => 1,
                'message' => 'Professors retrieved successfully.',
                'data' => $professors
            ], 200);

        } catch (\Exception $e) {
            // Log the exception for internal debugging
            Log::error("Error retrieving professors: " . $e->getMessage(), [
                'columnName' => $columnName,
                'searchKeyword' => $searchKeyword,
                'trace' => $e->getTraceAsString()
            ]);

            // Return a more user-friendly error message in the API response
            return response()->json([
                'success' => -1,
                'message' => 'An internal server error occurred while retrieving professors.'
                // In a development environment, you might include: 'debug_message' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    public function getStatusAuthors($status){
        try{
            $professors = Professor::where('activate', '=', $status)
            ->where('is_author', '=', 1)
            ->select('id', 'name')
            ->get();

            return response()->json([
                'success' => 1,
                'message' => 'Professors retreived successfully',
                'data' => $professors,
                
            ], 200);
    
        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    
    }
}
