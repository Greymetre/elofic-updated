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
