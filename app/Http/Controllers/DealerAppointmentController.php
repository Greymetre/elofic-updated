<?php

namespace App\Http\Controllers;

use App\DataTables\DealerAppointmentDataTable;
use App\Exports\DealerAppointmentExport;
use App\Models\Branch;
use App\Models\DealerAppointment;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use Excel;

class DealerAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DealerAppointmentDataTable $dataTable)
    {
        abort_if(Gate::denies('dealer_appointment'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return $dataTable->render('dealer_appointment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $branchs = Branch::where('active', 'Y')->get();
        $districts = District::where('active', 'Y')->get();
        return view('dealer_appointment.form', compact('branchs', 'districts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dealer_appointment = DealerAppointment::create($request->all());
        return redirect(route('dealer-appointment-thanks'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DealerAppointment  $dealerAppointment
     * @return \Illuminate\Http\Response
     */
    public function show(DealerAppointment $dealerAppointment)
    {
        return view('dealer_appointment.show', compact('dealerAppointment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DealerAppointment  $dealerAppointment
     * @return \Illuminate\Http\Response
     */
    public function edit(DealerAppointment $dealerAppointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DealerAppointment  $dealerAppointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DealerAppointment $dealerAppointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DealerAppointment  $dealerAppointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(DealerAppointment $dealerAppointment)
    {
        //
    }

    public function thanks(Request $request)
    {
        return view('dealer_appointment.thanks');
    }


    public function download(Request $request)
    {
        abort_if(Gate::denies('dealer_appointment_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        // return $request;
        return Excel::download(new DealerAppointmentExport($request), 'new_dealer_appointment.xlsx');
    }
    
}
