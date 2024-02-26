<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpensesRequest;
use App\Http\Requests\UpdateExpensesRequest;
use App\Models\Expenses;
use App\Models\User;
use App\Models\ExpensesType;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use DB;
use Auth;


class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  
        if ($request->ajax()) {
            $data = Expenses::with(['expense_type','users']);
            $data = $data->select(\DB::raw(with(new Expenses)->getTable().'.*'))->groupBy('id');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('id', function ($query) {
                    return $query->id??'';
                })
                ->addColumn('users.name', function ($query) {
                    return $query->users->name??'';
                })
                ->addColumn('expense_type.name', function ($query) {
                    return $query->expense_type->name??'';
                })
                // ->addColumn('date', function ($query) {
                //     return $query->date??'';
                // })
                // ->addColumn('claim_amount', function ($query) {
                //     return $query->claim_amount??'';
                // })
                // ->addColumn('claim_amount', function ($query) {
                //     return $query->claim_amount??'';
                // })

                ->addColumn('is_active', function ($query) {
                      $active = ($query->is_active == '1') ? 'checked="" value="'.$query->is_active.'"' : 'value="'.$query->is_active.'"';
                      return '<div class="togglebutton">
                          <label>
                            <input type="checkbox"'.$active.' id="'.$query->id.'" class="activeRecord">
                            <span class="toggle"></span>
                          </label>
                        </div>';
              })
                
                ->addColumn('action', function ($query) {
                    $btn = '';
                    $activebtn = '';

                    $btn = $btn . '<a href="'.route("expenses.edit", ["expense" => $query->id]).'" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' ' . trans('panel.expenses.title_singular') . '">
                               <i class="material-icons">edit</i>
                                </a>';

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;
                })
                ->rawColumns(['is_active','action'])
                ->make(true);
        } 
        return view('expenses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userids = getUsersReportingToAuth();
        $users= User::where('active','=','Y')->where(function($query) use($userids){
                            if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                            {
                                $query->whereIn('id',$userids);
                            }
                            })->select('id','name')->get();
        $expensestypes = ExpensesType::get();

        return view('expenses.create',compact('users','expensestypes'));    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreExpensesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     $rules = [
            'expenses_type'    => 'required', 
            'user_id'          => 'required', 
            'claim_amount'    => 'required', 
            'date'            => 'required', 
        ];


        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $data = $request->all();

             $data = array(
                'expenses_type' =>$request->expenses_type??NULL,
                'user_id' =>$request->user_id??NULL,
                'date' =>$request->date??NULL,
                'claim_amount' =>$request->claim_amount??NULL,
                'start_km' =>$request->start_km??NULL,
                'stop_km' =>$request->stop_km??NULL,
                'total_km ' =>$request->total_km ??NULL,
                'note' =>$request->note??NULL,
                'created_by' =>Auth::user()->id??NULL,
                'total_km' =>$request->total_km??NULL,
               );

            $expenses = Expenses::create($data);

            if ($request->hasFile('expense_file')){
                $file = $request->file('expense_file');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $expenses->addMedia($file)
                        ->usingFileName($customname)
                        ->toMediaCollection('expense_file');
            }
         return redirect(route('expenses.index'))->with('message', 'expense added successfully');
        }else {
            return redirect()->back()->withErrors($validator)->withInput();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function show(Expenses $expenses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function edit(Expenses $expenses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExpensesRequest  $request
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExpensesRequest $request, Expenses $expenses)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expenses $expenses)
    {
        //
    }

    public function changeStatus(Request $request){
        $expenses = Expenses::find($request->id);
        if($request->active == '0'){
            $expenses->checker_status = 1;
        }else{
            $expenses->checker_status = 0;
        }

        $expenses->save();
        return response()->json(['status'=>'success', 'message'=>'Status changed successfully']);
    }

}
