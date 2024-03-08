<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpensesType;
use App\Models\Expenses;
use App\Models\Media;
use Validator;
use Auth;

use Illuminate\Http\Request;

class ExpensesTypeController extends Controller
{

    public function __construct()
    {
        
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }


    public function getExpensesType(Request $request)
    {
        // $expenses_type = ExpensesType::all();
        // return response()->json(['status'=>'success', 'data'=>$expenses_type], 200); 

        try
        { 
        
            $validator = Validator::make($request->all(), [
                'payroll_id'  => "required",
            ]);
            if($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }

            $payroll_id = $request->payroll_id;
            $expense_types = ExpensesType::where('payroll_id',$payroll_id)->get();
            if(!empty($expense_types))
            {

                $datas = array();
                foreach($expense_types as $expense_type){
                $datas[] = array(
                'id' => $expense_type->id ?? "",
                'name' => $expense_type->name ?? "",
                'rate' => $expense_type->rate?? "",
                'allowance_type_id' => $expense_type->allowance_type_id ?? "",
                'payroll_id' => $expense_type->payroll_id ?? "",
                   );

                 }
    
                return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $datas ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $expense ],200);  
            
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        } 


    }



    public function createExpense(Request $request){
      try
        { 
            $userid = $request->user()->id;
            $validator = Validator::make($request->all(), [
               // 'customer_id'   => 'nullable|exists:customers,id',
                'expenses_type'  => "required",
                'claim_amount'  => "required",
                'date'  => "required",
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }
            if($expenses = Expenses::create([
                'user_id' => $userid,
                'expenses_type' => isset($request->expenses_type) ? $request->expenses_type :null,
                'date' => isset($request->date) ? $request->date :null,
                'claim_amount' => isset($request->claim_amount) ? $request->claim_amount :null,
                'start_km' => isset($request->start_km) ? $request->start_km :null,
                'stop_km' => isset($request->stop_km) ? $request->stop_km :null,
                'total_km' => isset($request->total_km) ? $request->total_km :null,
                'note' => isset($request->note) ? $request->note :null,
                'created_by' => $userid,
                'created_at' => date('Y-m-d H:i:s')
            ]))
            {
                if($request->hasFile('expense_file')){
                $files = $request->file('expense_file');
                foreach($files as $file){
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $expenses->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('expense_file');

                }
               }

                return response()->json(['status' => 'success','message' => 'Data inserted successfully.','data' => $expenses ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'Error in No Record Found.'],200); 
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        } 

    }


    public function expenseListing(Request $request){
        try
        { 
            $pageSize = $request->input('pageSize');
            $query = Expenses::with('media','expense_type')->where(['user_id'=>Auth::Id()])->orderBy('id','desc');
            //$expenses = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $expenses = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->paginate(100);

            if($expenses->isNotEmpty())
            {

                $datas = array();
                foreach($expenses as $expense){

                // $image = '';  
                // if(isset($expense) && $expense->getMedia('expense_file')->count() > 0 && file_exists($expense->getFirstMedia('expense_file')->getPath())){
                //  $image = $expense->getFirstMedia('expense_file')->getFullUrl();
                // }

                $image = array();
             
                if(isset($expense) && $expense->getMedia('expense_file')->count() > 0 && file_exists($expense->getFirstMedia('expense_file')->getPath())){
                    foreach($expense->getMedia('expense_file') as $expense_image){
                   $image[] = $expense_image->getFullUrl();
                 
                   }
                }    



              
                if($expense->checker_status == '1'){
                 $exp_status = 'Approved';
                }elseif($expense->checker_status == '2'){
                 $exp_status = 'Rejected';
                }else{
                 $exp_status = 'Pending';
                }


                  $datas[] = array(
                'id' => $expense->id ?? "",
                'expenses_type' => $expense->expenses_type ?? "",
                'expenses_type_name' => $expense->expense_type->name?? "",
                'user_id' => $expense->user_id ?? "",
                'date' => date("d-m-Y", strtotime($expense->date)),
                'note' => $expense->note ?? "",
                'start_km' => $expense->start_km ?? "",
                'stop_km' => $expense->stop_km ?? "",
                'total_km' => $expense->total_km ?? "",
                'claim_amount' => $expense->claim_amount ?? "",
                'status' => $exp_status,
                // 'claim_amount' => '$'.number_format($expense->claim_amount ?? 0,2),
                //'expense_image' =>  $expense->getFirstMedia('expense_file')->getFullUrl(),
                 'expense_image' =>  $image,
                
                   );
                   }    

                return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $datas ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $expenses ],200);  
            
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }

    }

    public function expenseDetails(Request $request){
        try
        { 
            $userid = $request->user()->id;
            $validator = Validator::make($request->all(), [
                'expense_id'  => "required",
            ]);
            if($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }

            $expense_id = $request->expense_id;
            $expense = Expenses::with('media','expense_type')->where(['user_id'=>Auth::Id()])->where('id',$expense_id)->first();
            if(!empty($expense))
            {

                 $image = array();
                 $image_id = array();
                if(isset($expense) && $expense->getMedia('expense_file')->count() > 0 && file_exists($expense->getFirstMedia('expense_file')->getPath())){
                    foreach($expense->getMedia('expense_file') as $expense_image){
                   $image[] = $expense_image->getFullUrl();
                   $image_id[] = $expense_image->id;
                   
                   }

                }


                if($expense->checker_status == '1'){
                 $exp_status = 'Approved';
                }elseif($expense->checker_status == '2'){
                 $exp_status = 'Rejected';
                }else{
                 $exp_status = 'Pending';
                }

                // $datas[] = array(
                // 'id' => $expense->id ?? "",
                // 'expenses_type' => $expense->expenses_type ?? "",
                // 'expenses_type_name' => $expense->expense_type->name?? "",
                // 'user_id' => $expense->user_id ?? "",
                // 'date' => date("d-m-Y", strtotime($expense->date)),
                // 'note' => $expense->note ?? "",
                // 'start_km' => $expense->start_km ?? "",
                // 'stop_km' => $expense->stop_km ?? "",
                // 'total_km' => $expense->total_km ?? "",
                // 'claim_amount' => $expense->claim_amount ?? "",
                // 'approve_amount' => $expense->approve_amount ?? "",
                // 'status' => $exp_status,
                // 'expense_image' =>  $image,
                //    );

                $datas = array();
                $datas['id'] = $expense->id ?? "";
                $datas['expenses_type'] = $expense->expenses_type ?? "";
                $datas['expenses_type_name'] = $expense->expense_type->name?? "";
                $datas['rate'] = $expense->expense_type->rate?? "";
                $datas['allowance_type_id'] = $expense->expense_type->allowance_type_id?? "";
                $datas['user_id'] = $expense->user_id ?? "";
                $datas['date'] = date("d-m-Y", strtotime($expense->date));
                $datas['note'] = $expense->note ?? "";
                $datas['start_km'] = $expense->start_km ?? "";
                $datas['stop_km'] = $expense->stop_km ?? "";
                $datas['total_km'] = $expense->total_km ?? "";
                $datas['claim_amount'] = $expense->claim_amount ?? "";
                $datas['expense_image'] = $image;
                $datas['image_id'] = $image_id;
              
                $datas['approve_amount'] = $expense->approve_amount ?? "";
                $datas['status'] = $exp_status;

    
                return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $datas ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $expense ],200);  
            
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        } 

    }


   public function updateExpense(Request $request){
        try
        { 
            $userid = $request->user()->id;
            $validator = Validator::make($request->all(), [
                'expense_id'  => "required",
            ]);
            if($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }

            $expense_detail = Expenses::where('id',$request['expense_id'])->first();
            if($expenses = Expenses::where('id', $request['expense_id'])->update([
                'user_id' => $userid,
                'expenses_type' => isset($request['expenses_type'])? $request['expenses_type']:$expense_detail->expenses_type,
                //'date' => isset($request['date'])? $request['date']:$expense_detail->date,
                'note' => isset($request['note'])? $request['note']:$expense_detail->note,
                'start_km' => isset($request['start_km'])? $request['start_km']:$expense_detail->start_km,
                'stop_km' => isset($request['stop_km'])? $request['stop_km']:$expense_detail->stop_km,
                'total_km' => isset($request['total_km'])? $request['total_km']:$expense_detail->total_km,
                'claim_amount' => isset($request['claim_amount'])? $request['claim_amount']:$expense_detail->claim_amount,
            ]))
            {
              if($request->hasFile('expense_file')){
                $files = $request->file('expense_file');

                //$expense_detail->clearMediaCollection('expense_file');

                foreach($files as $file){
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $expense_detail->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('expense_file');

                }
                }


              if(!empty($request->image_id)){
                Media::where('id',$request->image_id)->delete();
               } 

            return response()->json(['status' => 'success','message' => 'Data updated successfully.'], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Updated.'],200);  
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }  


   } 






}
