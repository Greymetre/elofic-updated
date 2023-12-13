<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
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


class CustomersExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->userid = !empty($request->input('executive_id')) ? $request->input('executive_id') : Auth::user()->id;

        
        $this->userids = getUsersReportingToAuth($this->userid);
    }

    public function collection()
    {
        return Customers::where(function ($query)  {
                                if($this->userids)

                                {
                                    $query->whereIn('executive_id', $this->userids);
                                }
                                if($this->startdate)
                                {
                                    $query->whereDate('created_at','>=',$this->startdate);
                                }
                                if($this->enddate)
                                {
                                    $query->whereDate('created_at','<=',$this->enddate);
                                }
                            })
                        ->select('id','name', 'first_name', 'last_name', 'mobile', 'email', 'latitude', 'longitude', 'customertype', 'created_at','created_by','executive_id','customer_code','contact_number','parent_id')
                        ->limit(5000)->latest()->get();   
    }

    public function headings(): array
    {
        // return ['Created Date','Customer ID','Customer Type','Created by','Firm Name', 'First Name', 'Last Name', 'Mobile', 'Email','Address', 'Gmap address','Pin Code','Zip Code','Market Place','City','District','State','Beat Name', 'Latitude', 'Longitude','GST No','Adhar No','Pan No','Other No','Shop Image', 'Employee Name', 'Grade', 'Visit Status','Contact number -2','Customer Code','Employee Code','Branch Name','Department','Designation','Parent Customer'];

    return ['Created Date','customer_id','Customer Type','Created by','firm_name', 'first_name', 'last_name', 'Mobile', 'email','address', 'Gmap address','Pin Code','Zip Code','market_place','City','District','State','Beat Name', 'Latitude', 'Longitude','gstin_no','aadhar_no','pan_no','other_no','Shop Image', 'Employee Name', 'grade','visit_status','contact_number2','customer_code','Employee Code','Branch Name','Department','Designation','Parent Customer','employee_id','parent_id','pincode_id','city_id','district_id','state_id','customer_type_id'];

    }

    public function map($data): array
    {
        // $data['gmap_address'] = UserActivity::where('customerid','=',$data['id'])->where('type','=','Counter Created')->pluck('address')->first();
        $data['gmap_address'] = UserActivity::where('customerid','=',$data['id'])->pluck('address')->first();
        return [
            $data['created_at'] = isset($data['created_at']) ? date("d-m-Y", strtotime($data['created_at'])) :'',
            $data['id'],
            isset($data['customertypes']['customertype_name']) ? $data['customertypes']['customertype_name'] :'',
            $data['createdbyname'] = isset($data['createdbyname']['name']) ? $data['createdbyname']['name'] : '',
            $data['name'],
            $data['first_name'],
            $data['last_name'],
            $data['mobile'],
            $data['email'],
            isset($data['customeraddress']['address1']) ? $data['customeraddress']['address1'] : '',
            $data['gmap_address'] = isset($data['gmap_address']) ? $data['gmap_address'] :'',
            $data['pincode_id'] = isset($data['customeraddress']['pincodename']['pincode']) ? $data['customeraddress']['pincodename']['pincode'] : '',
            isset($data['customeraddress']['zipcode']) ? $data['customeraddress']['zipcode'] : '',
            $data['landmark'] = isset($data['customeraddress']['landmark']) ? $data['customeraddress']['landmark'] : '',
            $data['city_name'] = isset($data['customeraddress']['cityname']['city_name']) ? $data['customeraddress']['cityname']['city_name'] : '',
            $data['district_name'] = isset($data['customeraddress']['districtname']['district_name']) ? $data['customeraddress']['districtname']['district_name'] :'',
            $data['state_name'] = isset($data['customeraddress']['statename']['state_name']) ? $data['customeraddress']['statename']['state_name'] :'',
            $data['beat_name'] = isset($data['beatdetails']['beats']['beat_name']) ? $data['beatdetails']['beats']['beat_name'] :'',
            $data['latitude'],
            $data['longitude'],
            isset($data['customerdetails']['gstin_no']) ? $data['customerdetails']['gstin_no'] :'',
            isset($data['customerdetails']['aadhar_no']) ? $data['customerdetails']['aadhar_no'] : '',
            isset($data['customerdetails']['pan_no']) ? $data['customerdetails']['pan_no'] :'',
            isset($data['customerdetails']['otherid_no']) ? $data['customerdetails']['otherid_no'] :'' ,
            $data['shop_image'] = isset($data['profile_image']) ? $data['profile_image'] :'',
            // $data['status_name'] = isset($data['statusname']['status_name']) ? $data['statusname']['status_name'] : '',
            $data['employee_name'] = isset($data['employeename']['name']) ? $data['employeename']['name'] : '',
            isset($data['customerdetails']['grade']) ? $data['customerdetails']['grade'] :'',
            isset($data['customerdetails']['visit_status']) ? $data['customerdetails']['visit_status'] : '',
            $data['contact_number'],
            $data['customer_code'],   
            isset($data['userdetails']['employee_codes']) ? $data['userdetails']['employee_codes'] : '',
            isset($data['userdetails']['getbranch']['branch_name']) ? $data['userdetails']['getbranch']['branch_name'] : '',
            isset($data['userdetails']['getdepartment']['division_name']) ? $data['userdetails']['getdepartment']['division_name'] : '',
            isset($data['userdetails']['getdesignation']['designation_name']) ? $data['userdetails']['getdesignation']['designation_name'] : '',
             $data->parentdetail->first_name??'',
             $data['executive_id']??NULL,
             $data['parent_id']??NULL,
             isset($data['customeraddress']['pincode_id']) ? $data['customeraddress']['pincode_id'] : '',
             isset($data['customeraddress']['city_id']) ? $data['customeraddress']['city_id'] : '',
             isset($data['customeraddress']['district_id']) ? $data['customeraddress']['district_id'] : '',
             isset($data['customeraddress']['state_id']) ? $data['customeraddress']['state_id'] : '',
             $data['customertype'],
        ];
    }

}