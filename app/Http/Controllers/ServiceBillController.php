<?php

namespace App\Http\Controllers;

use App\DataTables\ServiceBillDataTable;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Division;
use App\Models\ServiceBill;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;


class ServiceBillController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->service_bill = new ServiceBill();
        $this->path = 'service_bill';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ServiceBillDataTable $dataTable)
    {
        abort_if(Gate::denies('service_bill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('service_bill.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->complaint_id){
            $complaint = Complaint::find($request->complaint_id);
        }else{
            $complaint = Complaint::find(0);
        }
        $all_complaint_number = Complaint::with('product_details')->get();
        $lastServiceBillId = ServiceBill::max('id');
        $newserviceBillNo = $lastServiceBillId ? $lastServiceBillId + 1 : 1;
        $serviceBillNo = str_pad($newserviceBillNo, 3, '0', STR_PAD_LEFT);
        $divisions = Category::where('active', 'Y')->select('id', 'category_name')->get();

        return view('service_bill.create', compact('complaint', 'serviceBillNo', 'all_complaint_number', 'divisions'))->with('service_bill', $this->service_bill);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceBill  $serviceBill
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceBill $serviceBill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServiceBill  $serviceBill
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceBill $serviceBill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceBill  $serviceBill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceBill $serviceBill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceBill  $serviceBill
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceBill $serviceBill)
    {
        //
    }
}
