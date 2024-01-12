<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\salesWeightage;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use Validator;

class AppraisalController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->appraisal = new Appraisal();
        
    }
    public function index(Request $request){
        $search_branches = $request->input('search_branches');
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_branches = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $branches = array();
        $all_branch = array();
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
        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            $all_reporting_user_ids = User::whereIn('id', $all_reporting_user_ids)->whereIn('branch_id', $search_branches)->pluck('id')->toArray();
        }
        $all_user_details = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $all_users = array();
        $all_designation_id = array();
        $all_designation = array();
        foreach ($all_user_details as $k => $val) {
            $users[$k]['id'] = $val->id;
            $users[$k]['name'] = $val->name;
            if($val->getdesignation){
               if(!in_array($val->getdesignation->id, $all_designation_id)) {
                array_push($all_designation_id,$val->getdesignation->id);
                $all_designation[$k]['id'] = $val->getdesignation->id;
                $all_designation[$k]['designation_name'] = $val->getdesignation->designation_name;
               }
            }
        }
        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            if ($request->ajax()) {
                $response = ["users"=>$users, "status"=>true];
                return response()->json($response);
            }
        }
        if ($request->ajax()) {
            $data = Appraisal::whereIn('user_id', $all_reporting_user_ids)->latest();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('branch', function ($query) {
                    return $query->users->getbranch?$query->users->getbranch->branch_name:'-';
                })
                ->addColumn('department', function ($query) {
                    return $query->users->department?$query->users->department->division_name:'-';
                })
                ->addColumn('employee_code', function ($query) {
                    return $query->users?$query->users->employee_codes:'-';
                })
                ->addColumn('name', function ($query) {
                    return $query->users?$query->users->name:'-';
                })
                ->addColumn('designation', function ($query) {
                    return $query->users->getdesignation?$query->users->getdesignation->designation_name:'-';
                })
                ->addColumn('date_of_joining', function ($query) {
                    return $query->users->userinfo?date('d/M/Y', strtotime($query->users->userinfo->date_of_joining)):'-';
                })
                ->addColumn('ctc', function ($query) {
                    return $query->users->userinfo?$query->users->userinfo->salary:'-';
                })
                ->addColumn('last_increments', function ($query) {
                    return $query->users->userinfo?$query->users->userinfo->last_year_increments:'-';
                })
                ->addColumn('last_promotion', function ($query) {
                    return $query->users->userinfo?$query->users->userinfo->last_promotion:'-';
                })
                ->addColumn('target', function ($query) {
                    return $query->target?$query->target:'-';
                })
                ->addColumn('achievement', function ($query) {
                    return $query->achivment?$query->achivment:'-';
                })
                ->addColumn('sales_weightage', function ($query) {
                    return $query->sales_weightage?$query->sales_weightage->name:'-';
                })
                ->addColumn('rating', function ($query) use ($all_reporting_user_ids) {
                    if(in_array($query->rating_by, $all_reporting_user_ids)){
                        return $query->rating;
                    }else{
                        return '-';
                    }
                })
                ->addColumn('rating_by', function ($query) use ($all_reporting_user_ids) {
                    if(in_array($query->rating_by, $all_reporting_user_ids)){
                        return $query->rating_by_user?$query->rating_by_user->name.'('.$query->rating_by_user->getdesignation->designation_name.')':'-';
                    }else{
                        return '-';
                    }
                })
                ->rawColumns(['branch', 'department', 'employee_code', 'name', 'designation', 'date_of_joining', 'ctc', 'last_increments', 'last_promotion', 'target', 'achievement', 'sales_weightage'])
                ->make(true);
        }
        return view('appraisal.index', compact('branches', 'users', 'all_designation'));
    }

    public function create(Request $request){
        $search_branches = $request->input('search_branches');
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_branches = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $branches = array();
        $all_branch = array();
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
        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            $all_reporting_user_ids = User::whereIn('id', $all_reporting_user_ids)->whereIn('branch_id', $search_branches)->pluck('id')->toArray();
        }
        $all_user_details = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $all_users = array();
        foreach ($all_user_details as $k => $val) {
            $users[$k]['id'] = $val->id;
            $users[$k]['name'] = $val->name;
        
        }
        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            if ($request->ajax()) {
                $response = ["users"=>$users, "status"=>true];
                return response()->json($response);
            }
        }
        $sale_weightage = salesWeightage::get();
        return view('appraisal.create',compact('users', 'branches', 'sale_weightage'))->with('appraisal',$this->appraisal);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'executive_id' => 'required',
            'f_year' => 'required',
            'appraisal_type' => 'required',
        ]); 
        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        $user_id = auth()->user()->id;
        $sale_weightage_id = $request->sale_weightage_id;
        $executive_id = $request->executive_id;
        $f_year = $request->f_year;
        $appraisal_type = $request->appraisal_type;
        $appraisal_session = $request->appraisal_session;
        $target = $request->target;
        $achivment = $request->achivment;
        $acual = $request->acual;
        $rating = $request->rating;
        $remark = $request->remark;
        foreach ($sale_weightage_id as $key => $value) {
            Appraisal::create([
                'weightage_id' => $value,
                'user_id' => $executive_id,
                'year' => $f_year,
                'target' => $target[$key],
                'achivment' => $achivment[$key],
                'acual' => $acual[$key],
                'rating' => $rating[$key],
                'rating_by' => $user_id,
                'appraisal_type' => $appraisal_type,
                'appraisal_session' => $appraisal_session,
                'remark' => $remark,
            ]);
        }

        return redirect(url('appraisal/index'));
        
    }
}
