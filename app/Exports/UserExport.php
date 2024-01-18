<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Branch;
use App\Models\Division;
use App\Models\Designation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class UserExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return User::with(['reportinginfo','userinfo'])->where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('id', $this->userids);
                                }
                            })->select('id','name', 'first_name', 'last_name', 'mobile', 'email', 'gender', 'profile_image','reportingid','employee_codes','branch_id','department_id','designation_id','active')->latest()->get();   
    }

    public function headings(): array
    {
        // return ['id','name', 'first_name', 'last_name', 'mobile', 'email', 'gender', 'profile_image', 'role', 'Reporting','Employees Code','Branch Name','Division','Designation','Status'];

        return ['id','employees_code','name','Designation','Branch Name','Division','Reporting','mobile', 'email', 'gender', 'Status','profile_image', 'role','designation_id','branch_id','division_id','ctc','date_of_joining','last_year_increments','last_promotion'];
    }

    public function map($data): array
    {
         
           if($data['active'] =='Y'){
                $status = 'ACTIVE';
            }else{
                $status = 'INACTIVE';
            }

        return [
            $data['id'],
            $data['employee_codes'],
            $data['name'],
            isset($data['getdesignation']['designation_name']) ? $data['getdesignation']['designation_name'] :'',
            isset($data['getbranch']['branch_name']) ? $data['getbranch']['branch_name'] :'',
            isset($data['getdepartment']['division_name']) ? $data['getdepartment']['division_name'] :'',
            isset($data['reportinginfo']['name']) ? $data['reportinginfo']['name'] :'',
            //$data['first_name'],
            //$data['last_name'],
            $data['mobile'],
            $data['email'],
            $data['gender'],
            //$data['active'],
            $status,
            $data['profile_image'],
            //isset($data['roles']['name']) ? $data['roles']['name'] :'',
            str_replace(str_split('[/]/"'), '', $data->getRoleNames()),
            $data['designation_id']??NULL,
            $data['branch_id']??NULL,
            $data['department_id']??NULL,
            isset($data['userinfo']['salary']) ? $data['userinfo']['salary'] :'',
            isset($data['userinfo']['date_of_joining']) ? $data['userinfo']['date_of_joining'] :'',
            isset($data['userinfo']['last_year_increments']) ? $data['userinfo']['last_year_increments'] :'',
            isset($data['userinfo']['last_promotion']) ? $data['userinfo']['last_promotion'] :'',
                       
        ];
    }
}
