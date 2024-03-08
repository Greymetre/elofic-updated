<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpensesRequest;
use App\Http\Requests\UpdateExpensesRequest;
use App\Models\Expenses;
use App\Models\User;
use App\Models\ExpensesType;
use App\Models\ExpenseLog;
use App\Models\Media;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use DB;
use Auth;
use Excel;
use App\Exports\ExcelExport;




class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  
        

        //$expense_types = ExpensesType::orderBy('id','desc')->get();

        $userids = getUsersReportingToAuth();
        $users= User::where('active','=','Y')->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name','employee_codes')->get();


        $all_user_branches = User::with('getbranch')->whereIn('id', $userids)->orderBy('branch_id')->get();
        $branches = array();
        $all_branch = array();
        $bkey = 0;
        foreach ($all_user_branches as $k => $val) {
            if($val->getbranch){
                if (!in_array($val->getbranch->id, $all_branch)) {
                    array_push($all_branch, $val->getbranch->id);
                    $branches[$bkey]['id'] = $val->getbranch->id;
                    $branches[$bkey]['name'] = $val->getbranch->branch_name;
                    $bkey++;
                }
            }
        }



        $all_user_divisions = User::with('getdivision')->whereIn('id', $userids)->orderBy('branch_id')->get();
        $divisions = array();
        $all_division = array();
        $dkey = 0;


        foreach ($all_user_divisions as $dv => $div_val) {
            if ($div_val->getdivision) {
                if (!in_array($div_val->getdivision->id, $all_division)) {
                    array_push($all_division, $div_val->getdivision->id);
                    $divisions[$dkey]['id'] = $div_val->getdivision->id;
                    $divisions[$dkey]['name'] = $div_val->getdivision->division_name;
                    $dkey++;
                }
            }
        }



        $expense_ids = Expenses::orderBy('id','desc')->get();

        $pay_rolls = Config('constants.pay_roll');

        if($request->ajax()) {

            $data = Expenses::with(['expense_type','users'])->orderBy('id','desc');
              if(!empty($request['payroll']))
                {
                  // $payrollid = $request['payroll']; 
                  // $userid = User::where('payroll',$payrollid)->pluck('id');
                  // $data->whereIn('user_id', $userid);

                  $payrollid = $request['payroll']; 

                  // $userid = User::where('payroll',$payrollid)->pluck('id');
                  // $data->whereIn('user_id', $userid); 

                 $userid = User::where('active','=','Y')->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->where('payroll',$payrollid)->pluck('id');

                 $data->whereIn('user_id', $userid); 
                }
        
                if(!empty($request['executive_id']))
                {
                    $data->where('user_id', $request['executive_id']);
                }
                if(!empty($request['expenses_type']))
                {
                    $data->where('expenses_type', $request['expenses_type']);
                }

               if(!empty($request['branch_id']))
                {
                   $branch_user_id = User::where('branch_id',$request['branch_id'])->pluck('id');
                    if(!empty($branch_user_id)){
                       $data->whereIn('user_id', $branch_user_id);  
                    }
                }
              if(!empty($request['division_id']))
                {
                   $division_user_id = User::where('division_id',$request['division_id'])->pluck('id');
                    if(!empty($division_user_id)){
                       $data->whereIn('user_id', $division_user_id);  
                    }
                }

              if(!empty($request['expense_id']))
                { 
                  $data->where('id', $request['expense_id']);  
                   
                }


                if(!empty($request['start_date']) && !empty($request['end_date']))
                {
                  $data->whereBetween('date',[$request['start_date'],$request['end_date']]); 
                }

                if(!empty($request['status']))
                {
                    $data->where('checker_status', $request['status']);
                }

              $data = $data->select(\DB::raw(with(new Expenses)->getTable().'.*'))->groupBy('id');    


            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('id', function ($query) {
                    return $query->id??'';
                })
                ->addColumn('users.name', function ($query) {
                      $name = '('.$query->users->employee_codes.')'.$query->users->name;
                    return $name;
                

                })
                ->addColumn('expense_type.name', function ($query) {
                    return $query->expense_type->name??'';
                })

                ->editColumn('date', function ($query) {
                    return $query->date??'';
                })
                ->editColumn('claim_amount', function ($query) {
                    return $query->claim_amount??'';
                })
                ->editColumn('approve_amount', function ($query) {
                    return $query->approve_amount??'';
                })
                ->editColumn('note', function ($query) {
                    return $query->note??'';
                })
                ->editColumn('total_km', function ($query) {
                    return $query->total_km??'';
                })

                ->addColumn('users.getbranch.branch_name', function ($query) {
                    return $query->users->getbranch->branch_name??'';
                })

                ->addColumn('date_create', function ($query) {
                    return  date("Y-m-d g:i a", strtotime($query->created_at));
                })

                ->addColumn('checker_status', function ($query) {
                    $btn = '';
                    $activebtn = '';
                    if($query->checker_status =='1'){
                    $btn = $btn."<a href='".route("expenses.show", ["expense" => $query->id])."'><span class='btn btn-success'>Approved</span></a>";    

                    }elseif($query->checker_status =='2'){
                      $btn = $btn."<a href='".route("expenses.show", ["expense" => $query->id])."'><span class='btn btn-danger'>Rejected</span></a>";    
                    }
                    elseif($query->checker_status =='3'){
                      $btn = $btn."<a href='".route("expenses.show", ["expense" => $query->id])."'><span class='btn btn-dark'>Checked</span></a>";    
                    }

                    else{
                    $btn = $btn."<a href='".route("expenses.show", ["expense" => $query->id])."'><span class='btn btn-warning'>Pending</span></a>";    
                    }
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;

              })
                
             ->addColumn('action', function ($query) {
                    $btn = '';
                    $activebtn = '';

                     if(auth()->user()->can(['expenses_edit']))
                      {

                    $btn = $btn . '<a href="'.route("expenses.edit", ["expense" => $query->id]).'" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' ' . trans('panel.expenses.title_singular') . '">
                               <i class="material-icons">edit</i>
                                </a>';
                      }

                      if(auth()->user()->can(['expenses_delete']))
                      {

                    $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.expenses.title_singular').'">
                                            <i class="material-icons">clear</i>
                                          </a>';
                       }                   
                              

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;
                })
                ->rawColumns(['checker_status','action','users.name'])
                ->make(true);
        } 
        return view('expenses.index',compact('users','branches','pay_rolls','divisions','expense_ids'));
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
                'total_km' =>$request->total_km ??NULL,
                'note' =>$request->note??NULL,
                'created_by' =>Auth::user()->id??NULL,
               );

            $expenses = Expenses::create($data);

            if($expenses){

             $logdata = array(
                'log_date' => date('Y-m-d'),
                'expense_id' => $expenses->id,
                'created_by' => Auth::user()->id,
                'status_type' => 'generated'
             );
             ExpenseLog::create($logdata);

            }


            if($request->hasFile('expense_file')){
                // $file = $request->file('expense_file');
                // $customname = time() . '.' . $file->getClientOriginalExtension();
                // $expenses->addMedia($file)
                //         ->usingFileName($customname)
                //         ->toMediaCollection('expense_file');

                $files = $request->file('expense_file');
                foreach($files as $file){
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $expenses->addMedia($file)
                        ->usingFileName($customname)
                        ->toMediaCollection('expense_file');
                 }       

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
    public function show(Expenses $expense)
    {  
       
         $logdetails = ExpenseLog::with('logusers')->where('expense_id',$expense->id)->orderBy('id','desc')->get();
         //$expense->update(['accountant_status'=>'3','checker_status'=>'3']);
         return view('expenses.show',compact('expense','logdetails'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function edit(Expenses $expense)
    {    
        $userids = getUsersReportingToAuth();
        $users= User::where('active','=','Y')->where(function($query) use($userids){
                            if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                            {
                                $query->whereIn('id',$userids);
                            }
                            })->select('id','name')->get();
        $expensestypes = ExpensesType::get(); 
      return view('expenses.edit',compact('users','expensestypes','expense'));    

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExpensesRequest  $request
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expenses $expense)
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
          


            $expense_details = ExpensesType::where('id',$request->expenses_type)->first();
            $expense_details->allowance_type_id;


            if($expense_details->allowance_type_id == '1'){

           $data = array(
                'expenses_type' =>$request->expenses_type??NULL,
                'user_id' =>$request->user_id??NULL,
                'date' =>$request->date??NULL,
                'claim_amount' =>$request->claim_amount??NULL,
                'start_km' =>$request->start_km??NULL,
                'stop_km' =>$request->stop_km??NULL,
                'total_km' =>$request->total_km ??NULL,
                'note' =>$request->note??NULL,
                'created_by' =>Auth::user()->id??NULL,
               );
            $expense->update($data);

            }else{

           $data = array(
                'expenses_type' =>$request->expenses_type??NULL,
                'user_id' =>$request->user_id??NULL,
                'date' =>$request->date??NULL,
                'claim_amount' =>$request->claim_amount??NULL,
                'start_km' =>NULL,
                'stop_km' =>NULL,
                'total_km' =>NULL,
                'note' =>$request->note??NULL,
                'created_by' =>Auth::user()->id??NULL,
               );
            $expense->update($data);      

            }

            if($expense){
             $logdata = array(
                'log_date' => date('Y-m-d'),
                'expense_id' => $expense->id,
                'created_by' => Auth::user()->id,
                'status_type' => 'updated'
             );
             ExpenseLog::create($logdata);
            }


            if($request->hasFile('expense_file')){

                 //$expense->clearMediaCollection('expense_file');

                $files = $request->file('expense_file');
                foreach($files as $file){
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $expense->addMedia($file)
                        ->usingFileName($customname)
                        ->toMediaCollection('expense_file');
                 }                



            }
         return redirect(route('expenses.index'))->with('message', 'expense updated successfully');
        }else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
      

    }


    public function expenseDownload(Request $request){

        $filename = 'expense-report.xlsx';        
        $executive_id = $request->executive_id;
        $expenses_type = $request->expenses_type;
        $branch_id = $request->branch_id;
        $status = $request->status;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $payroll = $request->payroll;
        $division_id = $request->division_id;
        $expense_id = $request->expense_id;
 
        $expenses = Expenses::with(['expense_type','users','approve_reject']);

        if(!empty($payroll))
                {
                  $userid = User::where('payroll',$payroll)->pluck('id');
                  $expenses = $expenses->whereIn('user_id', $userid);
                }
        if(!empty($executive_id)){
            $expenses = $expenses->where(['user_id'=>$executive_id]);
        }

        if(!empty($executive_id)){
            $expenses = $expenses->where(['expenses_type'=>$expenses_type]);
        }

        if(!empty($branch_id)){
            $branch_user_id = User::where('branch_id',$branch_id)->pluck('id');
                    if(!empty($branch_user_id)){
                       $expenses->whereIn('user_id', $branch_user_id);  
                    }
        }

        if(!empty($start_date) && !empty($end_date)){
            $expenses->whereBetween('date',[$start_date,$end_date]); 
        }

        if(!empty($status))
        {
            $expenses->where('checker_status', $status);
        }

        if(!empty($division_id))
        {
           $division_user_id = User::where('division_id',$division_id)->pluck('id');
            if(!empty($division_user_id)){
               $expenses->whereIn('user_id', $division_user_id);  
            }
        }

      if(!empty($expense_id))
        { 
          $expenses->where('id', $expense_id);  
           
        }






        $expenses = $expenses->orderBy('id','desc')->get();

        $data = $expenses->map(function ($item, $key) {

                    if($item->checker_status == '1'){
                        $status = "Approved";
                    }
                    elseif($item->checker_status == '2') {
                        $status = "Rejected";
                    }else{
                        $status = "Pending";
                    }

                return [
                   
                        $item->id??"",
                        $item->date??"", 
                        $item->users->employee_codes??"",
                        $item->users->name??"",
                        $item->users->getbranch->branch_name??'',
                        $item->users->getdivision->division_name??'',
                        $item->expense_type->name??"",
                        $item->expense_type->rate??"",
                        $item->claim_amount??"",
                        $item->approve_amount??"",
                        $item->note??"",
                        $item->total_km??"",
                        $item->reason??"",
                        $status,
                        $item->approve_reject->name??"",
        
                ];
        })->toArray();

        $export = new ExcelExport([
            '#Expense Id',
            'Created at',
            'Emp Code',
            'User Name',
            'Branch',
            'Division',
            'Expense Type',
            'Rate',
            'Claim Amount',
            'Approve Amount',
            'Note',
            'Total km',
            'Reason',
            'Expense Status',
            'Status BY'
        ], $data);

        return Excel::download($export, $filename);


    }


    public function rejectExpense(Request $request){
         $expense_id = $request->expense_id; 
         $reason = $request->reason??NULL;
       Expenses::where('id',$expense_id)->update(['reason'=>$reason,'checker_status'=>'2','approve_reject_by'=>Auth::user()->id,'approve_amount'=>NULL]); 
        //return redirect(route('expenses.index')); 

        if($expense_id){
         $logdata = array(
            'log_date' => date('Y-m-d'),
            'expense_id' => $expense_id,
            'created_by' => Auth::user()->id,
            'status_type' => 'rejected'
         );
         ExpenseLog::create($logdata);
        }

        return redirect(route('expenses.show',["expense" => $expense_id]))->with('danger', 'Expense rejected');

    }

    public function approveExpense(Request $request){
        $expense_detail = Expenses::where('id',$request->expense_new_id)->first();
        $approve_amnt = $request->approve_amnt;
        $expense_id = $request->expense_new_id; 
        $reason = $request->reasons??NULL;

        if($expense_detail->claim_amount < $approve_amnt){
         return redirect(route('expenses.show',["expense" => $expense_id]))->with('success', 'Approve amount greater than to claim amount');
        }

        Expenses::where('id',$expense_id)->update(['reason'=>$reason,'checker_status'=>'1','approve_reject_by'=>Auth::user()->id,'approve_amount'=>$approve_amnt]); 

         if($expense_id){
         $logdata = array(
            'log_date' => date('Y-m-d'),
            'expense_id' => $expense_id,
            'created_by' => Auth::user()->id,
            'status_type' => 'approved'
         );
         ExpenseLog::create($logdata);
        }

        //return redirect(route('expenses.index'));
        return redirect(route('expenses.show',["expense" => $expense_id]))->with('success', 'Approved amount');

    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    
        try
        { 
            ExpenseLog::where('expense_id',$id)->delete();
            $expenses = Expenses::find($id);
            if($expenses->delete())
            {
                return response()->json(['status' => 'success','message' => 'Expense deleted successfully!']);
            }
            return response()->json(['status' => 'error','message' => 'Error in Expense Delete!']);
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

    }

    public function changeStatus(Request $request){
        $expenses = Expenses::find($request->id);
        $expenses->checker_status = '3';
        $expenses->approve_reject_by = Auth::user()->id;
        $expenses->save();

        if($request->id){
         $logdata = array(
            'log_date' => date('Y-m-d'),
            'expense_id' => $request->id,
            'created_by' => Auth::user()->id,
            'status_type' => 'checked'
         );
         ExpenseLog::create($logdata);
        }

        return response()->json(['status'=>'success', 'message'=>'Status checked successfully']);
       // return redirect(route('expenses.index'));
    }


    public function uncheckStatus(Request $request){
        $expenses = Expenses::find($request->id);
        $expenses->checker_status = '0';
        $expenses->approve_amount = null;
        $expenses->approve_reject_by = Auth::user()->id;
        $expenses->save();

        if($request->id){
         $logdata = array(
            'log_date' => date('Y-m-d'),
            'expense_id' => $request->id,
            'created_by' => Auth::user()->id,
            'status_type' => 'unchecked'
         );
         ExpenseLog::create($logdata);
        }

        return response()->json(['status'=>'success', 'message'=>'Status unchecked successfully']);
       // return redirect(route('expenses.index'));
    }






    public function getexpenseType(Request $request){
        $payroll = $request->payroll;
        $expenseTypes = ExpensesType::where('payroll_id',$payroll)->get();  
        $html = "";  
        $html .= "<option value='' >Select Expense Type</option>";
        foreach ($expenseTypes as $expenseType) {
            $html .= "<option value='".$expenseType->id."'>".ucwords($expenseType->name)."</option>";
            }
        return $html;   

    }


   public function getexpenseUserType(Request $request){
        $user_id = $request->user_id;
        $userDetail = User::where('id',$user_id)->first();
        $expenseTypes = ExpensesType::where('payroll_id',$userDetail->payroll)->get(); 
         $html = "";  
         $html .= "<option value=''>Select Expense Type</option>";
        if(!empty($userDetail->payroll)){
        foreach ($expenseTypes as $expenseType) {
            $html .= "<option value='".$expenseType->id."' data-allowtype='".$expenseType->allowance_type_id."' data-rate='".$expenseType->rate."'  >".ucwords($expenseType->name)."</option>";
            }
          }  

        return $html;

   }




   public function getexpenseUserTypeEdit(Request $request){

        $user_id = $request->user_id;
        $expenses_type = $request->expenses_type;
        $userDetail = User::where('id',$user_id)->first();
        $expenseTypes = ExpensesType::where('payroll_id',$userDetail->payroll)->get(); 
        $selected = '';

         $html = "";  
         //$html .= "<option value=''>Select Expense Type</option>";

        if(!empty($userDetail->payroll) && $expenseTypes->count()>0){ 
        foreach ($expenseTypes as $expenseType) {
               if($expenses_type == $expenseType->id){ 
                $selected = 'selected'; 
               }else{
                 $selected ="";
               } 

            $html .= "<option value='".$expenseType->id."' data-allowtype='".$expenseType->allowance_type_id."' data-rate='".$expenseType->rate."' ".$selected.">".ucwords($expenseType->name)."</option>";
            }
          }  

        return $html;

   }



   public function deletImages(Request $request){
     $id = $request->id;
     $expense_id = $request->expense_id;
     Media::where('id',$id)->delete();
     //$media = Media::find($id);
     // $model = Model::find($media->id);
     //$media->deleteMedia($media->id);

     return redirect()->route('expenses.edit', ['expense' => $expense_id]);
   
   }


   public function deleteview(Request $request){

     $id = $request->id;
     $expense_id = $request->expense_id;
     Media::where('id',$id)->delete();
     //$media = Media::find($id);
     // $model = Model::find($media->id);
     //$media->deleteMedia($media->id);
     return redirect()->route('expenses.show', ['expense' => $expense_id]);

   }








}
