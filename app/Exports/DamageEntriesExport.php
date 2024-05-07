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
use App\Models\DamageEntry;
use App\Models\User;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DamageEntriesExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{

    private $rowIndex = 3;

    public function __construct($request)
    {    
        $this->status = $request->input('status');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
    }

    public function collection()
    {        

        // $data = SalesTargetUsers::with(['user'])->whereBetween('year', $f_year_array)->toSql();
        $data = DamageEntry::with(['createdbyname','scheme','customer'])->select('*'); 
        if($this->status != '' && !empty($this->status && $this->status != null)) {
            $data->where('status',$this->status);
        }

        // dd($data);

        // if($this->month == '' && empty($this->month)){
        //     $data->where(function ($query) use($f_year_array) {
        //         $query->where('year', '=', $f_year_array[0])
        //               ->where('month', '>=', 'Apr');
        //     })->orWhere(function ($query) use($f_year_array) {
        //         $query->where('year', '=', $f_year_array[1])
        //               ->where('month', '<=', 'Mar');
        //     });
        // }else {
        //    $data->where(function ($query) use($f_year_array) {
        //         $query->where('year', '=', $f_year_array[0])
        //               ->where('month', '>=', $this->month);
        //     })->orWhere(function ($query) use($f_year_array) {
        //         $query->where('year', '=', $f_year_array[1])
        //               ->where('month', '<=', $this->month);
        //     });
        // }
        
        $data = $data->orderBy('id')->get();

        return $data;
    }


    public function headings(): array
    {
     $headings = ['Firm Name', 'Contact Person', 'Parent Name', 'Mobile Number', 'Coupon Code','Status','Remark'];

     return $headings;
    }


    public function map($data): array
    {

        $response = array();
        $response[0] = $data['customer_id']??'';
        $response[1] = $data['coupon_code']??'';
        $response[2] = $data['point']??'';
        $response[3] = $data['scheme_id'] ?? '';
        $response[4] = isset($data['status']) ? ($data['status'] == 0 ? 'Rejected' : ($data['status'] == 1 ? 'Approved' : ($data['status'] == 2 ? 'Pending' : ''))) : '',

        $response[5] = $data['remark']??'';

        return $response;
    }

}
