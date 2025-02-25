<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PlannedSOP;
use App\Models\Branch;
use App\Models\Product;

use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

use App\DataTables\PlannedSopDatatable;
use DataTables;
// use Validator;
use Gate;

use Auth;

class PlannedSOPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->plannedsop = new PlannedSOP();
        $this->path = 'planned_s_o_p_s';
    }


    public function index(Request $request)
    {
        // abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('planned_sop.index');
    }

    public function getClaims(PlannedSopDatatable $dataTable, Request $request)
    {
        return $dataTable->render('planned_sop.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::select('branch_name' , 'id')->get();
        $products = Product::where('active' , "Y")->select('product_name' , 'id')->get();
        return view('planned_sop.create' , compact('branches' , 'products'))->with('plannedsop',$this->plannedsop);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'product_id' => 'required',
            'plan_next_month' => 'required'
        ]);
        try{
            $this->plannedsop->create([
               'branch_id' => $request->branch_id ?? '',
               'product_id' => $request->product_id ?? '',
               'plan_next_month' => $request->plan_next_month ?? '',
               'opening_stock'   => $request->opening_stock ?? NULL,
               'budget_for_month' => $request->budget_for_month ?? NULL,
               'last_month_sale'  => $request->last_month_sale ?? NULL,
               'last_three_month_avg' => $request->last_three_month_avg ?? NULL,
               'last_year_month_sale' => $request->last_year_month_sale ?? NULL,
               'sku_unit_price'       => $request->sku_unit_price ?? NULL,
               's_op_val'             => $request->s_op_val ?? NULL,
               'top_sku'              => $request->top_sku  ?? NULL,
               'created_by'           => Auth::user()->name ?? NULL,
            ]);
            return Redirect::to('planned-sop')->with('message_success', 'Planned S & OP Created SucessFully');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage())->withInput();
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
