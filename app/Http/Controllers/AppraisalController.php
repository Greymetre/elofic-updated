<?php

namespace App\Http\Controllers;

use App\Exports\AppraisalExport;
use App\Models\Appraisal;
use App\Models\salesWeightage;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Excel;
use Auth;
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
                    'rating_by' => $user_id,
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
        $all_sales_weight = salesWeightage::get();
        $validator = Validator::make($request->all(), [
            'financial_year' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $executive_id = $request->input('executive_id');
        $f_year = $request->input('financial_year');
        if ($executive_id && $executive_id != '' && $executive_id != null) {
            $all_reporting_user_ids = array($executive_id);
        } else {
            $all_reporting_user_ids = getUsersReportingToAuth();
        }
        $data = [];
        $appraisal = Appraisal::select(
            DB::raw('GROUP_CONCAT(IFNULL(target, \'\')) as target'),
            DB::raw('GROUP_CONCAT(weightage_id) as weightage_id'),
            DB::raw('GROUP_CONCAT(year) as year'),
            DB::raw('GROUP_CONCAT(user_id) as user_id'),
            DB::raw('GROUP_CONCAT(achivment) as achivment'),
            DB::raw('GROUP_CONCAT(rating) as rating'),
            DB::raw('GROUP_CONCAT(rating_by) as rating_by'),
        )->whereIn('user_id', $all_reporting_user_ids)->where('year', $f_year)->groupBy('year', 'user_id')->orderBy('year')->get();

        $rportingByArray = Appraisal::select('year', 'rating_by')->whereIn('user_id', $all_reporting_user_ids)->groupBy('year', 'rating_by')->where('year', $f_year)->orderBy('year')->get();


        $all_grades =  array('Grtade By Self');


        $first_head = [
            'S.No',
            'Branch',
            'Department',
            'Emp Code',
            'Name',
            'Designation',
            'Date Of Joining',
            'CTC',
            'Last Yr Increments',
            'Last Promotion',
            // 'Sale Target',
            // 'Sale Achivment',
            // 'New Dealer Target',
            // 'New Dealer Achivment',
            // 'Saarthi Accu target',
            // 'Saarthi Accu Point',
            // 'target',
            // 'Plumbers Meet Nos of Meets',
            // 'target',
            // 'Discipline',
            // 'target',
            // '>60 Days OS',
        ];
        $second_head = array();
        foreach ($all_sales_weight as $wtg) {
            array_push($second_head, $wtg->name." target");
            array_push($second_head, $wtg->name." Achivment");
        }
        if (count($appraisal) > 0) {
            $rids = array();
            foreach ($appraisal as $k => $val) {
                $allWId = explode(',', $val->weightage_id);
                $final_arr = array();
                foreach($allWId as $wid){
                    if(!in_array($wid, $final_arr)){
                        array_push($final_arr, $wid);
                    }
                }
                $data[$k][0] = ++$k;
                $data[$k][1] = $val->users->getbranch->branch_name;
                $data[$k][2] = $val->users->getdepartment->division_name;
                $data[$k][3] = $val->users->employee_codes;
                $data[$k][4] = $val->users->name;
                $data[$k][5] = $val->users->getdesignation->designation_name;
                $data[$k][6] = $val->users->userinfo->date_of_joining ? date('d-M-y', strtotime($val->users->userinfo->date_of_joining)) : "";
                $data[$k][7] = $val->users->userinfo->salary ?? "-";
                $data[$k][8] = $val->users->userinfo->last_year_increments ?? "-";
                $data[$k][9] = $val->users->userinfo->last_promotion ?? "-";
                $all_tr = explode(',', $val->target);
                $all_ach = explode(',', $val->achivment);
                
                $tr = 0;
                for($i = count($final_arr); $i > 0; $i--){
                    $data[$k][10+$tr] = $all_tr[count($all_tr) - $i] ?? "0";
                    $data[$k][11+$tr] = $all_ach[count($all_ach) - $i] ?? "0";
                    $tr++;
                    $tr++;
                }

                // $data[$k][12] = $all_tr[count($all_tr) - 5] ?? "0";
                // $data[$k][13] = $all_ach[count($all_ach) - 5] ?? "0";
                // $data[$k][14] = $all_ach[count($all_ach) - 4] ?? "0";
                // $data[$k][15] = $all_ach[count($all_ach) - 3] ?? "0";
                // $data[$k][16] = $all_ach[count($all_ach) - 2] ?? "0";
                // $data[$k][17] = $all_ach[count($all_ach) - 1] ?? "0";

                $all_reporting_by = explode(',', $val->rating_by);
                $all_reporting_by = array_unique($all_reporting_by);
                if (in_array($val->users->id, $all_reporting_by)) {
                    $getRating = Appraisal::where('user_id', $val->users->id)->where('rating_by', $val->users->id)->get();
                    $totalper = 0;
                    if (count($getRating) > 0) {
                        foreach ($getRating as $calcu) {
                            $totalper += ($calcu->sales_weightage->weightage * $calcu->rating) / 10;
                        }
                        if ($totalper < 51) {
                            $data[$k][12+$tr] = 'C';
                        } else if ($totalper > 50 && $totalper < 61) {
                            $data[$k][12+$tr] = 'B';
                        } else if ($totalper > 60 && $totalper < 71) {
                            $data[$k][12+$tr] = 'B+';
                        } else if ($totalper > 70 && $totalper < 81) {
                            $data[$k][12+$tr] = 'A';
                        } else if ($totalper > 80) {
                            $data[$k][12+$tr] = 'A+';
                        }
                    }
                } else {
                    $data[$k][12+$tr] = '-';
                }
                $i = 0;
                $remark = "-";
                $Increment = "-";
                foreach($rportingByArray as $k2=>$val2){
                    $main_user = User::find(explode(',', $val->user_id)[0]);
                    $rp_user = User::find($val2->rating_by);
                    if($val2->rating_by != $val->user_id && $rp_user->roles[0]->id != $main_user->roles[0]->id){
                        if(!in_array($rp_user->id, $rids)){
                            array_push($all_grades, $rp_user->name.'('.$rp_user->roles[0]->name.')');
                            array_push($rids, $rp_user->id);
                        }
                        $rats = Appraisal::where('year', $val2->year)->where('user_id', $main_user->id)->where('rating_by', $val2->rating_by)->get();
                        if(count($rats) > 0){
                            $totalper = 0;
                            foreach($rats as $fn){
                                $totalper += ($fn->sales_weightage->weightage * $fn->rating) / 10;
                                if ($totalper < 51) {
                                    $data[$k][13+$tr+$i] = 'C';
                                } else if ($totalper > 50 && $totalper < 61) {
                                    $data[$k][13+$tr+$i] = 'B';
                                    if($rp_user->hasRole('Head office')){
                                        $remark = $rats[0]->remark;
                                        $Increment = '8%';
                                    }
                                } else if ($totalper > 60 && $totalper < 71) {
                                    $data[$k][13+$tr+$i] = 'B+';
                                    if($rp_user->hasRole('Head office')){
                                        $remark = $rats[0]->remark;
                                        $Increment = '10%';
                                    }
                                } else if ($totalper > 70 && $totalper < 81) {
                                    $data[$k][13+$tr+$i] = 'A';
                                    if($rp_user->hasRole('Head office')){
                                            $remark = $rats[0]->remark;
                                        $Increment = '12%';
                                    }
                                } else if ($totalper > 80) {
                                    $data[$k][13+$tr+$i] = 'A+';
                                    if($rp_user->hasRole('Head office')){
                                        $remark = $rats[0]->remark;
                                        $Increment = '14%';
                                    }
                                }   
                            }
                        }else{
                            $data[$k][13+$tr+$i] = "-";
                        }
                        $i++;
                    }
                }
                $data[$k][14+$tr+$i] = $Increment;
                $data[$k][15+$tr+$i] = " ";
                $data[$k][16+$tr+$i] = " ";
                $data[$k][17+$tr+$i] = $remark;
            }
        }
        $last_head = [
            'Increment %',
            'Final Amount',
            'Promotion',
            'Remark'
        ];
        $headings = array_merge(
            $first_head,
            $second_head,
            $all_grades,
            $last_head,
        );
        $export = new AppraisalExport($data, $headings, count($all_grades));

        return Excel::download($export, 'AppraisalData.xlsx');
    }

    public function getappraisal(Request $request)
    {
        if ($request->appraisal_type == 'quarterly' || $request->appraisal_type == 'half_yearly') {
            $all_reporting_user_ids = getUsersReportingToAuth();
            $appraisal = Appraisal::with('sales_weightage')->where('user_id', $request->executive_id)->where('year', $request->f_year)->where('appraisal_type', $request->appraisal_type)->where('appraisal_session', $request->appraisal_session)->whereIn('rating_by', $all_reporting_user_ids)->get();
        } else {
            $all_reporting_user_ids = getUsersReportingToAuth();
            $appraisal = Appraisal::with('sales_weightage')->where('user_id', $request->executive_id)->where('year', $request->f_year)->where('appraisal_type', $request->appraisal_type)->whereIn('rating_by', $all_reporting_user_ids)->get();
        }
        if (count($appraisal) > 0) {
            foreach ($appraisal as $k => $val) {
                $appraisal[$k]->rating_by_user->getdesignation = $val->rating_by_user->getdesignation;
                $appraisal[$k]->rating_by_user->roles = $val->rating_by_user->roles;
            }

        }

        return response()->json($appraisal);
    }
}
