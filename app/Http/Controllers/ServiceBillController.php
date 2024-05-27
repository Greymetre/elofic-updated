<?php

namespace App\Http\Controllers;

use App\DataTables\ServiceBillDataTable;
use App\Models\ServiceBill;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;


class ServiceBillController extends Controller
{
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
