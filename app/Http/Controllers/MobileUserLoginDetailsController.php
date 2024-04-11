<?php

namespace App\Http\Controllers;

use Gate;
use Excel;
use Validator;
use App\Models\Branch;
use App\Models\Services;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Exports\MobileAppLoginUsersExport;
use App\Models\SchemeDetails;
use App\Models\SchemeHeader;
use Carbon\Carbon;
use App\Models\MobileUserLoginDetails;
use App\Models\SalesTargetUsers;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Division;

class MobileUserLoginDetailsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->mobile_user_login_details = new MobileUserLoginDetails();
        $this->path = 'mobile_user_login_details';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function mobile_user_login(Request $request) {

        $mobile_users = MobileUserLoginDetails::latest()->get();
        $users = User::latest()->get();
        $branches = Branch::latest()->get(); 
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);

        abort_if(Gate::denies('loyalty_mobile_app_users_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('loyalty_app_mobile_users.index', compact('mobile_users','branches','years','users','divisions'));
    }


    public function mobile_user_login_list(Request $request)
    {
         $query = MobileUserLoginDetails::with(['customer'])->where(function ($query) use ($request) {

             if($request->month && $request->month != '' && $request->month != null){
                 $query->where('month',$request->month) ;
             }
         })->orderBy('id', 'asc');

                   // $data = SalesTargetUsers::with(['user','user.getbranch'])->get();

         return Datatables::of($query)
         ->addIndexColumn()
         ->addColumn('action', function ($data) {
         })
        ->rawColumns(['action'])
        ->make(true);
    }


    public function mobile_user_login_download(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'financial_year' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        abort_if(Gate::denies('mobile_app_login_details_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();

        return Excel::download(new MobileAppLoginUsersExport($request), 'mobile_app_login_.xlsx');
    }
}
