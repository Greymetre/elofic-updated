<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpensesType;
use App\Models\Expenses;
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
        $expenses_type = ExpensesType::all();
        return response()->json(['status'=>'success', 'data'=>$expenses_type], 200); 
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
                $file = $request->file('expense_file');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $expenses->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('expense_file');
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
            $query = Expenses::with('media','expense_type')->where(['user_id'=>Auth::Id()]);
            $expenses = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->orderBy('id','desc')->get();
            if($expenses->isNotEmpty())
            {

                $datas = array();
                foreach($expenses as $expense){

                $image = '';  

                if(isset($expense) && $expense->getMedia('expense_file')->count() > 0 && file_exists($expense->getFirstMedia('expense_file')->getPath())){
                 $image = $expense->getFirstMedia('expense_file')->getFullUrl();
                }

                  $datas[] = array(
                'id' => $expense->id ?? "",
                'expenses_type' => $expense->expenses_type ?? "",
                'expenses_type_name' => $expense->expense_type->name?? "",
                'user_id' => $expense->user_id ?? "",
                'date' => $expense->date ?? "",
                'note' => $expense->note ?? "",
                'start_km' => $expense->start_km ?? "",
                'stop_km' => $expense->stop_km ?? "",
                'total_km' => $expense->total_km ?? "",
                'claim_amount' => $expense->claim_amount ?? "",
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


                $image = '';  
                if(isset($expense) && $expense->getMedia('expense_file')->count() > 0 && file_exists($expense->getFirstMedia('expense_file')->getPath())){
                 $image = $expense->getFirstMedia('expense_file')->getFullUrl();
                }




                $datas[] = array(
                'id' => $expense->id ?? "",
                'expenses_type' => $expense->expenses_type ?? "",
                'expenses_type_name' => $expense->expense_type->name?? "",
                'user_id' => $expense->user_id ?? "",
                'date' => $expense->date ?? "",
                'note' => $expense->note ?? "",
                'start_km' => $expense->start_km ?? "",
                'stop_km' => $expense->stop_km ?? "",
                'total_km' => $expense->total_km ?? "",
                'claim_amount' => $expense->claim_amount ?? "",
                // 'claim_amount' => '$'.number_format($expense->claim_amount ?? 0,2),
                'expense_image' =>  $image,
                   );
    
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
                'date' => isset($request['date'])? $request['date']:$expense_detail->date,
                'note' => isset($request['note'])? $request['note']:$expense_detail->note,
                'start_km' => isset($request['start_km'])? $request['start_km']:$expense_detail->start_km,
                'stop_km' => isset($request['stop_km'])? $request['stop_km']:$expense_detail->stop_km,
                'total_km' => isset($request['total_km'])? $request['total_km']:$expense_detail->total_km,
                'claim_amount' => isset($request['claim_amount'])? $request['claim_amount']:$expense_detail->claim_amount,
            ]))
            {
              if($request->hasFile('expense_file')){
                $file = $request->file('expense_file');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $expense_detail->addMedia($file)
                ->usingFileName($customname)
                ->toMediaCollection('expense_file');
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
