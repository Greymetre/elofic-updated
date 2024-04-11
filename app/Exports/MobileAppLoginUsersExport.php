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

class MobileAppLoginUsersExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{

    private $rowIndex = 3;

    public function __construct($request)
    {    
        $this->user_id = $request->input('user');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);
        
        $data = MobileUserLoginDetails::with(['customer'])->get();

        return $data;
    }


    public function headings(): array
    {

     $headings = ['S. No.','Firm Name', 'Contact Person', 'Mobile Number', 'App Version', 'Device Type','Device Name','First Login date','Last Login date','Login Status'];



       return $headings;
   }

   public function map($data): array
   {
   	// dd($data);
       return [
           $data['id'],
           isset($data['customer']['name']) ? $data['customer']['name'] :'',
           isset($data['customer']['first_name']) ? $data['customer']['first_name'] :'',
           isset($data['customer']['mobile']) ? $data['customer']['mobile'] :'',
           isset($data['app_version']) ? $data['app_version'] :'',
           isset($data['device_type']) ? $data['device_type'] :'',
           isset($data['device_name']) ? $data['device_name'] :'',
           isset($data['first_login_date']) ? date('Y-m-d', strtotime($data['first_login_date'])) :'',
           isset($data['last_login_date']) ? date('Y-m-d', strtotime($data['last_login_date'])) :'',
           isset($data['login_status']) ? $data['login_status'] :'',
           
       ];
   }

}
