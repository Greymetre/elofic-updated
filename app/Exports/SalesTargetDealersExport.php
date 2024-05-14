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
use App\Models\SalesTargetUsers;
use App\Models\SalesTargetCustomers;
use App\Models\User;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesTargetDealersExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping,WithStyles
{

    private $rowIndex = 3;

    public function __construct($request)
    {   
        // dd($request->all()); 
        $this->user_id = $request->input('user');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
        $this->target = $request->input('target');  
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);
        $month = $this->month;
        

        $data = SalesTargetCustomers::with(['customer','customer.userdetails'])->select([
         DB::raw('GROUP_CONCAT(target) as targets'),
         DB::raw('GROUP_CONCAT(achievement) as achievements'),
         DB::raw('GROUP_CONCAT(month) as months'),  
         DB::raw('GROUP_CONCAT(year) as years'),
         DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
         DB::raw('customer_id'),
         DB::raw('type'),
        ]); 

        // $data = SalesTargetUsers::with(['user','user.getdesignation','user.getdivision','user.getbranch'])->select([
        //  DB::raw('GROUP_CONCAT(target) as targets'),
        //  DB::raw('GROUP_CONCAT(achievement) as achievements'),
        //  DB::raw('GROUP_CONCAT(month) as months'),  
        //  DB::raw('GROUP_CONCAT(year) as years'),
        //  DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
        //  DB::raw('user_id'),
        // ]); 

        // dd($data);

        if($this->month == '' && empty($this->month)){
            $data->where(function ($query) use($f_year_array) {
                $query->where('year', '=', $f_year_array[0])
                ->where('month', '>=', 'Apr');
            })->orWhere(function ($query) use($f_year_array) {
                $query->where('year', '=', $f_year_array[1])
                ->where('month', '<=', 'Mar');
            });
        }else {
            if($this->month != '' && !empty($this->month)){
                if($this->month == 'Jan' || $this->month == 'Feb' || $this->month == 'Mar') {
                    $data->where(function ($query) use($f_year_array,$month) {
                       $query->where('year', '=', $f_year_array[1])
                       ->where('month', '=', $month);
                   });
                }else{
                    $data->where(function ($query) use($f_year_array,$month) {
                       $query->where('year', '=', $f_year_array[0])
                       ->where('month', '=', $month);
                   });
                }
            }else{
                $data->where(function ($query) use($f_year_array) {
                   $query->where('year', '=', $f_year_array[0])
                   ->where('month', '>=', $this->month);
               })->orWhere(function ($query) use($f_year_array) {
                   $query->where('year', '=', $f_year_array[1])
                   ->where('month', '<=', $this->month);
               });
           } 
       }
        
        $data = $data->groupBy('customer_id')->orderBy('month')->get();

        // dd($data);

        return $data;
    }


    public function headings(): array
    {
     $f_year_array = explode('-', $this->financial_year);

     $startYear = $f_year_array[0];

     $endYear = $f_year_array[1];

     $headings = ['Dealer id', 'Dealer Distributor Name', 'Firm Name', 'City', 'Branch', 'Division', 'Sales Type'];

     $quarterNames = ['Q1', 'Q2', 'Q3', 'Q4'];

     $quarterIndex = 0;

     for ($year = $startYear; $year <= $endYear; $year++) {

        // if($this->month){
        //     $startMonth = date('m',strtotime($this->month));
        //     $endMonth =date('m',strtotime($this->month));
        // }else{
        //     $startMonth = ($year == $startYear) ? 4 : 1;
        //     $endMonth = ($year == $endYear) ? 3 : 12;
        // }
         $startMonth = ($year == $startYear) ? 4 : 1;
         $endMonth = ($year == $endYear) ? 3 : 12;


         for ($month = $startMonth; $month <= $endMonth; $month++) {

               $formattedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
               $headings[] = "$formattedMonth/$year";
               $headings[] = "";
               $headings[] = "";

               if($month == '06' || $month == '09' || $month == '12' || $month == '03' ) {
                   $headings[] = $quarterNames[$quarterIndex];
                   $quarterIndex++;
                   $headings[] = "";
                   $headings[] = "";
               }

           }
       }

       $headings[] = 'Total';

       $sub_headings = ['','','','','','','','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%'];

       $final_heading = [$headings, $sub_headings];

       return $final_heading;
   }


   public function map($data): array
   {
    $response = array();
    $first_name = !empty($data['customer']['first_name']) ? $data['customer']['first_name'] : '';
    $last_name = !empty($data['customer']['last_name']) ? $data['customer']['last_name'] : '';
    
    $dealer_name =  $first_name.' '.$last_name;
    $response[0] = $data['customer']['id'] ?? '';
    $response[1] = $dealer_name;
    $response[2] = $data['customer']['name'];
    $response[3] = $data['customer']['customeraddress']['cityname']['city_name'] ?? '';
    $response[4] = $data['customer']['userdetails']['getbranch']['branch_name'] ?? '';
    $response[5] = $data['customer']['userdetails']['getdivision']['division_name']??'';
    $response[6] = $data['type']??'';
    $f_year_array = explode('-', $this->financial_year);
    $data['months'] = explode(',', $data['months']);
    $data['targets'] = explode(',', $data['targets']);
    $data['achievements'] = explode(',', $data['achievements']);
    $data['achievement_percents'] = explode(',', $data['achievement_percents']);


    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']); 

        if($month == 'Apr' && $f_year_array[0] == $year[$key]) {
            $response[7] = $data['targets'][$key];
            $response[8] = $data['achievements'][$key]??'';
            if(isset($response[7]) && isset($response[8]) && !empty($response[8]) && !empty($response[7])) {
                $achievementPercent = ($response[7] == 0) ? 0 : ($response[8] * 100 / $response[7]);
            }else{
                $achievementPercent = '';
            }   
            $response[9] = $achievementPercent;
        }
        else{
            if(!isset($response[7])) {
                $response[7] = '';
            }
            if(!isset($response[8])) {
                $response[8] = '';
            }
            if(!isset($response[9])) {
                $response[9] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'May' && $f_year_array[0] == $year[$key]) {
            $response[10] = $data['targets'][$key];
            $response[11] = $data['achievements'][$key]??'';
            if(isset($response[10]) && isset($response[11]) && !empty($response[11]) && !empty($response[10])) {
                $achievementPercent = ($response[10] == 0) ? 0 : ($response[11] * 100 / $response[10]);
            }else{
                $achievementPercent = '';
            }
            $response[12] = $achievementPercent;
        }
        else{
            if(!isset($response[10])) {
                $response[10] = '';
            }
            if(!isset($response[11])) {
                $response[11] = '';
            }
            if(!isset($response[12])) {
                $response[12] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Jun' && $f_year_array[0] == $year[$key]) {
            $response[13] = $data['targets'][$key];
            $response[14] = $data['achievements'][$key]??'';
            if(isset($response[13]) && isset($response[14]) && !empty($response[14]) && !empty($response[13])) {
                $achievementPercent = ($response[13] == 0) ? 0 : ($response[14] * 100 / $response[13]);
            }else{
                $achievementPercent = '';
            }
            $response[15] = $achievementPercent;
        }else{
            if(!isset($response[13])) {
                $response[13] = '';
            }
            if(!isset($response[14])) {
                $response[14] = '';
            }
            if(!isset($response[15])) {
                $response[15] = '';
            }
        }
    }

    $response[17] = '=H'.$this->rowIndex.' + K'.$this->rowIndex.' + N'.$this->rowIndex;
    $response[18] = '=I'.$this->rowIndex.' + L'.$this->rowIndex.' + O'.$this->rowIndex;
    $response[19] = '=(J'.$this->rowIndex.' + M'.$this->rowIndex.' + P'.$this->rowIndex.') / 3';

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Jul' && $f_year_array[0] == $year[$key]) {
            $response[20] = $data['targets'][$key];
            $response[21] = $data['achievements'][$key]??'';
            if(isset($response[20]) && isset($response[21]) && !empty($response[21]) && !empty($response[20])) {
                $achievementPercent = ($response[20] == 0) ? 0 : ($response[21] * 100 / $response[20]);
            }else{
                $achievementPercent = '';
            }
            $response[22] = $achievementPercent;
        }else{
            if(!isset($response[20])) {
                $response[20] = '';
            }
            if(!isset($response[21])) {
                $response[21] = '';
            }
            if(!isset($response[22])) {
                $response[22] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Aug' && $f_year_array[0] == $year[$key]) {
            $response[23] = $data['targets'][$key];
            $response[24] = $data['achievements'][$key]??'';
            if(isset($response[23]) && isset($response[24]) && !empty($response[24]) && !empty($response[23])) {
                $achievementPercent = ($response[23] == 0) ? 0 : ($response[24] * 100 / $response[23]);
            }else{
                $achievementPercent = '';
            }
            $response[25] = $achievementPercent;
        }else{
            if(!isset($response[23])) {
                $response[23] = '';
            }
            if(!isset($response[24])) {
                $response[24] = '';
            }
            if(!isset($response[25])) {
                $response[25] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Sep' && $f_year_array[0] == $year[$key]) {
            $response[26] = $data['targets'][$key];
            $response[27] = $data['achievements'][$key]??'';
            if(isset($response[26]) && isset($response[27]) && !empty($response[27]) && !empty($response[26])) {
                $achievementPercent = ($response[26] == 0) ? 0 : ($response[27] * 100 / $response[26]);
            }else{
                $achievementPercent = '';
            }
            $response[28] = $achievementPercent;
        }else{
            if(!isset($response[26])) {
               $response[26] = '';
            }
            if(!isset($response[27])) {
               $response[27] = '';
            }
            if(!isset($response[28])) {
               $response[28] = '';
            }
        }
    }

    $response[29] = '=T'.$this->rowIndex.' + W'.$this->rowIndex.' + Z'.$this->rowIndex;
    $response[30] = '=U'.$this->rowIndex.' + X'.$this->rowIndex.' + AA'.$this->rowIndex;
    $response[31] = '=(V'.$this->rowIndex.' + Y'.$this->rowIndex.' + AB'.$this->rowIndex.') / 3';


    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Oct' && $f_year_array[0] == $year[$key]) {
            $response[32] = $data['targets'][$key];
            $response[33] = $data['achievements'][$key]??'';
            if(isset($response[32]) && isset($response[33]) && !empty($response[33]) && !empty($response[32])) {
                $achievementPercent = ($response[32] == 0) ? 0 : ($response[33] * 100 / $response[32]);
            }else{
                $achievementPercent = '';
            }
            $response[34] = $achievementPercent;
        }else{
            if(!isset($response[32])) {
               $response[32] = '';
            }
            if(!isset($response[33])) {
               $response[33] = '';
            }
            if(!isset($response[34])) {
               $response[34] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Nov' && $f_year_array[0] == $year[$key]) {
            $response[35] = $data['targets'][$key];
            $response[36] = $data['achievements'][$key]??'';
            if(isset($response[35]) && isset($response[36]) && !empty($response[36]) && !empty($response[35])) {
                $achievementPercent = ($response[35] == 0) ? 0 : ($response[36] * 100 / $response[35]);
            }else{
                $achievementPercent = '';
            }
            $response[37] = $achievementPercent;
        }else{
            if(!isset($response[35])) {
               $response[35] = '';
            }
            if(!isset($response[36])) {
               $response[36] = '';
            }
            if(!isset($response[37])) {
               $response[37] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Dec' && $f_year_array[0] == $year[$key]) {
            $response[38] = $data['targets'][$key];
            $response[39] = $data['achievements'][$key]??'';
            if(isset($response[38]) && isset($response[39]) && !empty($response[39]) && !empty($response[38])) {
                $achievementPercent = ($response[38] == 0) ? 0 : ($response[39] * 100 / $response[38]);
            }else{
                $achievementPercent = '';
            }
            $response[40] = $achievementPercent;
        }else{
            if(!isset($response[38])) {
               $response[38] = '';
            }
            if(!isset($response[39])) {
               $response[39] = '';
            }
            if(!isset($response[40])) {
               $response[40] = '';
            }
        }
    }

    $response[41] = '=AF'.$this->rowIndex.' + AI'.$this->rowIndex.' + AL'.$this->rowIndex;
    $response[42] = '=AG'.$this->rowIndex.' + AJ'.$this->rowIndex.' + AM'.$this->rowIndex;
    $response[43] = '=(AH'.$this->rowIndex.' + AK'.$this->rowIndex.' + AN'.$this->rowIndex.') / 3';

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Jan' && $f_year_array[1] == $year[$key]) {
            $response[44] = $data['targets'][$key];
            $response[45] = $data['achievements'][$key]??'';
            if(isset($response[44]) && isset($response[45]) && !empty($response[45]) && !empty($response[44])) {
                $achievementPercent = ($response[44] == 0) ? 0 : ($response[45] * 100 / $response[44]);
            }else{
                $achievementPercent = '';
            }
            $response[46] = $achievementPercent;
        }else{
            if(!isset($response[44])) {
               $response[44] = '';
            }
            if(!isset($response[45])) {
               $response[45] = '';
            }
            if(!isset($response[46])) {
               $response[46] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Feb' && $f_year_array[1] == $year[$key]) {
            $response[47] = $data['targets'][$key];
            $response[48] = $data['achievements'][$key]??'';
            if(isset($response[47]) && isset($response[48]) && !empty($response[48]) && !empty($response[47])) {
                $achievementPercent = ($response[47] == 0) ? 0 : ($response[48] * 100 / $response[47]);
            }else{
                $achievementPercent = '';
            }
            $response[49] = $achievementPercent;
        }else{
            if(!isset($response[47])) {
               $response[47] = '';
            }
            if(!isset($response[48])) {
               $response[48] = '';
            }
            if(!isset($response[49])) {
               $response[49] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Mar' && $f_year_array[1] == $year[$key]) {
            $response[50] = $data['targets'][$key];
            $response[51] = $data['achievements'][$key]??'';
            if(isset($response[50]) && isset($response[51]) && !empty($response[51]) && !empty($response[50])) {
                $achievementPercent = ($response[50] == 0) ? 0 : ($response[51] * 100 / $response[50]);
            }else{
                $achievementPercent = '';
            }
            $response[52] = $achievementPercent;
        }else{
            if(!isset($response[50])) {
                $response[50] = '';
            }
            if(!isset($response[51])) {
                $response[51] = '';
            }
            if(!isset($response[52])) {
                $response[52] = '';
            }
        }
    }

    $response[53] = '=AR'.$this->rowIndex.' + AU'.$this->rowIndex.' + AX'.$this->rowIndex;
    $response[54] = '=AS'.$this->rowIndex.' + AV'.$this->rowIndex.' + AY'.$this->rowIndex;
    $response[55] = '=(AT'.$this->rowIndex.' + AW'.$this->rowIndex.' + AZ'.$this->rowIndex.') / 3';

    $response[56] = '=Q'.$this->rowIndex.' + AC'.$this->rowIndex.' + AO'.$this->rowIndex.' + BA'.$this->rowIndex;
    $response[57] = '=R'.$this->rowIndex.' + AD'.$this->rowIndex.' + AP'.$this->rowIndex.' + BB'.$this->rowIndex;
    $response[58] = '=(S'.$this->rowIndex.' + AE'.$this->rowIndex.' + AQ'.$this->rowIndex.' + BC'.$this->rowIndex.') / 4';

    $this->rowIndex++;

    return $response;
}

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:D2');
        $sheet->mergeCells('E1:E2');
        $sheet->mergeCells('F1:F2');
        $sheet->mergeCells('G1:G2');
        $sheet->mergeCells('H1:J1');
        $sheet->mergeCells('K1:M1');
        $sheet->mergeCells('N1:P1');
        $sheet->mergeCells('Q1:S1');
        $sheet->mergeCells('T1:V1');
        $sheet->mergeCells('W1:Y1');
        $sheet->mergeCells('Z1:AB1');
        $sheet->mergeCells('AC1:AE1');
        $sheet->mergeCells('AF1:AH1');
        $sheet->mergeCells('AI1:AK1');
        $sheet->mergeCells('AL1:AN1');
        $sheet->mergeCells('AO1:AQ1');
        $sheet->mergeCells('AR1:AT1');
        $sheet->mergeCells('AU1:AW1');
        $sheet->mergeCells('AX1:AZ1');
        $sheet->mergeCells('BA1:BC1');
        $sheet->mergeCells('BD1:BF1');

        $sheet->getStyle('A1:ZZ1')->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'background' => [
                'color'=> '#000000'
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:ZZ2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
    }
}
