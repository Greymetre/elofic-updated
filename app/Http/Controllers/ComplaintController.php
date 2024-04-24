<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use App\DataTables\ComplaintDataTable;
use App\Models\Branch;
use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\Customers;
use App\Models\Division;
use App\Models\EndUser;
use App\Models\Pincode;
use App\Models\User;
use App\Models\WarrantyActivation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use DB;

class ComplaintController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->complaint = new Complaint();
        $this->path = 'complaints';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ComplaintDataTable $dataTable)
    {
        abort_if(Gate::denies('complaint_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('complaint.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $newComplaintNumber = $this->getComplaintNumber();
        
        $roleName = "Service Eng";

        $assign_users = User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })
            ->with(['roles' => function ($query) {
                $query->with('permissions');
            }])->select('id', 'name')
            ->get();
        $service_centers = Customers::where('customertype', '4')->select('id', 'name')->get();
        $branchs = Branch::where('active', 'Y')->select('id', 'branch_name', 'branch_code')->get();
        $pincodes = Pincode::where('active', 'Y')->select('id', 'pincode')->get();
        $divisions = Division::where('active', 'Y')->select('id', 'division_name')->get();
        $complaint_types = ComplaintType::where('active', 'Y')->select('id', 'name')->get();
        return view('complaint.create', compact('assign_users', 'service_centers', 'branchs', 'pincodes', 'divisions', 'complaint_types', 'newComplaintNumber'))->with('complaints', $this->complaint);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->end_user_id || $request->end_user_id == NULL || $request->end_user_id == ''){
            $end_user = EndUser::updateOrCreate(['customer_number' => $request->customer_number ?? ''],[
                'customer_name' => $request->customer_name ?? '',
                'customer_number' => $request->customer_number ?? '',
                'customer_email' => $request->customer_email ?? '',
                'customer_address' => $request->customer_address ?? '',
                'customer_place' => $request->customer_place ?? '',
                'customer_pindcode' => $request->customer_pindcode ?? '',
                'customer_country' => $request->customer_country ?? '',
                'customer_state' => $request->customer_state ?? '',
                'customer_district' => $request->customer_district ?? '',
                'customer_city' => $request->customer_city ?? ''
            ]);
            $request->end_user_id = $end_user->id;
        }
        $check_warranty = WarrantyActivation::with('customer', 'media')->where('product_serail_number', $request->product_serail_number)->first();
        if(!$check_warranty)
        {
            WarrantyActivation::create([
                'product_serail_number' => $request->product_serail_number ?? NULL,
                'product_id' => $request->product_id ?? NULL,
                'end_user_id' => $request->end_user_id ?? NULL,
                'branch_id' => $request->branch_id ?? NULL,
                'customer_id' => $request->seller ?? NULL,
                'status' => 0,
                'sale_bill_no' => $request->sale_bill_no ?? NULL,
                'sale_bill_date' => $request->sale_bill_date ?? NULL,
                'warranty_date' => $request->customer_bill_date ?? NULL,
                'created_by' => auth()->user()->id
            ]);
        }
        $newComplaintNumber = $this->getComplaintNumber();
        Complaint::create([
            'complaint_number' => $newComplaintNumber,
            'complaint_date' => $request->complaint_date ?? NULL,
            'claim_amount' => $request->claim_amount ?? NULL,
            'seller' => $request->seller ?? NULL,
            'end_user_id' => $request->end_user_id ?? NULL,
            'party_name' => $request->party_name ?? NULL,
            'product_laying' => $request->product_laying ?? NULL,
            'service_center' => $request->service_center ?? NULL,
            'assign_user' => $request->assign_user ?? NULL,
            'product_id' => $request->product_id ?? NULL,
            'product_serail_number' => $request->product_serail_number ?? NULL,
            'product_code' => $request->product_code ?? NULL,
            'product_name' => $request->product_name ?? NULL,
            'category' => $request->category ?? NULL,
            'specification' => $request->specification ?? NULL,
            'product_no' => $request->product_no ?? NULL,
            'phase' => $request->phase ?? NULL,
            'seller_branch' => $request->seller_branch ?? NULL,
            'purchased_branch' => $request->purchased_branch ?? NULL,
            'product_group' => $request->product_group ?? NULL,
            'company_sale_bill_no' => $request->company_sale_bill_no ?? NULL,
            'company_sale_bill_date' => $request->company_sale_bill_date ?? NULL,
            'customer_bill_date' => $request->customer_bill_date ?? NULL,
            'customer_bill_no' => $request->customer_bill_no ?? NULL,
            'company_bill_date_month' => $request->company_bill_date_month ?? NULL,
            'under_warranty' => $request->under_warranty ?? NULL,
            'service_type' => $request->service_type ?? NULL,
            'customer_bill_date_month' => $request->customer_bill_date_month ?? NULL,
            'warranty_bill' => $request->warranty_bill ?? NULL,
            'fault_type' => $request->fault_type ?? NULL,
            'service_centre_remark' => $request->service_centre_remark ?? NULL,
            'complaint_status' => $request->complaint_status ?? 0,
            'remark' => $request->remark ?? NULL,
            'division' => $request->division ?? NULL,
            'register_by' => $request->register_by ?? NULL,
            'complaint_type' => $request->complaint_type ?? NULL,
            'description' => $request->description ?? NULL,
            'created_by_device' => 'user',
            'created_by' => auth()->user()->id
        ]);

        return Redirect::to('complaints')->with('message_success', 'Complaint Store Successfully and the complaint number is <span title="Copy" id="copyText">'. $newComplaintNumber .'</span>');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function show(Complaint $complaint)
    {
        return view('complaint.show', compact('complaint'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function edit(Complaint $complaint)
    {
        $this->complaint = $complaint;
        $roleName = "Service Eng";

        $assign_users = User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })
            ->with(['roles' => function ($query) {
                $query->with('permissions');
            }])->select('id', 'name')
            ->get();
        $service_centers = Customers::where('customertype', '4')->select('id', 'name')->get();
        $branchs = Branch::where('active', 'Y')->select('id', 'branch_name', 'branch_code')->get();
        $pincodes = Pincode::where('active', 'Y')->select('id', 'pincode')->get();
        $divisions = Division::where('active', 'Y')->select('id', 'division_name')->get();
        $complaint_types = ComplaintType::where('active', 'Y')->select('id', 'name')->get();
        return view('complaint.create', compact('assign_users', 'service_centers', 'branchs', 'pincodes', 'divisions', 'complaint_types'))->with('complaints', $this->complaint);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Complaint $complaint)
    {
        $complaint->update($request->all());
        $newComplaintNumber = $complaint->complaint_number;

        return Redirect::to('complaints')->with('message_success', 'Complaint Update Successfully and the complaint number is <span title="Copy" id="copyText">'. $newComplaintNumber .'</span>');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Complaint $complaint)
    {
        //
    }

    public function getComplaintNumber(){
        $currentYear = date('y');
        $nextYear = $currentYear + 1;
        $financialYear = "$currentYear-$nextYear";
        $latestComplaint = Complaint::where('complaint_number', 'like', "SEC/HO/$financialYear/%")
            ->orderBy('complaint_number', 'desc')
            ->first();

        if ($latestComplaint) {
            $lastComplaintNumber = explode('/', $latestComplaint->complaint_number);
            $nextComplaintNumber = intval(end($lastComplaintNumber)) + 1;
        } else {
            $nextComplaintNumber = 1;
        }

        $nextComplaintNumberPadded = str_pad($nextComplaintNumber, 3, '0', STR_PAD_LEFT);

        return "SEC/HO/$financialYear/$nextComplaintNumberPadded";
    }

    public function cancelComplaint(Request $request){
        dd($request->all());
    }

    public function pendingComplaint(Request $request){
        dd($request->all());
    }
}
