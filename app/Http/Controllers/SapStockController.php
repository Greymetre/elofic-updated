<?php

namespace App\Http\Controllers;

use App\DataTables\SAPStockDataTable;
use App\Models\SapStock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PlannedSOP;
use App\Models\PlannedSopSaleData;
use App\Models\PrimarySales;
use Gate;

class SapStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SAPStockDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('sap_stock_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('sap_stock.index');
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
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function show(SapStock $sapStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function edit(SapStock $sapStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SapStock $sapStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(SapStock $sapStock)
    {
        //
    }
}
