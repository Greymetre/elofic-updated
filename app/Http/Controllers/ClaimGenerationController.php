<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClaimGeneration;
use App\Models\Customers;
use App\Models\Complaint;
use App\Models\ClaimGenerationDetail;

use App\DataTables\ClaimGenerationDatatable;

use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

use DataTables;
use Validator;
use Gate;
use Auth;

class ClaimGenerationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->claim_generation = new ClaimGeneration();
        $this->path = 'claim_generations';
    }


    public function index()
    {
        abort_if(Gate::denies('claim_generation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
         $service_centers = Customers::where('customertype', '4')->select('id', 'name', 'customer_code')->get();
        return view('claim-generation.index' , compact('service_centers'));
    }

    public function getClaims(ClaimGenerationDatatable $dataTable, Request $request)
    {
        return $dataTable->render('claim-generation.index');
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
        abort_if(Gate::denies('generate_claim'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'start_month' => 'required',
            // 'end_month' => 'required',
            'service_center' => 'required'
        ]);
        try {
            $startDate = Carbon::createFromFormat('F Y', $request->start_month)->startOfMonth()->startOfDay()->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('F Y', $request->start_month)->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');

            $complaints = Complaint::with(['service_bill.service_bill_products' , 'service_center_details'])->whereHas('service_bill', function ($query) use ($startDate, $endDate) {
                $query->where('status', 3)
                      ->whereBetween('updated_at', [$startDate, $endDate]); // Ensures full range is covered
            })
            ->where('service_center' , $request->service_center)
            ->get();

           $formatted_date = Carbon::createFromFormat('F Y', $request->start_month)->startOfMonth();
           $month = $formatted_date->format('M'); // Short month format (e.g., "Feb")
           $year = $formatted_date->format('Y');

           if ($complaints->isNotEmpty()) {
                $firstComplaint = $complaints[0]; // Get the first complaint
                $serviceCenterAcronym = collect(explode(' ', $firstComplaint->service_center_details->name))
                ->map(function ($word) {
                    // Remove special characters and numbers from each word
                    $cleanWord = preg_replace('/[^A-Za-z]/', '', $word);
                    return strtoupper(substr($cleanWord, 0, 1)); // Get first letter of cleaned word
                })
                ->implode('');
                $claimNumber ='#'.$serviceCenterAcronym . '-' . $month . '-' . $year;
                $allTotal = $complaints->sum(function ($complaint) {
                    return $complaint->service_bill ? (float) $complaint->service_bill->service_bill_products->sum('subtotal') : 0.0;
                });
            }

            if(isset($claimNumber)){
                $claim_generation = ClaimGeneration::updateOrCreate(
                    [
                        'month' => $month,
                        'year' => $year,
                        'service_center_id' => $request->service_center,
                    ],
                    [
                        'claim_number' => $claimNumber,
                        'claim_amount' => $allTotal ?? NULL,
                    ]
                );
                if(isset($claim_generation)){
                    foreach ($complaints as $key => $complaint) {
                        ClaimGenerationDetail::updateOrCreate([ 
                                'claim_generation_id' => $claim_generation->id,
                                'complaint_id'        => $complaint->id
                        ],[]);
                    }
                }

               return redirect()->back()->with(
                    'message_success', 
                    'Your claim has been generated for ' . $complaints[0]->service_center_details->name . ' for ' . $month .' - '. $year
                );
            }


            return redirect()->back()->with(
                'message_danger', 
                'No claims found for the selected service center in ' . $month .' - '. $year
            );
  
        } catch (\Exception $e) {
              return redirect()->back()->with(
                'message_danger', 
                'An error occurred: ' . $e->getMessage()
            );
        }
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
        abort_if(Gate::denies('claim_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $claimGeneration = ClaimGeneration::find($id);
        if(!$claimGeneration){
          return redirect()->back()->with('message_danger', 'Record not found');
        }
        return view('claim-generation.create', compact('claimGeneration'));
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
