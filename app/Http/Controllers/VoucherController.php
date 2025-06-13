<?php

namespace App\Http\Controllers;

use App\Models\AccountRegister;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class VoucherController extends Controller
{
    //

    public function getPostableVouchers($type){
        try{
            $vouchers = Voucher::where('is_posted', 0)
            ->where('type', $type)
            ->get();
           
            if ($vouchers->isEmpty()) {
                return response()->json([
                    'success' => 0,
                    'message' => "No vouchers found for posting.",
                    'data' => [],                   
                ], 404);
            }
            
            return response()->json([
                'success' => 1,
                'message' => 'All vouchers retrieved successfully',
                'data' => $vouchers,
            ]);

        }
        catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);

        }
    }

    public function getVoucherEntries($voucher_id){

        $entries = VoucherEntry::where('voucher_id', $voucher_id)
        ->with('account:id,account_name')
        ->get()
        ->map(function ($entry) {
            return [
                'id' => $entry->id,
                'voucher_id' => $entry->voucher_id,
                'account_id' => $entry->account_id,
                'account_name' => $entry->account->account_name ?? null,
                'amount' => $entry->amount,
                'type' => $entry->type,
                'description' => $entry->description,
            ];
        });

        return response()->json([
            'success' => 1,
            'message' => 'All vouchers retrieved successfully',
            'data' => $entries,
        ]);


    }

    public function saveDraftVoucher(Request $request)
{
    DB::beginTransaction();
    try {
        $validated = $request->validate([
            'type' => 'required|string',
            'payment_mode' => 'required|in:cash,bank,journal',
            'voucher_date' => 'nullable|date',
            'description' => 'nullable|string',
            'transaction_no' => 'nullable|string',
            'payment_account' => 'nullable|exists:chart_of_accounts,id',
            
        ]);
        $voucher_no = $this->generateVoucher($validated['type']);

        $validated['voucher_no'] = $voucher_no;
       

        $Voucher = Voucher::create($validated); 

       
            foreach($request->entries as $Entry){
                VoucherEntry::create([
                    'voucher_id' => $Voucher->id,
                    'account_id' => $Entry['account_id'],
                    'amount' => $Entry['amount'],
                    'type' => $Entry['type'],
                    'description' => $Entry['description'] ?? null,
                ]);

            }

        

        DB::commit();

        return response()->json([
            'success' => 1,
            'message' => 'Voucher created successfully!',
            'voucher' => $Voucher,
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => -1,
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function updateDraftVoucher(Request $request)
{
    DB::beginTransaction();

    try {
        $validated = $request->validate([
            'id' => 'required|exists:vouchers,id',
            'type' => 'required|string',
            'payment_mode' => 'required|in:cash,bank,journal',
            'payment_account' => 'nullable|exists:chart_of_accounts,id',
            'voucher_date' => 'nullable|date',
            'description' => 'nullable|string',
            'transaction_no' => 'nullable|string',
            'entries' => 'required|array',
            'entries.*.account_id' => 'required|integer|exists:chart_of_accounts,id',
            'entries.*.amount' => 'required|numeric|min:0',
            'entries.*.type' => 'required|in:debit,credit',
            'entries.*.description' => 'nullable|string',
            
        ]);
        $user = Auth::guard('api')->user();
        if($request -> is_posted){
            $validated['is_posted'] = 1;
            $validated['posted_by'] = $user->id;
            $validated['posted_at'] = Carbon::now();           

        }
        
        
        $voucher = Voucher::findOrFail($validated['id']);

        $voucher->update($validated);

        // Delete existing voucher entries
        VoucherEntry::where('voucher_id', $request->id)->delete();

        // Create new voucher entries
        foreach ($request->entries as $entry) {
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'account_id' => $entry['account_id'],
                'amount' => $entry['amount'],
                'type' => $entry['type'],
                'description' => $entry['description'] ?? null,
            ]);
        }

        if($request->is_posted){
            foreach ($request->entries as $entry) {
                AccountRegister::create([
                    'voucher_id' => $voucher->id,
                    'account_id' => $entry['account_id'],
                    'amount' => $entry['amount'],
                    'type' => $entry['type'],
                    'description' => $entry['description'] ?? null,
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'success' => 1,
            'message' => 'Voucher updated successfully!',
            'voucher' => $voucher,
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => -1,
            'message' => $e->getMessage(),
        ], 500);
    }
}


    public function generateVoucher($type)
{
    $prefix = '';

    switch (strtolower($type)) {
        case 'cash receipt':
            $prefix = 'CR-';
            break;
        case 'cash payment':
            $prefix = 'CP-';
            break;
        case 'bank receipt':
            $prefix = 'BR-';
            break;
        case 'bank payment':
            $prefix = 'BP-';
            break;
        case 'journal voucher':
            $prefix = 'JV-';
            break;
        case 'salary voucher':
            $prefix = 'SALV-';
            break;
        case 'advance salary':
            $prefix = 'ASV-';
            break;
        case 'employee loan':
            $prefix = 'ELV-';
            break;
        case 'employee bonus':
            $prefix = 'EBV-';
            break;
        default:
            $prefix = 'VC-'; // Generic Voucher
            break;
    }

    $currentDate = Carbon::now();
    $yearMonth = $currentDate->format('Ym'); // Example: 202504

    // Count how many vouchers already exist for this prefix and year+month
    $count = Voucher::where('voucher_no', 'like', "{$prefix}{$yearMonth}%")->count();

    $serial = str_pad($count + 1, 4, '0', STR_PAD_LEFT); // Pad to 4 digits, e.g., 0001

    return "{$prefix}{$yearMonth}{$serial}";
}
}
