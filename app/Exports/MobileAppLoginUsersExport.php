<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use App\Models\MobileUserLoginDetails;
use App\Models\User;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MobileAppLoginUsersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{

    private $rowIndex = 3;

    public function __construct($request)
    {
        $this->user_id = $request->input('user');
        $this->month = $request->input('month');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
        $this->financial_year = $request->input('financial_year');
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);

        $data = MobileUserLoginDetails::with(['customer']);

        if ($this->start_date && !empty($this->start_date) && $this->end_date && !empty($this->end_date)) {
            $startDate = date('Y-m-d', strtotime($this->start_date));
            $endDate = date('Y-m-d', strtotime($this->end_date));
            $data->whereDate('first_login_date', '>=', $startDate)
                ->whereDate('first_login_date', '<=', $endDate);
        }

        $data = $data->get();

        return $data;
    }


    public function headings(): array
    {

        $headings = ['S. No.', 'Customer ID', 'Firm Name', 'Contact Person', 'Mobile Number', 'Branch', 'State', 'District', 'City', 'App Version', 'Device Type', 'Device Name', 'First Login date', 'Last Login date', 'Login Status'];



        return $headings;
    }

    public function map($data): array
    {
        // dd($data);
        $branch_arr = array();
        if ($data->customer->getemployeedetail && !empty($data->customer->getemployeedetail) && count($data->customer->getemployeedetail) > 0) {
            foreach ($data->customer->getemployeedetail as $key_new => $datas) {
                if (isset($datas->employee_detail->getbranch->branch_name) && !in_array($datas->employee_detail->getbranch->branch_name, $branch_arr)) {
                    $branch_arr[] = $datas->employee_detail->getbranch->branch_name;
                }
            }
        }
        return [
            $data['id'],
            $data['customer']['id'],
            isset($data['customer']['name']) ? $data['customer']['name'] : '',
            isset($data['customer']['first_name']) ? $data['customer']['first_name'] : '',
            isset($data['customer']['mobile']) ? $data['customer']['mobile'] : '',
            implode(',', $branch_arr),
            isset($data['customer']['customeraddress']) ? ($data['customer']['customeraddress']['statename']?$data['customer']['customeraddress']['statename']['state_name']:'') : '',
            isset($data['customer']['customeraddress']) ? ($data['customer']['customeraddress']['districtname']?$data['customer']['customeraddress']['districtname']['district_name']:'') : '',
            isset($data['customer']['customeraddress']) ? ($data['customer']['customeraddress']['cityname']?$data['customer']['customeraddress']['cityname']['city_name']:'') : '',
            isset($data['app_version']) ? $data['app_version'] : '',
            isset($data['device_type']) ? $data['device_type'] : '',
            isset($data['device_name']) ? $data['device_name'] : '',
            isset($data['first_login_date']) ? date('Y-m-d', strtotime($data['first_login_date'])) : '',
            isset($data['last_login_date']) ? date('Y-m-d', strtotime($data['last_login_date'])) : '',
            isset($data['login_status']) ? ($data['login_status'] == '0' ? 'Logout' : 'Login') : '',

        ];
    }
}
