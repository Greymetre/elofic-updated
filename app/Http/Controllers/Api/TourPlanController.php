<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourProgramme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TourPlanController extends Controller
{
    public function show(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]); 
        if ($validator->fails()) {
            return response()->json(['status' => 'error','message' =>  $validator->errors()], 400); 
        }

        $user_id = $request->input('user_id');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $tour_plan = TourProgramme::where('userid', $user_id)->orderBy('date', 'desc');
        
        if($start_date && $start_date != '' && $start_date != null){
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date));
            $tour_plan = $tour_plan->whereBetween('date', [$start_date, $end_date]);
        }
        
        
        $tour_plan = $tour_plan->get();

        if(count($tour_plan) > 0){
            return response()->json(['status' => 'success','message' => 'Data retrieved successfully.', 'data' => $tour_plan ], 200);
        }else{
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $tour_plan ],400);
        }
    }

    public function user_list(Request $request)
    {
        $user = $request->user();
        $user_id = $user->id;
        $pageSize = $request->input('pageSize');
        $search_name = $request->input('search_name');
        $search_branches = $request->input('search_branches');

        
        $all_reporting_user_ids = getUsersReportingToAuth($user_id);
        

        $all_user_branches = User::with('getbranch')->whereIn('id', getUsersReportingToAuth($user_id))->orderBy('branch_id')->get();
        $branches= array();
        $all_branch= array();
        $bkey = 0;
        foreach ($all_user_branches as $k => $val) {
            if($val->getbranch){
                if(!in_array($val->getbranch->id, $all_branch)){
                    array_push($all_branch, $val->getbranch->id);
                    $branches[$bkey]['id'] = $val->getbranch->id;
                    $branches[$bkey]['name'] = $val->getbranch->branch_name;
                    $bkey++;
                }
            }
        }

        if($search_branches && count($search_branches) > 0 && $search_branches[0] != null){
            $all_reporting_user_ids = User::whereIn('id', $all_reporting_user_ids)->whereIn('branch_id', $search_branches)->pluck('id')->toArray();
        }

        $all_user_details = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $all_users= array();
        foreach ($all_user_details as $k => $val) {
            $all_users[$k]['id'] = $val->id;
            $all_users[$k]['name'] = $val->name;
        }
        if($search_name && $search_name != ''){
            $all_reporting_user_ids = array();
            $all_reporting_user_ids[] = $search_name;
        }
        $date_checkIn = User::select('name', 'id')
        ->whereIn('id', $all_reporting_user_ids);
        $date_checkIn->orderBy('id', 'desc');
    
        $date_checkIn = (!empty($pageSize)) ? $date_checkIn->paginate($pageSize) : $date_checkIn->paginate(100);
        
        $data = array();
        if(count($date_checkIn) > 0){
            foreach($date_checkIn as $key=>$checkIn){
                $data[$key]['user_id'] = $checkIn->id;
                $data[$key]['name'] = $checkIn->name;
            }
            return response()->json(['status' => 'success','message' => 'Data retrieved successfully.', 'users'=>$all_users, 'branches'=>$branches, 'page_count'=>$date_checkIn->lastPage(), 'data' => $data ], 200);
        }else{
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);
        }
    }

    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required|array',
            'town' => 'required|array',
            'objectives' => 'required|array',
        ]); 
        if ($validator->fails()) {
            return response()->json(['status' => 'error','message' =>  $validator->errors()], 400); 
        }
        $created_by = auth()->user()->id;
        $user_id = $request->input('user_id');
        $all_date = $request->input('date');
        $all_town = $request->input('town');
        $all_objectives = $request->input('objectives');

        if($created_by){
            foreach($all_date as $k=>$date){
                TourProgramme::create([
                    'date' => $date,
                    'userid' => $user_id,
                    'town' => $all_town[$k],
                    'objectives' => $all_objectives[$k],
                    'created_by' => $created_by,
                ]);
            }
            return response()->json(['status' => 'success','message' => 'Data added successfully.'], 200);
        }else{
            return response(['status' => 'error', 'message' => 'Something went wrong.'],400);
        }

    }

    public function edit(Request $request){
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required',
            'user_id' => 'required',
            'date' => 'required',
            'town' => 'required',
            'objectives' => 'required',
        ]); 
        if ($validator->fails()) {
            return response()->json(['status' => 'error','message' =>  $validator->errors()], 400); 
        }
        $tour_id = $request->input('tour_id');
        $user_id = $request->input('user_id');
        $date = $request->input('date');
        $town = $request->input('town');
        $objectives = $request->input('objectives');

        $tour_plan = TourProgramme::find($tour_id);

        if($tour_plan){
            $tour_plan->date = $date;
            $tour_plan->userid = $user_id;
            $tour_plan->town = $town;
            $tour_plan->objectives = $objectives;
            return response()->json(['status' => 'success','message' => 'Data updated successfully.'], 200);
        }else{
            return response(['status' => 'error', 'message' => 'Something went wrong.'],400);
        }

    }
}
