<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DealerAppointment;
use App\Models\District;
use Illuminate\Http\Request;

class DealerAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dealer_appointment.index');
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
        dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DealerAppointment  $dealerAppointment
     * @return \Illuminate\Http\Response
     */
    public function show(DealerAppointment $dealerAppointment)
    {
        //
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
}
