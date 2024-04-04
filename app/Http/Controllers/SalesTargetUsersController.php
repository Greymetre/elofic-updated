<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\SalesTargetUsersRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SalesTargetUsers;
use App\Exports\SalesTargetUsersTemplate;
use App\Exports\SalesTargetUsersExport;
use App\Imports\SalesTargetUsersImport;
use App\Models\Branch;
use App\Models\Division;
use App\Models\User;
use Carbon\Carbon;
use DataTables;
use Validator;
use Gate;
use Excel;

class SalesTargetUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->subcategories = new SalesTargetUsers();
        $this->path = 'salestargetusers';
    }

    public function sales_target_users_list(Request $request)
    {
        $query = SalesTargetUsers::with(['user','user.getbranch','user.getdesignation'])->where(function ($query) use ($request) {
                       
                        if($request->month && $request->month != '' && $request->month != null){
                            $query->where('month',$request->month) ;
                        }

                        if($request->branch_id && $request->branch_id != '' && $request->branch_id != null){
                            $userIds = User::where('branch_id', $request->branch_id)->pluck('id');
                            $query->whereIn('user_id',$userIds) ;
                        }

                        if($request->user_id && $request->user_id != '' && $request->user_id != null){
                            $query->where('user.id',$request->user) ;
                        }

                        if($request->division && $request->division != '' && $request->division != null){
                            $divisionIds = User::where('division_id', $request->division)->pluck('id');
                            $query->whereIn('user_id',$divisionIds) ;
                        }

                        if($request->year && $request->year != '' && $request->year != null){
                            $query->where('year',$request->year) ;
                        }
                        if($request->min_range && $request->min_range != '' && $request->min_range != null && $request->max_range && $request->max_range != '' && $request->max_range != null){
                            $query->whereBetween('points',[$request->min_range,$request->max_range]) ;
                        }
                    })->orderBy('id', 'asc');
      
        // $data = SalesTargetUsers::with(['user','user.getbranch'])->get();

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn = '';
                $activebtn = '';

                if(auth()->user()->can(['target_users_access_edit']))
                {
                  $btn = $btn.'<a href"javascript:void(0)" class="btn btn-info btn-just-icon btn-sm edit" id="'.encrypt($data->id).'" title="'.trans('panel.global.edit').' '.trans('panel.sales_target_user.title_singular').'">
                        <i class="material-icons">edit</i>
                      </a>';
                }

                if (auth()->user()->can(['target_users_access_delete'])) {
                    $btn = $btn . ' <a href="#" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $data->id . '" title="'.trans('panel.global.delete').' '.trans('panel.sales_target_user.title_singular').'">
                              <i class="material-icons">clear</i>
                            </a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                              ' . $btn . '
                          </div>';
         
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function sales_target_users(Request $request)
    {
        $sales_target_users = SalesTargetUsers::latest()->get();
        $users = User::latest()->get();
        $branches = Branch::latest()->get(); 
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);
        abort_if(Gate::denies('target_users_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('sales_target_users.index', compact('sales_target_users','branches','years','users','divisions'));
    }


    public function target_users_upload(Request $request) {

        abort_if(Gate::denies('sales_target_users_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');                
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        // Excel::import(new SalesTargetUsersImport, request()->file('import_file'));
        Excel::import(new SalesTargetUsersImport, $request->file('import_file')->store('temp'));

        return back()->with('success', 'Sales Target User Import successfully !!'); 
    }

    public function target_users_download(Request $request) {
        abort_if(Gate::denies('sales_target_users_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SalesTargetUsersExport($request), 'SalesTargetUsers.xlsx');
    }

    public function sales_target_users_delete(Request $request)
    {
        SalesTargetUsers::where('id', $request->id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Sales Target User Deleted successfully']);
    }

    public function template(Request $request) {
        abort_if(Gate::denies('sales_target_users_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SalesTargetUsersTemplate, 'sales_target_users.xlsx');
    }

    public function update_target_user_modal(Request $request, $id) {
        $id = decrypt($id);
        $sales_target_user = SalesTargetUsers::find($id);
        return response()->json($sales_target_user);
    }

    public function update_target_user_updte(Request $request) {

        $data = $request->all();
        SalesTargetUsers::where('id', $data['id'])->update([
            'user_id' => $data['user_id'],
            'month' => $data['month'],
            'year' => $data['year'],
            'target' => $data['target'],
        ]);

        return back()->with('success', 'Sales Target User Updated successfully !!'); 
    }
}
