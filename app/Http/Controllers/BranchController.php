<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    //
    public function saveBranch(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'contact_no' => 'nullable|string',
                'city_id' => 'required|exists:cities,id',
            ]);

            $coaController = new COAController();
    
            $salaryParent = ChartOfAccount::where('system_account_name', 'salary')->firstOrFail();
            $advanceParent = ChartOfAccount::where('system_account_name', 'employee advances')->firstOrFail();
            $loanParent = ChartOfAccount::where('system_account_name', 'employee loans')->firstOrFail();
           
            
    
            // Salary Account
            $salaryAccount = ChartOfAccount::create([
                'code' => $coaController->generateCOA_Code($salaryParent->id, 4),
                'account_name' => $validated['name'] . ' Salary',
                'parent_id' => $salaryParent->id,
                'type' => 'expense',
                'report_type' => 'income_statement',
                'normal_balance' => 'debit',
                'level' => 4,
                'has_child' => false,
            ]);
            $validated['salary_account'] = $salaryAccount->id;
    
            // Advance Account
            $advanceAccount = ChartOfAccount::create([
                'code' => $coaController->generateCOA_Code($advanceParent->id, 4),
                'account_name' => $validated['name'] . ' Advances',
                'parent_id' => $advanceParent->id,
                'type' => 'asset',
                'report_type' => 'balance_sheet',
                'normal_balance' => 'debit',
                'level' => 4,
                'has_child' => false,
            ]);
            $validated['salary_advance_account'] = $advanceAccount->id;
    
            // Loan Account
            $loanAccount = ChartOfAccount::create([
                'code' => $coaController->generateCOA_Code($loanParent->id, 4),
                'account_name' => $validated['name'] . ' Loans',
                'parent_id' => $loanParent->id,
                'type' => 'asset',
                'report_type' => 'balance_sheet',
                'normal_balance' => 'debit',
                'level' => 4,
                'has_child' => false,
            ]);
            $validated['employee_loan_account'] = $loanAccount->id;
    
            // Overtime Account
            $overtimeAccount = ChartOfAccount::create([
                'code' => $coaController->generateCOA_Code($salaryParent->id, 4),
                'account_name' => $validated['name'] . ' Overtime',
                'parent_id' => $salaryParent->id,
                'type' => 'expense',
                'report_type' => 'income_statement',
                'normal_balance' => 'debit',
                'level' => 4,
                'has_child' => false,
            ]);
            $validated['overtime_account'] = $overtimeAccount->id;

             // Other Allowances Account
             $otherAccount = ChartOfAccount::create([
                'code' => $coaController->generateCOA_Code($salaryParent->id, 4),
                'account_name' => $validated['name'] . ' Other Allowances',
                'parent_id' => $salaryParent->id,
                'type' => 'expense',
                'report_type' => 'income_statement',
                'normal_balance' => 'debit',
                'level' => 4,
                'has_child' => false,
            ]);
            $validated['other_allowance_account'] = $otherAccount->id;

             // Bonus Account
             $bonusAccount = ChartOfAccount::create([
                'code' => $coaController->generateCOA_Code($salaryParent->id, 4),
                'account_name' => $validated['name'] . ' Bonus',
                'parent_id' => $salaryParent->id,
                'type' => 'expense',
                'report_type' => 'income_statement',
                'normal_balance' => 'debit',
                'level' => 4,
                'has_child' => false,
            ]);
            $validated['bonus_account'] = $bonusAccount->id;
    
            Branch::create($validated);
    
            DB::commit();
    
            return response()->json([
                'success' => 1,
                'message' => 'Branch created successfully',
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
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
