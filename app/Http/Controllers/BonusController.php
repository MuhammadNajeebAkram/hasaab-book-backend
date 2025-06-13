<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    //

    public function getBonuses(){
        try{
            $bonuses = Bonus::where('activate', 1)
            ->get();

            return response()->json([
                'success' => 1,
                'data' => $bonuses,
            ], 500);

        }catch(\Exception $e){
            return response()->json([
                'success' => -1,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
