<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\User;

class ReportingActivityController extends Controller
{
    public function allReportingUsers(Request $request){
        $user = $request->user();
        $user_id = $user->id;
        $pageSize = $request->input('pageSize');
        $search_name = $request->input('search_name');
        if($user->roles[0]->name == 'superadmin'){
            $all_reporting_user_ids = User::query();
            if($search_name){
                $all_reporting_user_ids->where('name', 'LIKE', '%'.$search_name.'%');
            }
            $all_reporting_user_ids = $all_reporting_user_ids->pluck('id')->toArray();
        }else{
            $all_reporting_user_ids = User::where('reportingid', $user_id)->pluck('id')->toArray();
        }
        $date_checkIn = CheckIn::select('checkin_date', 'user_id')
        ->with('users')
        ->whereIn('user_id', $all_reporting_user_ids)
        ->groupBy('checkin_date', 'user_id')
        ->orderBy('checkin_date', 'desc');

        $date_checkIn = (!empty($pageSize)) ? $date_checkIn->paginate($pageSize) : $date_checkIn->get();

        $data = array();
        foreach($date_checkIn as $key=>$checkIn){
            $data[$key]['name'] = $checkIn->users->name;
            $data[$key]['date'] = date('d/M/Y', strtotime($checkIn->checkin_date));
        }
        if(count($data) > 0){
            return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data ], 200);
        }else{
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);
        }
    }
}
