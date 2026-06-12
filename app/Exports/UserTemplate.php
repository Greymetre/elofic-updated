<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class UserTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return User::select('id','name', 'first_name', 'last_name', 'mobile', 'email', 'password','gender', 'profile_image')->limit(0)->get();   
    }

    public function headings(): array
    {
        // return ['id','User Name','Email'
        // // ,'Gender','User Code'
        // ,'Location','Employee Codes','Branch id'
        // // ,'Designation id','Division id','Department id'
        // ,'Payroll','Reportingid','role_ids'
        // // ,'Sales Type'
        // ,'Date Of Joining','Date Of Birth'
        // // ,'Last Year Increments','Last Year Increment Percent','Last promotion','CTC Annual','Gross Salary Monthly','Salary','last_year_increment_value','marital_status','father_name','father_date_of_birth','mother_name','mother_date_of_birth','marriage_anniversary','spouse_name','spouse_date_of_birth','children_one','children_one_date_of_birth','children_two','children_two_date_of_birth','children_three','children_three_date_of_birth','children_four','children_four_date_of_birth','children_five','children_five_date_of_birth','pan_number','aadhar_number','emergency_number','current_address','permanent_address','biometric_code','account_number','bank_name','ifsc_code','pf_number','un_number','esi_number','probation_period'
        // ,'date_of_confirmation'
        // // ,'notice_period'
        // ,'date_of_leaving'
        // // ,'current_company_tenture','previous_exp','total_exp',
        // ];
        return [
            // 'ID', 
            // 'Employees Code', 
            // 'User Name', 
            // 'Designation', 
            // 'Role', 
            // 'Zone Name', 
            // 'Base Location', 
            // 'Department', 
            // 'Division', 
            // 'Reporting To', 
            // 'Mobile', 
            // 'Email',
            // //  'Gender',
            // 'Date Of Joining', 
            // 'Date Of Birth', 
            // 'Grade', 
            // 'Designation code', 
            // 'Epmloyee Super Code', 
            // 'Travel Policy Name',
            // //    'Age', 'CTC Annual', 'Gross Salary Monthly', 'CTC Per Month', 'Last Year Increments', 'Last Year Increment Percent', 'Last Year Increments Value', 'Last Promotion', 'Marital Status', 'Father Name', 'Father Date Of Birth', 'Mother Name', 'Mother  DOB', 'Marriage Anniversary', 'Spouse name', 'Spouse Date Of Birth', 'Children-1', 'Children-1 DOB', 'Children-2', 'Children-2 DOB', 'Children-3', 'Children-3 DOB', 'Children-4', 'Children-4 DOB', 'Children-5', 'Children-5 DOB', 'PAN Number', 'Adhar Number', 'Emergency Number', 'Current Address', 'Permanent Address', 'Biometric Code', 'Account Number', 'Bank Name', 'IFSC Code', 'PF Number', 'UN Number', 'ESI Number', 'Probation Period',
            // 'Date of Confirmation',
            // //  'Notice Period',
            // 'Date of leaving',
            // 'Base Location Coordinates (latitude, longitude)',
            // 'Change name (Earned Leave (EL) Balance)',
            // 'Casual Leave (CL) Balance',
            // 'Sick Leave (SL) Balance',
            // 'Comp off Balance',
            // //    'High School', 'Higher Secondary', 'Graducation', 'Post Graducation', 'Other', 'Current Company TENURE', 'Previous Exp', 'Total Exp', 'Sales Type', 'Status', 'profile_image',
            // // 'designation_id', 'branch_id','primary_branch_id', 'division_id', 'department_id',
            // 'Reporting ID', 
            // 'Role Ids',
            // 'payroll',
            // 'designation_id',
            // 'branch_id',
            // 'division_id',
            // 'department_id',
            // 'Attandance Summary Report',
            // //   'warehouse_id', 'Attandance Summary Report', 'Order Mails', 'Order Mail Type', 'Order Mail Type ID',
            // //    'Leave Balance',  'Blood Group', 'Personal Number'

            'id',
            'employees_code',
            'user_name',
            'designation',
            'role',
            'zone_name',
            'base_location',
            'department',
            'division',
            'reporting_to',
            'mobile',
            'email',
            'date_of_joining',
            'date_of_birth',
            'grade',
            'designation_code',
            'employee_super_code',
            'travel_policy_name',
            'date_of_confirmation',
            'date_of_leaving',
            'base_location_coordinates_latitude_longitude',
            'earned_leave_el_balance',
            'casual_leave_cl_balance',
            'sick_leave_sl_balance',
            'comp_off_balance',
            'reporting_id',
            'role_ids',
            'payroll',
            'designation_id',
            'branch_id',
            'division_id',
            'department_id',
            'attandance_summary_report',
           ];
    }

}
