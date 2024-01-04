<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class AttendanceExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->start_date = $request->start_date;
        $this->end_date = $request->end_date;
    }

    public function collection()
    {
        return Attendance::with('users')->where(function($query) {
                        if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                        {
                            $query->whereIn('user_id', getUsersReportingToAuth());
                        }
                        if($this->start_date)
                        {
                            $query->whereDate('punchin_date','>=',$this->start_date);
                        }
                        if($this->end_date)
                        {
                            $query->whereDate('punchin_date','<=',$this->end_date);
                        }
                    })
                    ->select('id','user_id', 'punchin_date', 'punchin_time', 'punchin_address', 'punchout_date', 'punchout_time', 'punchout_address', 'worked_time','punchin_summary','punchout_summary','punchin_longitude','punchin_latitude','punchout_latitude','punchout_longitude','working_type')
                    ->limit(5000)->latest()->get();   
    }

    public function headings(): array
    {
        // return ['id','Employee Code','user_id','Designation','Branch','Division','punchin_date', 'punchin_time','punchout_time','worked_time','Working Type', 'punchin_address', 'punchout_date','punchout_address','punchin_summary','punchout_summary','punchin_longitude','punchin_latitude','punchout_latitude','punchout_longitude'];
        return ['id','Employee Code','user_id','Designation','Branch','Division','punchin_date', 'punchin_time','punchout_time','worked_time','Working Type', 'punchin_address','punchout_address','punchin_summary','punchin_longitude','punchin_latitude','punchout_longitude','punchout_latitude'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            isset($data['users']['employee_codes'])? $data['users']['employee_codes'] :'',
            isset($data['users']['name'])? $data['users']['name'] :'',
            isset($data['users']['getdesignation']['designation_name'])? $data['users']['getdesignation']['designation_name'] :'',
            isset($data['users']['getbranch']['branch_name'])? $data['users']['getbranch']['branch_name'] :'',
            isset($data['users']['getdepartment']['division_name'])? $data['users']['getdepartment']['division_name'] :'',

            $data['punchin_date'],
            $data['punchin_time'],
            isset($data['punchout_time']) ? $data['punchout_time'] :'21:00:00',
            $data['worked_time'],
            isset($data['working_type']) ? $data['working_type'] : '',

            $data['punchin_address'],
            //isset($data['punchout_date']) ? $data['punchout_date'] :$data['punchin_date'],
           
            $data['punchout_address'],
            $data['punchin_summary'],
            //$data['punchout_summary'],
            isset($data['punchin_longitude']) ? $data['punchin_longitude'] : '',
            isset($data['punchin_latitude']) ? $data['punchin_latitude'] : '',
            isset($data['punchout_longitude']) ? $data['punchout_longitude'] : '',
            isset($data['punchout_latitude']) ? $data['punchout_latitude'] : '',
            
          
          
           
        ];
    }

}