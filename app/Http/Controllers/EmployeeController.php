<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    //
    public function saveEmployee(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string',
                'cnic' => 'required|string',
                'email' => 'nullable|email',
                'contact_no' => 'required|string',
                'emergency_contact' => 'nullable|string',
                'address' => 'required|string',
                'city_id' => 'required|exists:cities,id',
                'designation_id' => 'required|exists:employee_designations,id',
                'branch_id' => 'required|exists:branches,id',
                'joining_date' => 'required|date',
                'dob' => 'nullable|date',
                'gender' => 'required|in:male,female,other',
                'salary' => 'required|numeric',
                'house_rent' => 'nullable|numeric',
                'travel_allowance' => 'nullable|numeric',
                'medical_allowance' => 'nullable|numeric',
                'has_overtime'=> 'required|boolean',

            ]);

            Employee::create($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Employee created successfully',
                
            ], 200);

        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateEmployee(Request $request){
        try{
            $validated = $request->validate([
                'id' => 'required|exists:employees,id',
                'name' => 'required|string',
                'cnic' => 'required|string',
                'email' => 'nullable|email',
                'contact_no' => 'required|string',
                'emergency_contact' => 'nullable|string',
                'address' => 'required|string',
                'city_id' => 'required|exists:cities,id',
                'designation_id' => 'required|exists:employee_designations,id',
                'branch_id' => 'required|exists:branches,id',
                'joining_date' => 'required|date',
                'dob' => 'nullable|date',
                'gender' => 'required|in:male,female,other',
                'salary' => 'required|numeric',
                'house_rent' => 'nullable|numeric',
                'travel_allowance' => 'nullable|numeric',
                'medical_allowance' => 'nullable|numeric',
                'has_overtime'=> 'required|boolean',

            ]);

            $employee = Employee::findOrFail($validated['id']);

            $employee->update($validated);

            return response()->json([
                'success' => 1,
                'message' => 'Employee updated successfully',
               
            ], 200);

        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getEmployees(){
        try{
            $employees = Employee::get();

            return response()->json([
                'success' => 1,
                'message' => 'Employees retreived successfully',
                'data' => $employees,
                
            ], 200);



        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getEmployeesWithSalaryAccounts(){
        try{
            $employees = Employee::
            with('branch:id,salary_account,salary_advance_account,employee_loan_account,overtime_account,other_allowance_account,bonus_account')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'name' => $entry->name,
                    'salary_account' => $entry->branch->salary_account,
                    'salary_advance_account' => $entry->branch->salary_advance_account,
                    'employee_loan_account' => $entry->branch->employee_loan_account,
                    'overtime_account' => $entry->branch->overtime_account,
                    'other_allowance_account' => $entry->branch->other_allowance_account,
                    'bonus_account' => $entry->branch->bonus_account,
                ];
            });

            return response()->json([
                'success' => 1,
                'message' => 'Employees retreived successfully',
                'data' => $employees,
                
            ], 200);



        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getEmployeeSalaryInfo($employee_id){
        try{
            $employees = Employee::where('id', $employee_id)
            ->select('salary', 'house_rent', 'medical_allowance', 'travel_allowance', 'has_overtime')
            ->get()->first();

            return response()->json([
                'success' => 1,
                'message' => 'Employees retreived successfully',
                'data' => $employees,
                
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
