<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PlannedSOP;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\Division;

use App\Exports\PlannedSopExport;
use App\Exports\PlannedSopTemplate;
use App\Exports\PlannedSopPUMExport;

use App\Imports\PlannedSopImport;

use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

use App\DataTables\PlannedSopDatatable;
use DataTables;
// use Validator;
use Gate;
use Excel;
use Auth;

use Carbon\Carbon;

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
        $divisions = Category::select('category_name' , 'id')->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear , $currentYear + 2);
        return view('planned_sop.index' , compact('divisions' , 'years'));
    }

    public function plannedSopList(PlannedSopDatatable $dataTable, Request $request)
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
        abort_if(Gate::denies('sop_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::select('branch_name' , 'id')->get();
        $divisions = Category::select('category_name' , 'id')->get();
        $products = Product::where('active' , "Y")->select('product_name' , 'id')->get();
        $main_divisions = Division::where('active' , "Y")->select('division_name' , 'id')->get();
        return view('planned_sop.create' , compact('branches' , 'products' , 'divisions' , 'main_divisions'))->with('plannedsop',$this->plannedsop);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('sop_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try{
            if(isset($request->planning_month)){
               $formatted_date = Carbon::createFromFormat('F Y', $request->planning_month)->startOfMonth();
               $for_oder_id = $formatted_date->format("F/Y");
               $planning_month = $formatted_date->format("Y-m-d");
            }
            $division = Category::find($request->product_division);
            if(isset($request->view_only)){
                if(Auth::user()->roles[0]['name'] == "superadmin"){
                    $view_only = implode(',', $request->view_only);
                }else{
                    $view_only = Auth::user()->division_id;
                }
            }
            foreach ($request->product_id as $key => $product) {
                $total = PlannedSOP::latest('id')->value('id');
                $formattedTotal = str_pad($total, 3, '0', STR_PAD_LEFT);
                $order_id = strtoupper(substr($division->category_name, 0, 3)) . '/' . $for_oder_id . '/'. $formattedTotal;  
                $this->plannedsop->updateOrCreate(
                    [
                        'planning_month' => $planning_month,
                        'product_id'     => $request->product_id[$key] ?? '',
                        'branch_id'      => $request->branch_id ?? '',
                        'plan_next_month'=> $request->plan_next_month[$key] ?? '',
                    ],
                    [
                        'order_id'             => $order_id ?? '',
                        'division_id'          => $request->product_division ?? Null,
                        'opening_stock'        => $request->opening_stock[$key] ?? NULL,
                        'open_order_qty'       => $request->open_order_qty[$key] ?? Null,
                        'production_qty'       => $request->for_production_qty[$key] ?? 0,
                        'budget_for_month'     => $request->budget_for_month[$key] ?? NULL,
                        'last_month_sale'      => $request->last_month_sale[$key] ?? NULL,
                        'last_three_month_avg' => $request->last_three_month_avg[$key] ?? NULL,
                        'last_year_month_sale' => $request->last_year_month_sale[$key] ?? NULL,
                        'sku_unit_price'       => $request->sku_unit_price[$key] ?? NULL,
                        's_op_val'             => $request->s_op_val[$key] ?? NULL,
                        'top_sku'              => $request->top_sku[$key] ?? NULL,
                        'created_by'           => Auth::user()->name ?? NULL,
                        'view_only'            => $view_only ?? NULL,
                        'status'               => 1,
                    ]
                );
            }
            return Redirect::to('planned-sop')->with('message_success', 'Planned S & OP Created SucessFully');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function sop_download(Request $request)
    {
        abort_if(Gate::denies('sop_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if($request->division_id == 1){
           if (!isset($request->financial_year)) {
                return back()->with('message_error', 'Please select a financial year.');
            }            
            return Excel::download(new PlannedSopPUMExport($request), 'plannedsop.xlsx');
        }
        return Excel::download(new PlannedSopExport($request), 'plannedsop.xlsx');
    }

     public function sop_template(Request $request)
    {
        abort_if(Gate::denies('sop_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PlannedSopTemplate($request), 'PlannedSopTemplate.xlsx');
    }

    public function sop_import(Request $request){
        abort_if(Gate::denies('sop_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new PlannedSopImport,request()->file('import_file'));
        return back();
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
        abort_if(Gate::denies('sop_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $plannedsop = PlannedSOP::find($id);
        if(!$plannedsop){
          return redirect()->back()->with('message_danger', 'Record not found');
        }
        $branches = Branch::select('branch_name' , 'id')->get();
        $divisions = Category::select('category_name' , 'id')->get();
        $products = Product::where('active' , "Y")->select('product_name' , 'id')->get();
        return view('planned_sop.edit' , compact('branches' , 'products' , 'divisions' , 'plannedsop'));
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
        abort_if(Gate::denies('sop_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $plannedsop = PlannedSOP::find($id);
        if(!$plannedsop){
          return redirect()->back()->with('message_danger', 'Record not found');
        }
        try{
            if(isset($request->planning_month)){
               $formatted_date = Carbon::createFromFormat('F Y', $request->planning_month)->startOfMonth();
               $planning_month = $formatted_date->format("Y-m-d");
               $request["planning_month"] = $planning_month;
            }
            $plannedsop->update($request->all());
            if(isset($request->status) && $request->status == 2){
                 $plannedsop->update(['verify_by'=>Auth::user()->name]);
            }
            return Redirect::to('planned-sop')->with('message_success', 'Planned S&OP Updated Sucessfully.');
        }
        catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('sop_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $plannedsop = PlannedSOP::find($id);
        if(!$plannedsop){
          return redirect()->back()->with('message_danger', 'Record not found');
        }
        $plannedsop->delete();
        return redirect()->back()->with('message_success', 'Record deleted Sucessfully.');
    }

    // multistatus changes
    public function planned_sop_multistatus_change(Request $request){
        $ids = $request->ids ?? [];
        $value = $request->value ?? '';
        $status = "";
        if($value == 2){
           $status = "Verified";
        }else if($value == 3){
           $status = "Approved";
        }
        $plannedsops = PlannedSOP::whereIn('id' , $ids)->get();
        $sop_ids = [];
        $update = False;
        foreach ($plannedsops as $plannedsop) {
            if($plannedsop->status != $value){
                $plannedsop->update([
                    'status' => $value,
                ]);
                $update = true;
                if($value == 2){
                     $plannedsop->update(['verify_by'=>Auth::user()->name]);
                }
            }
        }
        $message = $status .' '. 'Sucessfully';
        return response()->json(['status' => true  , 'update' => $update , 'message' => $message]);
    }
}
