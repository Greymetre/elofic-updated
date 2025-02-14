<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketIntelligencesField;
use Illuminate\Http\Request;

class MarketIntelligenceController extends Controller
{
    public function getFields(Request $request)
    {
        $field = MarketIntelligencesField::with('fieldsData:id,value,field_id')
            ->select('id', 'field_name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->field_name => $item->fieldsData];
            });
        return response()->json(['status' => 'success', 'data' => $field], 200);
    }
}
