<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketIntelligenceServey;
use App\Models\MarketIntelligencesField;
use App\Models\MarketIntelligencesFielddata;
use Illuminate\Http\Request;
use Auth;

class MarketIntelligenceController extends Controller
{
    public function getFields(Request $request)
    {
        $user = $request->user();

        $field = MarketIntelligencesField::with('fieldsData:id,value,field_id')
            ->where('division_id' , $user->division_id)
            ->select('id', 'field_name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->field_name => $item->fieldsData];
            });
        return response()->json(['status' => 'success', 'data' => $field], 200);
    }

    public function MarketIntelligenceStore(Request $request)
    {
        $data = $request->all();

        $data['created_by'] = Auth::user()->id;

        
        $servey = MarketIntelligenceServey::create($data);
        if ($request->hasFile('servey_image')) {
            $file = $request->file('servey_image');
            $customname = time() . '.' . $file->getClientOriginalExtension();
            $servey->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('servey_image');
        }

        return response()->json(['status' => 'success', 'message' => 'Market Intelligence created successfully', 'data' => $servey], 200);
    }
}
