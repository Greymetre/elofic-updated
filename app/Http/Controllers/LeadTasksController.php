<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

use DataTables;
use Auth;

use App\Exports\ExcelExport;
use Excel;

use App\Models\Lead;
use App\Models\LeadTask;

class LeadTasksController extends Controller
{
    

  
    public function index(Request $request)
    {
        abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        return view('lead-tasks.index');
    }

    public function getLeadTasks(Request $request){
        $lead_tasks = LeadTask::with(['lead','assignUser']); 
        $lead_tasks = $lead_tasks->select(\DB::raw(with(new LeadTask)->getTable().'.*'))->groupBy('id');
        return DataTables::of($lead_tasks)
            ->editColumn('lead.company_name', function ($lead_task) {
                    return $lead_task->lead->company_name??'';
            })
            ->editColumn('description', function ($lead_task) {
               return $lead_task->description;
            })
            ->editColumn('date', function ($lead_task) {
               return date("M d,Y",strtotime($lead_task->date));
            })
            ->addColumn('action', function ($lead_task) {
               return "";
            })
            ->addColumn('assignUser.name', function ($lead_task) {
               return $lead_task->assignUser->name??'';
            })
            ->addColumn('checkbox', function ($lead_task) {
                 $lead_task_id = "'".$lead_task->id."'";
                return '<input type="checkbox" class="lead_task-checkbox checkbox_cls" value="'.$lead_task->id.'" name="lead_task_ids[]" onclick="checkboxDelete('.$lead_task_id.')">';
            })
           
            ->rawColumns(['action','checkbox'])
            ->make(true);
    }

    function exportTasks(Request $request){
        $filename = 'tasks.xlsx';

        $results_per_page = 8000;
        $page_number = intval($request->input('page_number'));
        $page_result = ($page_number-1) * $results_per_page;

        $lead_tasks = LeadTask::with(['lead']); 
        $lead_tasks = $lead_tasks->get();
        $data = $lead_tasks->map(function ($item, $key) {

            return [
                $item->id,
                $item->lead->company_name??'',
                $item->description,
                date("M d,Y",strtotime($item->date)),
                $item->assignUser->name??'',
                
               

            ];
        })->toArray();

        $export = new ExcelExport([
            'Id',
            'Name',
            'Description',
            'Date',
            'Assign to',
        ], $data);

        return Excel::download($export, $filename);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
    {
        $rules = [
            'lead_id'=>'required',
            'assigned_to'=>'required',
            'description'=>'required',
            'date'=>'required',
            //'time'=>'required',
            
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id(); 
        $task_id = $request->task_id;
        $lead_task = LeadTask::where(['id'=>$task_id])->first();
        if($lead_task){
             $lead_task->update(['assigned_to'=>$request->assigned_to,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'description'=>$request->description,'date'=>$request->date,'time'=>$request->time]);
            $request->session()->flash('message_success',__('Lead Task Update successfully.'));
        }else{
            $lead_note = LeadTask::create(['assigned_to'=>$request->assigned_to,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'description'=>$request->description,'date'=>$request->date,'time'=>$request->time]);
            $request->session()->flash('message_success',__('Lead Task Added successfully.'));
        }
       

        return redirect()->back();
    }

    


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, LeadTask $leadTask)
    // {
    //     $rules = [
    //         'lead_id'=>'required',
    //         'assigned_to'=>'required',
    //         'description'=>'required',
    //         'date'=>'required',
    //         'time'=>'required',
            
    //     ];

    //     $request->validate($rules);
    //     $data = $request->all();
    //     $created_by = Auth::id(); 
    //     $lead_task = LeadTask::where(['id'=>$leadTask->id])->first();
    //     if($lead_task){
    //         $lead_task->update(['assigned_to'=>$request->assigned_to,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'description'=>$request->description,'date'=>$request->date,'time'=>$request->time]);
    //          $request->session()->flash('message_success',__('Lead Task Added successfully.'));
    //     }else{
    //          $request->session()->flash('message_info',__('something went wrong.'));

    //     }
       
    //     return redirect()->back();
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, LeadTask $leadTask)
    {
        $leadTask->delete();
        $request->session()->flash('message_success',__('Lead Task deleted successfully.'));
        return redirect()->back();
    }

    public function checkboxAction(Request $request){
         $lead_ids = $request->lead_ids;
        $lead_id_arr = explode(",", $lead_ids);
        if(count($lead_id_arr)>0){
            LeadTask::whereIn('id',$lead_id_arr)->delete(); 
            $request->session()->flash('message_success',__('Lead Task deleted successfully.'));
            return redirect()->back();
        }
        
    }
}
