<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;

class ComplaintApiController extends Controller
{

    public function __construct()
    {
        $this->complaint = new Complaint();


        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
             if($request->user()->hasRole('Service Eng') || $request->user()->hasRole('superadmin')){
                if ($request->user()->hasRole('superadmin')) {
                    $complaints = Complaint::select('id', 'complaint_number', 'complaint_status' , 'complaint_date')->get();
                } else {
                    $complaints = Complaint::where('assign_user', $request->user()->id)
                        ->select('id', 'complaint_number', 'complaint_status' , 'complaint_date')
                        ->get();
                }
                if($complaints){
                    return response()->json(['status' => 'success', 'data' => $complaints], $this->successStatus);
                }else{
                    return response()->json(['status' => 'success', 'data' => "No Complaints"], $this->notFound);
                }
             }else{
                return response()->json(['status' => 'error', 'message' => 'Complaint can access only Service Eng'] , $this->notFound);
             }
        }catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
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
    public function show($id , Request $request)
    {
         try{
             if($request->user()->hasRole('Service Eng') || $request->user()->hasRole('superadmin')){

                $complaint = Complaint::with([
                    'customer:id,customer_name,customer_number,customer_email,customer_address,customer_place,customer_state,customer_district,customer_city,customer_pindcode',
                    'customer.pincodeDetails:id,pincode',
                    'service_center_details:id,customer_code,name',
                    'createdbyname:id,name'
                ])->where('id', $id)
                    ->when(!$request->user()->hasRole('superadmin'), function ($query) use ($request) {
                        return $query->where('assign_user', $request->user()->id);
                    })
                    ->first();



                if (!$complaint) {
                    return response()->json(null);
                }

                // Prepare the data dynamically
                $data = collect($complaint->only([
                    'complaint_number', 
                    'complaint_date', 
                    'complaint_status'
                ]))->map(fn($value) => $value === "" ? null : $value)->toArray();

                // Append related fields
                $data += [
                    "service_center_name" => optional($complaint->service_center_details)->customer_code 
                        ? '[' . $complaint->service_center_details->customer_code . '] ' . $complaint->service_center_details->name
                        : null,
                    "created_by" => optional($complaint->createdbyname)->name,
                ];

                // Append customer details dynamically
                $data += collect($complaint->customer?->only([
                    'customer_name', 'customer_number', 'customer_email', 'customer_address', 
                    'customer_place', 'customer_state', 'customer_district', 'customer_city'
                ]))->map(fn($value) => $value === "" ? null : $value)->toArray();

                // Append customer pincode safely
                $data["customer_pincode"] = optional($complaint->customer?->pincodeDetails)->pincode ?? null;

                if($complaint){
                    return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
                }else{
                    return response()->json(['status' => 'success', 'data' => "No Complaints"], $this->successStatus);
                }
             }else{
                return response()->json(['status' => 'error', 'message' => 'Complaint can access only Service Eng'] , $this->notFound);
             }
        }catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
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
