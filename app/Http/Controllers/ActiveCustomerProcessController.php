<?php

namespace App\Http\Controllers;

use App\Models\ActiveCustomerProcess;
use Illuminate\Http\Request;

class ActiveCustomerProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('work_in_progress');
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
