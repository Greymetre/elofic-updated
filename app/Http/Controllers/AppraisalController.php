<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\salesWeightage;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use Validator;
use DB;

class AppraisalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->appraisal = new Appraisal();
    }
    public function index(Request $request)
    {
        $search_branches = $request->input('search_branches');
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_branches = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $branches = array();
        $all_branch = array();
        $bkey = 0;
        foreach ($all_user_branches as $k => $val) {
            if ($val->getbranch) {
                if (!in_array($val->getbranch->id, $all_branch)) {
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
            if ($val->getdesignation) {
                if (!in_array($val->getdesignation->id, $all_designation_id)) {
                    array_push($all_designation_id, $val->getdesignation->id);
                    $all_designation[$k]['id'] = $val->getdesignation->id;
                    $all_designation[$k]['designation_name'] = $val->getdesignation->designation_name;
                }
            }
        }
        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            if ($request->ajax()) {
                $response = ["users" => $users, "status" => true];
                return response()->json($response);
            }
        }
        if ($request->user_id && $request->user_id != null && $request->user_id != '') {
            $all_reporting_user_ids = array();
            $all_reporting_user_ids[] = $request->user_id;
        }
        if ($request->ajax()) {
            $data = Appraisal::select('year', 'user_id', DB::raw('GROUP_CONCAT(created_at) as dates'))->whereIn('user_id', $all_reporting_user_ids)->groupBy('year', 'user_id');
            
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function ($query) {
                    return $query->users ? $query->users->name : '-';
                })
                ->addColumn('financial_year', function ($query) {
                    return str_replace('_', '-', $query->year);
                })
                ->addColumn('date', function ($query) {
                    $all_dates = explode(',', $query->dates);
                    return date('d-M-y', strtotime($all_dates[0]));
                })
                ->rawColumns(['user_name', 'financial_year', 'date'])
                ->make(true);
        }
        return view('appraisal.index', compact('branches', 'users', 'all_designation'));
    }

    public function create(Request $request)
    {
        $search_branches = $request->input('search_branches');
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_branches = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $branches = array();
        $all_branch = array();
        $bkey = 0;
        foreach ($all_user_branches as $k => $val) {
            if ($val->getbranch) {
                if (!in_array($val->getbranch->id, $all_branch)) {
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
                $response = ["users" => $users, "status" => true];
                return response()->json($response);
            }
        }
        $sale_weightage = salesWeightage::get();
        return view('appraisal.create', compact('users', 'branches', 'sale_weightage'))->with('appraisal', $this->appraisal);
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
            Appraisal::updateOrCreate(
                [
                'weightage_id' => $value,
                'user_id' => $executive_id,
                'year' => $f_year,
                ],
                [
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
                ]
            );
        }

        return redirect(url('appraisal/index'));
    }

    public function download(Request $request)
    {
        dd($request->all());
    }

    public function getappraisal(Request $request){
        if($request->appraisal_type == 'quarterly' || $request->appraisal_type == 'half_yearly'){
            $all_reporting_user_ids = getUsersReportingToAuth();
            $appraisal = Appraisal::with('sales_weightage')->where('user_id', $request->executive_id)->where('year', $request->f_year)->where('appraisal_type', $request->appraisal_type)->where('appraisal_session', $request->appraisal_session)->whereIn('rating_by', $all_reporting_user_ids)->get();
            foreach($appraisal as $k=>$val){
                $appraisal[$k]->rating_by_user->getdesignation = $val->rating_by_user->getdesignation;
            }
        }else{
            $all_reporting_user_ids = getUsersReportingToAuth();
            $appraisal = Appraisal::with('sales_weightage')->where('user_id', $request->executive_id)->where('year', $request->f_year)->where('appraisal_type', $request->appraisal_type)->whereIn('rating_by', $all_reporting_user_ids)->get();
            foreach($appraisal as $k=>$val){
                $appraisal[$k]->rating_by_user->getdesignation = $val->rating_by_user->getdesignation;
            }
        }

        return response()->json($appraisal);
    }
}
