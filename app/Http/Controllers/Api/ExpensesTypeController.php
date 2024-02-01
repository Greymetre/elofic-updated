<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpensesType;
use Illuminate\Http\Request;

class ExpensesTypeController extends Controller
{
    public function getExpensesType(Request $request)
    {
        $expenses_type = ExpensesType::all();
        return response()->json(['status'=>'success', 'data'=>$expenses_type], 200); 
    }
}
