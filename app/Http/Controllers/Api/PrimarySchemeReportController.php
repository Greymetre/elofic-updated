<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Validator;
use Gate;

use App\Models\Division;
use App\Models\PrimarySales;
use App\Models\PrimaryScheme;
use Carbon\Carbon;

class PrimarySchemeReportController extends Controller
{
    public function getPrimarySchemeFilter(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        foreach ($years as $k => $year) {
            $startYear = $year - 1;
            $endYear = $year;
            $data['years'][$k]['key'] = $startYear . '-' . $endYear;
            $data['years'][$k]['value'] = $startYear . '-' . $endYear;
        }


        $data['divisions'] = PrimarySales::select('division')
            ->groupBy('division')
            ->pluck('division')
            ->map(function ($item) {
                return [
                    'key' => $item,
                    'value' => $item,
                ];
            })
            ->values();
        $data['quarters'] = [['key' => '1', 'value' => 'Q1(Apr,May,Jun)'], ['key' => '2', 'value' => 'Q2(Jul,Aug,Sep)'], ['key' => '3', 'value' => 'Q3(Oct,Nov,Dec)'], ['key' => '4', 'value' => 'Q4(Jan,Feb,Mar)']];
        $data['types'] = [['key' => 'qualified', 'value' => 'Qualified'], ['key' => 'unqualified', 'value' => 'Unqualified']];

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function getPrimarySchemes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'division' => 'required',
        ]); 
        if ($validator->fails()) {
            return response()->json(['status' => 'error','message' =>  $validator->errors()], 400); 
        }
        $pSchemes = PrimaryScheme::where('quarter', $request->quarter)->where('division', $request->division)->select('id', 'scheme_name')->get();
        return response()->json(['status' => 'success', 'data' => $pSchemes]);
    }
}
