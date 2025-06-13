<?php

namespace App\Http\Controllers;
use App\Models\ChartOfAccount;
use Dom\CharacterData;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class COAController extends Controller
{
    //
    public function getCOAByLevel($level){

        try {
              // Fetch accounts of specific level
        $accounts = ChartOfAccount::where('level', $level)
        ->where('is_active', true)
        ->select('id', 'account_name', 'opening_balance', 'normal_balance') // also select opening_balance and normal_balance
        ->get();

    if ($accounts->isEmpty()) {
        return response()->json([
            'success' => 0,
            'message' => "No accounts found for level $level.",
            'data' => []
        ], 404);
    }

            if($level == 4){              

                foreach ($accounts as $account) {
                    // Fetch total debit and credit based on 'type' column
                    $balanceData = DB::table('account_registers')
                        ->selectRaw("
                            SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as total_debit,
                            SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as total_credit
                        ")
                        ->where('account_id', $account->id)
                        ->first();
        
                    $totalDebit = $balanceData->total_debit ?? 0;
                    $totalCredit = $balanceData->total_credit ?? 0;
        
                    // Compute based on normal balance
                    if (strtolower($account->normal_balance) === 'debit') {
                        $balance = $account->opening_balance + ($totalDebit - $totalCredit);
                    } else {
                        $balance = $account->opening_balance + ($totalCredit - $totalDebit);
                    }
        
                    $account->balance = $balance;
                }

    // Return response
    return response()->json([
        'success' => 1,
        'message' => "Accounts retrieved successfully for level $level.",
        'data' => $accounts,
    ]);
            }
            
           
            // Return response
            return response()->json([
                'success' => 1,
                'message' => "Accounts retrieved successfully for level $level.",
                'data' => $accounts,
                
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => -1,
                'message' => 'Error retrieving chart of accounts.',
                'error' => $e->getMessage()
            ], 500);
        }

        
    }

    public function getCOAChildren($parent){
        try{
            $accounts = ChartOfAccount::where('parent_id', $parent)
            ->where('is_active', true)
            ->select('id', 'account_name')
            ->get();

            // Check if any records were found
            if ($accounts->isEmpty()) {
                return response()->json([
                    'success' => 0,
                    'message' => "No accounts found for level $parent.",
                    'data' => []
                ], 404);
            }

            // Return response
            return response()->json([
                'success' => 1,
                'message' => "Accounts retrieved successfully for level $parent.",
                'data' => $accounts,
                
            ]);

        }catch (\Exception $e) {
            return response()->json([
                'success' => -1,
                'message' => 'Error retrieving chart of accounts.',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function saveCOA(Request $request)
{
    try {
        $validated = $request->validate([               
            'account_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'opening_balance' => 'nullable|numeric', // changed from decimal to numeric for validation
            'type' => 'required|in:asset,liability,equity,expense,income',
            'report_type' => 'required|in:income_statement,balance_sheet,cash_flow,other',
            'normal_balance' => 'required|in:debit,credit',
            'level' => 'required|integer|min:2|max:4',
            'has_child' => 'nullable|boolean',
        ]);

        // Generate code only if parent_id and level exist
        $validated['code'] = $this->generateCOA_Code($request->parent_id, $request->level);
        $validated['level'] = $request->level;
        $validated['has_child'] = $request->has_child ?? false;
        $validated['is_active'] = $request->is_active ?? true;

        $account = ChartOfAccount::create($validated); // Fixed typo: 'created' ➜ 'create'

        return response()->json([
            'success' => 1,
            'message' => 'Chart of Account created successfully',
            
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => -1,
            'message' => $e->getMessage()
        ], 500);
    }
}
public function getAllCOAByParent($parent){
    try{
        $accounts = ChartOfAccount::where('parent_id', $parent)
        ->get();

        // Check if any records were found
        if ($accounts->isEmpty()) {
            return response()->json([
                'success' => 0,
                'message' => "No accounts found for parent $parent.",
                'data' => []
            ], 404);
        }

        // Return response
        return response()->json([
            'success' => 1,
            'message' => "Accounts retrieved successfully for parent $parent.",
            'data' => $accounts,
            
        ], 200);



    }catch(\Exception $e){
        return response()->json([
            'success' => -1,
            'message' => $e->getMessage()
        ], 500);
    }
}
public function getCashOrBankAccount($is_cash, $is_bank){
    try{
        $cashParent = ChartOfAccount::where('is_cash_account', $is_cash)
        ->where('is_bank_account', $is_bank)
        ->where('level', 3)
        ->first();
     
        $accounts = ChartOfAccount::where('parent_id', $cashParent->id)
        ->select('id', 'account_name', 'normal_balance', 'opening_balance')
        ->get();

        if($accounts->isEmpty()){
            return response()->json([
                'success' => 0,
                'message' => "No accounts found for Cash.",
                'data' => []
            ], 404);
        }
     
        foreach ($accounts as $account) {
            // Fetch total debit and credit based on 'type' column
            $balanceData = DB::table('account_registers')
                ->selectRaw("
                    SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as total_debit,
                    SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as total_credit
                ")
                ->where('account_id', $account->id)
                ->first();

            $totalDebit = $balanceData->total_debit ?? 0;
            $totalCredit = $balanceData->total_credit ?? 0;

            // Compute based on normal balance
            if (strtolower($account->normal_balance) === 'debit') {
                $balance = $account->opening_balance + ($totalDebit - $totalCredit);
            } else {
                $balance = $account->opening_balance + ($totalCredit - $totalDebit);
            }

            $account->balance = $balance;
        }

// Return response
return response()->json([
'success' => 1,
'message' => "Cash Accounts retrieved successfully.",
'data' => $accounts,
]);
    }catch(\Exception $e){
        return response()->json([
            'success' => -1,
            'message' => $e->getMessage()
        ], 500);
    }
  
}
public function getSalariesAccounts($is_salary_account, $is_advance_account, $is_loan_account){
try{
    $parent = ChartOfAccount::where('is_salary_account', $is_salary_account)
    ->where('is_salary_advance_account', $is_advance_account)
    ->where('is_employee_loan_account', $is_loan_account)
    ->select('id')
    ->first();

    $accounts = ChartOfAccount::where('parent_id', $parent->id)
    ->select('id', 'account_name')
    ->get();

    return response()->json([
        'success' => 1,
        'message' => 'Account retrieved successfully',
        'data' => $accounts,
    ], 200);

}catch(\Exception $e){
    return response()->json([
        'success' => -1,
        'message' => $e->getMessage()
    ], 500);
}
}

public function getChildOfSystemCOA($account_name){
    try{
        $id = ChartOfAccount::where('system_account_name', $account_name)
        ->value('id');
        

        $accounts = ChartOfAccount::where('parent_id', $id)
        ->select('id', 'account_name', 'normal_balance', 'opening_balance')
        ->get();

        if($accounts->isEmpty()){
            return response()->json([
                'success' => 0,
                'message' => "No accounts found for Cash.",
                'data' => []
            ], 404);
        }
     
        foreach ($accounts as $account) {
            // Fetch total debit and credit based on 'type' column
            $balanceData = DB::table('account_registers')
                ->selectRaw("
                    SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as total_debit,
                    SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as total_credit
                ")
                ->where('account_id', $account->id)
                ->first();

            $totalDebit = $balanceData->total_debit ?? 0;
            $totalCredit = $balanceData->total_credit ?? 0;

            // Compute based on normal balance
            if (strtolower($account->normal_balance) === 'debit') {
                $balance = $account->opening_balance + ($totalDebit - $totalCredit);
            } else {
                $balance = $account->opening_balance + ($totalCredit - $totalDebit);
            }

            $account->balance = $balance;
        }

// Return response
return response()->json([
'success' => 1,
'message' => "Cash Accounts retrieved successfully.",
'data' => $accounts,
]);

    }catch(\Exception $e){
    return response()->json([
        'success' => -1,
        'message' => $e->getMessage()
    ], 500);
}
}

public function updateCOA(Request $request)
{
    try {
        $validated = $request->validate([
            'id' => 'required|numeric',
            'account_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'type' => 'required|in:asset,liability,equity,expense,income',
            'report_type' => 'required|in:income_statement,balance_sheet,cash_flow,other',
            'normal_balance' => 'required|in:debit,credit',
            'level' => 'required|integer|min:2|max:4',
            'has_child' => 'nullable|boolean',
        ]);

        $coa = ChartOfAccount::findOrFail($validated['id']);

        if ($coa->is_default) {
            return response()->json([
                'success' => 0,
                'message' => 'This is a default account and cannot be updated.',
            ], 403); // 403 Forbidden
        }

        $coa->update($validated);

        return response()->json([
            'success' => 1,
            'message' => 'Chart of Account updated successfully.',
        ], 200); // 200 OK

    } catch (\Exception $e) {
        return response()->json([
            'success' => -1,
            'message' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}
public function activateAccount($id, $status){
    try{
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);

        $coa = ChartOfAccount::findOrFail($id);

        if ($coa->is_default) {
            return response()->json([
                'success' => 0,
                'message' => 'This is a default account and cannot be updated.',
            ], 403); // 403 Forbidden
        }

        $coa->update([
            'is_active' => $status ? 1 : 0, // ✅ fix here
        ]);

        return response()->json([
            'success' => 1,
            'message' => 'Chart of Account updated successfully.',
        ], 200); // 200 OK




    }catch(\Exception $e){
        return response()->json([
            'success' => -1,
            'message' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}

    public function generateCOA_Code($parent_id, $level)
{
    $parent = ChartOfAccount::where('id', $parent_id)->select('code')->first();

    if (!$parent) {
        throw new \Exception("Parent account not found");
    }

    $parentCode = $parent->code;

    // Count existing children under this parent to generate next segment
    $count = ChartOfAccount::where('parent_id', $parent_id)->count();
    $nextSegment = $count + 1;

    // Pad the segment with leading zeros
    if ($level == 2) {
        // Example: parent code = "1", nextSegment = 1 → "1-01"
        $newCode = $parentCode . '-' . str_pad($nextSegment, 2, '0', STR_PAD_LEFT);
    } elseif ($level == 3) {
        // Example: parent code = "1-01", nextSegment = 1 → "1-01-01"
        $newCode = $parentCode . '-' . str_pad($nextSegment, 2, '0', STR_PAD_LEFT);
    } elseif ($level == 4) {
        // Example: parent code = "1-01-01", nextSegment = 1 → "1-01-01-0001"
        $newCode = $parentCode . '-' . str_pad($nextSegment, 4, '0', STR_PAD_LEFT);
    } else {
        throw new \Exception("Unsupported level");
    }

    return $newCode;
}


}
