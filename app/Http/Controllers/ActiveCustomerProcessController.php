<?php

namespace App\Http\Controllers;

use App\Models\ActiveCustomerProcess;
use App\Models\ActiveCustomerProcessStep;
use App\Models\CustomerProcess;
use App\Models\CustomerProcessStep;
use App\Models\Customers;
use App\Models\EmployeeDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class ActiveCustomerProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ip() != '111.118.252.250') {
            return view('work_in_progress');
        }
        $users_ids = getUsersReportingToAuth();
        $customer_ids = EmployeeDetail::whereIn('user_id', $users_ids)->pluck('customer_id')->toArray();
        if ($request->ajax()) {
            $completedCustomerIds = ActiveCustomerProcess::select('customer_id')
                ->groupBy('customer_id')
                ->havingRaw('SUM(CASE WHEN status != ? THEN 1 ELSE 0 END) = 0', ['completed'])
                ->pluck('customer_id')
                ->toArray();

            $query = ActiveCustomerProcess::with([
                'customer:id,name,mobile,creation_date',
                'process:id,process_name',
                'assignedBy:id,name'
            ]);

            if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('Admin')) {
                $query->whereIn('customer_id', $customer_ids);
            }

            if($request->status == 'closed'){
                $query->whereIn('customer_id', $completedCustomerIds);
            }else if($request->status == 'active'){
                $query->whereNotIn('customer_id', $completedCustomerIds);
            }

            $query = $query->whereRaw('0 = 1')->select('active_customer_processes.*');

            return DataTables::of($query)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {
                    // $view = '<a href="' . route('active_customer_process.show', $row->id) . '" class="btn btn-sm btn-info">View</a>';
                    if (auth()->user()->can(['active_process_delete'])) {
                        $delete = '<button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '" title="Delete Active Process"><i class="material-icons">delete</i></button>';
                        return $delete;
                    }
                })

                ->editColumn('customer.creation_date', function ($row) {
                    return isset($row->customer->creation_date) ? date('d M Y', strtotime($row->customer->creation_date)) : '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('active_customer_process.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->ip() != '111.118.252.250') {
            dd("working");
        }
        abort_if(Gate::denies('active_process_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customers = Customers::where('active', 'Y')->select('id', 'name')->get();
        $processes = CustomerProcess::with('steps')->get();
        $active_process = new ActiveCustomerProcess();
        return view('active_customer_process.create', compact('customers', 'processes'))->with('active_process', $active_process);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->ip() != '111.118.252.250') {
            dd("working");
        }
        abort_if(Gate::denies('active_process_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'process_id' => 'required|array',
            'process_id.*' => 'required|exists:customer_processes,id',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->process_id as $process_id) {
                $activeProcess = ActiveCustomerProcess::updateOrCreate([
                    'customer_id' => $request->customer_id,
                    'process_id' => $process_id,
                ], [
                    'assigned_by' => auth()->id(),
                    'status' => 'pending',
                ]);
                $processSteps = CustomerProcessStep::where('customer_process_id', $process_id)->get();
                foreach ($processSteps as $step) {
                    ActiveCustomerProcessStep::updateOrCreate([
                        'active_customer_process_id' => $activeProcess->id,
                        'customer_process_step_id' => $step->id,
                    ], [
                        'status' => 'pending',
                    ]);
                }
            }
        });
        return redirect()->route('active_customer_process.index')->with('success', 'Process assigned successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ActiveCustomerProcess  $activeCustomerProcess
     * @return \Illuminate\Http\Response
     */
    public function show(ActiveCustomerProcess $activeCustomerProcess)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ActiveCustomerProcess  $activeCustomerProcess
     * @return \Illuminate\Http\Response
     */
    public function edit(ActiveCustomerProcess $activeCustomerProcess)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ActiveCustomerProcess  $activeCustomerProcess
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ActiveCustomerProcess $activeCustomerProcess)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ActiveCustomerProcess  $activeCustomerProcess
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActiveCustomerProcess $activeCustomerProcess)
    {
        //
    }
}
