<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceBillComplaintType as ServiceBillComplaintTypes;

use Gate;
use DB;
use Excel;
use Validator;
use DataTables;
use Carbon\Carbon;
use Auth;

class ServiceBillComplaintType extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('service-bill-complaint.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function getServiceComplaintType(Request $request){
        $query = ServiceBillComplaintTypes::with([
            'service_complaint_reasons',
            'service_group_complaints.subcategory'
        ])->latest()->newQuery();
       

         return DataTables::of($query)
            ->addIndexColumn()
            ->addIndexColumn()
            ->addColumn('status', function ($query) {
                if($query->complaint_status == '0'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-secondary">Open</span></a>';
                }elseif($query->complaint_status == '1'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-warning">Pending</span></a>';
                }elseif($query->complaint_status == '2'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-info">Work Done</span></a>';
                }elseif($query->complaint_status == '3'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-success">Completed</span></a>';
                }elseif($query->complaint_status == '4'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-primary">Closed</span></a>';
                }elseif($query->complaint_status == '5'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-danger">Canceled</span></a>';
                }
            })
            ->addColumn('subcategory', function ($query) {
                return $query->service_group_complaints->subcategory->subcategory_name ?? '';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
