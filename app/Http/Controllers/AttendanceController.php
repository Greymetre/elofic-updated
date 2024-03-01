<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;
use Validator;
use Gate;
use App\Models\TourProgramme;
use App\DataTables\AttendancesDataTable;
use App\Exports\AttendanceExport;
use Carbon\Carbon;
use Excel;
use App\Models\BeatSchedule;
use Illuminate\Support\Facades\Storage;
use File;

use App\Models\Attachment;
use App\Models\User;
use App\Models\Holiday;

use App\Exports\ExcelExport;
use DateTime;
use DatePeriod;
use DateInterval;

class AttendanceController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->attendance = new Attendance();
        
    }

    public function index(AttendancesDataTable $dataTable)
    {
        //abort_if(Gate::denies('attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pinchinimages = Attachment::where('file_path', '<>', '')->pluck('file_path');
        $path = public_path();
        //$files = File::allFiles($path);
        foreach ($pinchinimages as $key => $image) {
            if(File::exists($path.'/'.$image)){
                File::move($path.'/'.$image, $path.'/finals/'.$image);
            }
        }
        // foreach ($pinchinimages as $key => $value) {
        //     $newpath = str_ireplace('attendances', 'final', $value);
        //     Storage::move($value, $newpath);

        //     // if(File::exists($value)){
        //     //     File::move(public_path($value), public_path($newpath));
        //     // }           
        // }
        
        return $dataTable->render('attendances.index');
    }

    public function attendancesInfo(Request $request)
    {
        if ($request->ajax()) {
            $data = Attendance::where(function ($query) use ($request) {
                                if(!empty($request['user_id']))
                                {
                                    $query->where('user_id', $request['user_id']);
                                }
                            })
                            ->latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('punchin_date', function($data)
                    {
                        return isset($data->punchin_date) ? showdateformat($data->punchin_date) : '';
                    })
                    ->editColumn('worked_time', function($data)
                    {
                        return isset($data->worked_time) ? $data->worked_time : '';
                    })
                    ->make(true);
        }
    }

    public function download(Request $request)
    {
        ////abort_if(Gate::denies('visitreport_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new AttendanceExport($request), 'attendancereports.xlsx');
    }



    public function attendanceSummaryDownload(Request $request){

        $filename = 'attendance-summary-report.xlsx';
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $executive_id = $request->executive_id;
        $end_date = date('Y-m-d',strtotime($end_date . "+1 days"));

        $period = new DatePeriod(
           new DateTime($start_date),
           new DateInterval('P1D'),
           new DateTime($end_date)
          );



         /// get all user

      
        $attendancesummary = User::with(['attendance_details','createdbyname','getbranch'])->where('active','Y');
        if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
            {
              $attendancesummary = $attendancesummary->whereIn('id', getUsersReportingToAuth());
            }

        if($executive_id)
          {
            $attendancesummary = $attendancesummary->where('id', $executive_id);
          } 

        $attendancesummary = $attendancesummary->get();       


        ///



    
        // $attendancesummary = Attendance::with(['users']);
        // if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
        //     {
        //       $attendancesummary = $attendancesummary->whereIn('user_id', getUsersReportingToAuth());
        //     }
        // if($start_date)
        //     {
        //         $attendancesummary = $attendancesummary->whereDate('punchin_date','>=',$start_date);
        //     }
        // if($end_date)
        // {
        //    $attendancesummary = $attendancesummary->whereDate('punchin_date','<=',$end_date);
        // }

        // if($executive_id)
        // {
        //     $attendancesummary = $attendancesummary->where('user_id', $executive_id);
        // } 

        // $attendancesummary = $attendancesummary->get()->unique('user_id');  

       

         $date1 = $start_date;
         $date2 = $end_date;

        
         $label2 =[];

        //new

        foreach ($period as $key => $value) {

          $label2[]=  $value->format('j-M-Y');
          $like_date=  $value->format('j-M-Y');  

        }

        //new


        // $label2[] ="TGT";

        $label2[] ="";

        $data = $attendancesummary->map(function ($item, $key) use ($label2 ,$date1,$date2,$period){
                            
        
                //neww
                   $label_data=[];

                 foreach ($period as $key => $value) {
                  $like_date =  $value->format('j-M-Y'); 

                   $check = $value->format('Y-m-d');

                //last new

                  $attendance_details = Attendance::where(['user_id'=>$item->id])->where('punchin_date','like',$check.'%')->first();


                 ///nnn

                     $userId = $item->id;
                     $branchId = $item->branch_id;
                     $holiday_detail = Holiday::where('branch',$branchId)->first();
                     $hoday_dates = $holiday_detail->holiday_date??'';
                     $check_date_attendance  = explode(',', $hoday_dates);

                     if(in_array($check,$check_date_attendance)){
                       $label_data[] = 'Holiday';

                     }else{


                 ///nnn  
 
                  if(!empty($attendance_details)){

                    if($attendance_details->attendance_status == '1'){
                        if($attendance_details->working_type == 'Leave'){
                         $label_data[] =  'L';
                        }elseif($attendance_details->working_type == 'Local Market Visit'){
                          $label_data[] =  'P';
                        }
                        elseif($attendance_details->working_type == 'Office Work'){
                          $label_data[] = 'P';
                        }elseif($attendance_details->working_type == 'Plumber Meet'){
                         $label_data[] = 'P';
                        }elseif($attendance_details->working_type == 'Retailer Meet'){
                          $label_data[] = 'P';
                        }elseif($attendance_details->working_type == 'Service Center Visit'){
                           $label_data[] = 'P';
                        }
                        elseif($attendance_details->working_type =='Tour'){
                           $label_data[] = 'P';
                        }
                        // else{
                        //   $label_data[]='W/o';
                        // } 

                    }
                    else{
                        $label_data[]='A';
                    }
                    }else{
                       
                        $dayname = date('l', strtotime($check));
                        if($dayname == 'Sunday'){
                         $label_data[]='W/o';
                        }else{
                         $label_data[]='MIS';
                        }
                        
                    }   


                ///last end new   

                 } 

 
                }

             
               //neww

                $label_data[]="";

            $return =  [
                $item->id??'',
                $item->employee_codes??'',
                $item->name??'',
                $item->getbranch->branch_name??'',
                $item->getdivision->division_name??'', 
                $item->getdesignation->designation_name??'', 
            ];

           return  $option_array = array_merge($return, $label_data);
            
        })->toArray();


        $label1 = [
            'User Id',
            'Employee Code',
            'User Name',
            'Branch',
            'Division',
            'Designation',  
            
        ];

          $label = array_merge($label1, $label2);

         $export = new ExcelExport($label, $data);

        return Excel::download($export, $filename);

    
    }









    public function submitAttendances(Request $request)
    {
        try
        { 
            if(Attendance::updateOrCreate(['user_id' => $request['user_id'] , 'punchin_date' => date('Y-m-d',strtotime($request['punchin_date']))],[
                'user_id' => $request['user_id'],
                'punchin_date' => date('Y-m-d',strtotime($request['punchin_date'])),
                'punchin_time' => date('G:i',strtotime($request['punchin_date'])),
                'punchout_date' => date('Y-m-d',strtotime($request['punchout_date'])),
                'punchout_time' => date('G:i',strtotime($request->punchout_date)),
                'worked_time' => date("H:i",strtotime($request['punchout_date']) - strtotime($request['punchin_date'])),
                'working_type' => isset($request['working_type']) ? $request['working_type'] :'fields',
                'created_at' => getcurentDateTime(),
                'updated_at' => getcurentDateTime(),
            ]))
            {
              return Redirect::to('reports/attendancereport')->with('message_success', 'PunchIn Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Lead Stages')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function removePunchout(Request $request)
    {
        try
        { 
            if(Attendance::where('id','=',$request['id'])->whereDate('punchin_date','=',date('Y-m-d'))->update([
                'punchout_date' => null,
                'punchout_time' => null,
                'punchout_latitude' => null,
                'punchout_longitude' => null,
                'punchout_address' => '',
                'punchout_image' => '',
                'punchout_summary' => '',
                'worked_time' => '',
                'updated_at' => getcurentDateTime(),
            ]))
            {
              return redirect()->back()->with('message_success', 'Punchout Remeoved Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Punchout Remeoved')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
    public function destroy($id)
    {
        ////abort_if(Gate::denies('customer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try
        { 
            $attendance = Attendance::whereDate('punchin_date','=',date('Y-m-d'))->where('id','=',$id)->first();
            TourProgramme::whereDate('date','=',date('Y-m-d'))->where('userid','=',$attendance['user_id'])->update(['type' => '']);
            BeatSchedule::whereDate('beat_date','=',date('Y-m-d'))->where('user_id','=',$attendance['user_id'])->delete();
            if($attendance->delete())
            {
                return response()->json(['status' => 'success','message' => 'Attendance deleted successfully!']);
            }
            return response()->json(['status' => 'error','message' => 'Error in Attendance Delete!']);
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function approveAttendance(Request $request)
    { 
        try
        { 
            if(Attendance::where('id','=',$request['id'])->update([
                'attendance_status' => 1,
                'remark_status' => null
            ]))
            {
              return redirect()->back()->with('message_success', 'Attendance Approved Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Attendance Approved')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }


    public function rejectAttendance(Request $request)
    {
        $remark_status  = $request['remark_status']??null;
        try
        { 
            if(Attendance::where('id','=',$request['attendance_id'])->update([
                'attendance_status' => 2,
                'remark_status' => $remark_status??null,
            ]))
            {
              return Redirect::to('reports/attendancereport')->with('message_success', 'Attendance Rejected Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Attendance Rejected')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }



}
