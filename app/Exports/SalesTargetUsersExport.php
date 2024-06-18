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
use App\Models\User;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesTargetUsersExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping,WithStyles
{

    private $rowIndex = 3;

    public function __construct($request)
    {    
        $this->user_id = $request->input('user');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
        $this->target = $request->input('target');  
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);
        

        // $data = SalesTargetUsers::with(['user'])->whereBetween('year', $f_year_array)->toSql();
        $userIds = getUsersReportingToAuth();
        $data = SalesTargetUsers::with(['user','user.getdesignation','user.getdivision','user.getbranch'])->whereIn('user_id', $userIds)->select([
         DB::raw('GROUP_CONCAT(target) as targets'),
         DB::raw('GROUP_CONCAT(achievement) as achievements'),
         DB::raw('GROUP_CONCAT(month) as months'),  
         DB::raw('GROUP_CONCAT(year) as years'),
         DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
         DB::raw('user_id'),
         DB::raw('type'),
        ]); 

        if($this->month == '' && empty($this->month)){
            $data->where(function ($query) use($f_year_array) {
                $query->where('year', '=', $f_year_array[0])
                      ->where('month', '>=', 'Apr');
            })->orWhere(function ($query) use($f_year_array) {
                $query->where('year', '=', $f_year_array[1])
                      ->where('month', '<=', 'Mar');
            });
        }else {
           $data->where(function ($query) use($f_year_array) {
                $query->where('year', '=', $f_year_array[0])
                      ->where('month', '>=', $this->month);
            })->orWhere(function ($query) use($f_year_array) {
                $query->where('year', '=', $f_year_array[1])
                      ->where('month', '<=', $this->month);
            });
        }
        
        $data = $data->groupBy('user_id')->orderBy('month')->get();

        return $data;
    }


    public function headings(): array
    {
     $f_year_array = explode('-', $this->financial_year);

     $startYear = $f_year_array[0];

     $endYear = $f_year_array[1];

     $headings = ['Emp Code', 'User Name', 'Designation', 'Branch Name', 'Division','Sales Type'];

     $quarterNames = ['Q1', 'Q2', 'Q3', 'Q4'];

     $quarterIndex = 0;

     for ($year = $startYear; $year <= $endYear; $year++) {
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

       $sub_headings = ['','','','','','','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%'];

       $final_heading = [$headings, $sub_headings];

       return $final_heading;
   }


   public function map($data): array
   {
    $response = array();
    $response[0] = $data['user']['employee_codes']??'';
    $response[1] = $data['user']['name']??'';
    $response[2] = $data['user']['getdesignation']['designation_name']??'';
    $response[3] = $data['user']['getbranch']['branch_name'] ?? '';
    $response[4] = $data['user']['getdivision']['division_name']??'';
    $response[5] = $data['type']??'';
    $f_year_array = explode('-', $this->financial_year);
    $data['months'] = explode(',', $data['months']);
    $data['targets'] = explode(',', $data['targets']);
    $data['achievements'] = explode(',', $data['achievements']);
    $data['achievement_percents'] = explode(',', $data['achievement_percents']);


    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']); 

        if($month == 'Apr' && $f_year_array[0] == $year[$key]) {
            $response[6] = $data['targets'][$key];
            $response[7] = $data['achievements'][$key]??'';
            if(isset($response[6]) && isset($response[7]) && !empty($response[7]) && !empty($response[6])) {
                $achievementPercent = ($response[6] == 0) ? 0 : ($response[7] * 100 / $response[6]);
            }else{
                $achievementPercent = '';
            }   
            $response[8] = $achievementPercent;
        }
        else{
            if(!isset($response[6])) {
                $response[6] = '';
            }
            if(!isset($response[7])) {
                $response[7] = '';
            }
            if(!isset($response[8])) {
                $response[8] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'May' && $f_year_array[0] == $year[$key]) {
            $response[9] = $data['targets'][$key];
            $response[10] = $data['achievements'][$key]??'';
            if(isset($response[9]) && isset($response[10]) && !empty($response[10]) && !empty($response[9])) {
                $achievementPercent = ($response[9] == 0) ? 0 : ($response[10] * 100 / $response[9]);
            }else{
                $achievementPercent = '';
            }
            $response[11] = $achievementPercent;
        }
        else{
            if(!isset($response[9])) {
                $response[9] = '';
            }
            if(!isset($response[10])) {
                $response[10] = '';
            }
            if(!isset($response[11])) {
                $response[11] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Jun' && $f_year_array[0] == $year[$key]) {
            $response[12] = $data['targets'][$key];
            $response[13] = $data['achievements'][$key]??'';
            if(isset($response[12]) && isset($response[13]) && !empty($response[13]) && !empty($response[12])) {
                $achievementPercent = ($response[12] == 0) ? 0 : ($response[13] * 100 / $response[12]);
            }else{
                $achievementPercent = '';
            }
            $response[14] = $achievementPercent;
        }else{
            if(!isset($response[12])) {
                $response[12] = '';
            }
            if(!isset($response[13])) {
                $response[13] = '';
            }
            if(!isset($response[14])) {
                $response[14] = '';
            }
        }
    }

    $response[16] = '=G'.$this->rowIndex.' + J'.$this->rowIndex.' + M'.$this->rowIndex;
    $response[17] = '=H'.$this->rowIndex.' + K'.$this->rowIndex.' + N'.$this->rowIndex;
    $response[18] = '=(I'.$this->rowIndex.' + L'.$this->rowIndex.' + O'.$this->rowIndex.') / 3';

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Jul' && $f_year_array[0] == $year[$key]) {
            $response[19] = $data['targets'][$key];
            $response[20] = $data['achievements'][$key]??'';
            if(isset($response[19]) && isset($response[20]) && !empty($response[20]) && !empty($response[19])) {
                $achievementPercent = ($response[19] == 0) ? 0 : ($response[20] * 100 / $response[19]);
            }else{
                $achievementPercent = '';
            }
            $response[21] = $achievementPercent;
        }else{
            if(!isset($response[19])) {
                $response[19] = '';
            }
            if(!isset($response[20])) {
                $response[20] = '';
            }
            if(!isset($response[21])) {
                $response[2] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Aug' && $f_year_array[0] == $year[$key]) {
            $response[22] = $data['targets'][$key];
            $response[23] = $data['achievements'][$key]??'';
            if(isset($response[22]) && isset($response[23]) && !empty($response[23]) && !empty($response[22])) {
                $achievementPercent = ($response[22] == 0) ? 0 : ($response[23] * 100 / $response[22]);
            }else{
                $achievementPercent = '';
            }
            $response[24] = $achievementPercent;
        }else{
            if(!isset($response[22])) {
                $response[22] = '';
            }
            if(!isset($response[23])) {
                $response[23] = '';
            }
            if(!isset($response[24])) {
                $response[24] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Sep' && $f_year_array[0] == $year[$key]) {
            $response[25] = $data['targets'][$key];
            $response[26] = $data['achievements'][$key]??'';
            if(isset($response[25]) && isset($response[26]) && !empty($response[26]) && !empty($response[25])) {
                $achievementPercent = ($response[25] == 0) ? 0 : ($response[26] * 100 / $response[25]);
            }else{
                $achievementPercent = '';
            }
            $response[27] = $achievementPercent;
        }else{
            if(!isset($response[25])) {
               $response[25] = '';
            }
            if(!isset($response[26])) {
               $response[26] = '';
            }
            if(!isset($response[27])) {
               $response[27] = '';
            }
        }
    }

    $response[28] = '=S'.$this->rowIndex.' + V'.$this->rowIndex.' + Y'.$this->rowIndex;
    $response[29] = '=T'.$this->rowIndex.' + W'.$this->rowIndex.' + Z'.$this->rowIndex;
    $response[30] = '=(U'.$this->rowIndex.' + X'.$this->rowIndex.' + AA'.$this->rowIndex.') / 3';


    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Oct' && $f_year_array[0] == $year[$key]) {
            $response[31] = $data['targets'][$key];
            $response[32] = $data['achievements'][$key]??'';
            if(isset($response[31]) && isset($response[32]) && !empty($response[32]) && !empty($response[31])) {
                $achievementPercent = ($response[31] == 0) ? 0 : ($response[32] * 100 / $response[31]);
            }else{
                $achievementPercent = '';
            }
            $response[33] = $achievementPercent;
        }else{
            if(!isset($response[31])) {
               $response[31] = '';
            }
            if(!isset($response[32])) {
               $response[32] = '';
            }
            if(!isset($response[33])) {
               $response[33] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Nov' && $f_year_array[0] == $year[$key]) {
            $response[34] = $data['targets'][$key];
            $response[35] = $data['achievements'][$key]??'';
            if(isset($response[34]) && isset($response[35]) && !empty($response[35]) && !empty($response[34])) {
                $achievementPercent = ($response[34] == 0) ? 0 : ($response[35] * 100 / $response[34]);
            }else{
                $achievementPercent = '';
            }
            $response[36] = $achievementPercent;
        }else{
            if(!isset($response[34])) {
               $response[34] = '';
            }
            if(!isset($response[35])) {
               $response[35] = '';
            }
            if(!isset($response[36])) {
               $response[36] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Dec' && $f_year_array[0] == $year[$key]) {
            $response[37] = $data['targets'][$key];
            $response[38] = $data['achievements'][$key]??'';
            if(isset($response[37]) && isset($response[38]) && !empty($response[38]) && !empty($response[37])) {
                $achievementPercent = ($response[37] == 0) ? 0 : ($response[38] * 100 / $response[37]);
            }else{
                $achievementPercent = '';
            }
            $response[39] = $achievementPercent;
        }else{
            if(!isset($response[37])) {
               $response[37] = '';
            }
            if(!isset($response[38])) {
               $response[38] = '';
            }
            if(!isset($response[39])) {
               $response[39] = '';
            }
        }
    }

    $response[40] = '=AE'.$this->rowIndex.' + AH'.$this->rowIndex.' + AK'.$this->rowIndex;
    $response[41] = '=AF'.$this->rowIndex.' + AI'.$this->rowIndex.' + AL'.$this->rowIndex;
    $response[42] = '=(AG'.$this->rowIndex.' + AJ'.$this->rowIndex.' + AM'.$this->rowIndex.') / 3';

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Jan' && $f_year_array[1] == $year[$key]) {
            $response[43] = $data['targets'][$key];
            $response[44] = $data['achievements'][$key]??'';
            if(isset($response[43]) && isset($response[44]) && !empty($response[44]) && !empty($response[43])) {
                $achievementPercent = ($response[43] == 0) ? 0 : ($response[44] * 100 / $response[43]);
            }else{
                $achievementPercent = '';
            }
            $response[45] = $achievementPercent;
        }else{
            if(!isset($response[43])) {
               $response[43] = '';
            }
            if(!isset($response[44])) {
               $response[44] = '';
            }
            if(!isset($response[45])) {
               $response[45] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Feb' && $f_year_array[1] == $year[$key]) {
            $response[46] = $data['targets'][$key];
            $response[47] = $data['achievements'][$key]??'';
            if(isset($response[46]) && isset($response[47]) && !empty($response[47]) && !empty($response[46])) {
                $achievementPercent = ($response[46] == 0) ? 0 : ($response[47] * 100 / $response[46]);
            }else{
                $achievementPercent = '';
            }
            $response[48] = $achievementPercent;
        }else{
            if(!isset($response[46])) {
               $response[46] = '';
            }
            if(!isset($response[47])) {
               $response[47] = '';
            }
            if(!isset($response[48])) {
               $response[48] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Mar' && $f_year_array[1] == $year[$key]) {
            $response[49] = $data['targets'][$key];
            $response[50] = $data['achievements'][$key]??'';
            if(isset($response[49]) && isset($response[50]) && !empty($response[50]) && !empty($response[49])) {
                $achievementPercent = ($response[49] == 0) ? 0 : ($response[50] * 100 / $response[49]);
            }else{
                $achievementPercent = '';
            }
            $response[50] = $achievementPercent;
        }else{
            if(!isset($response[49])) {
                $response[49] = '';
            }
            if(!isset($response[50])) {
                $response[50] = '';
            }
            if(!isset($response[51])) {
                $response[51] = '';
            }
        }
    }

    $response[52] = '=AQ'.$this->rowIndex.' + AT'.$this->rowIndex.' + AW'.$this->rowIndex;
    $response[53] = '=AR'.$this->rowIndex.' + AU'.$this->rowIndex.' + AX'.$this->rowIndex;
    $response[54] = '=(AS'.$this->rowIndex.' + AV'.$this->rowIndex.' + AY'.$this->rowIndex.') / 3';

    $response[55] = '=P'.$this->rowIndex.' + AB'.$this->rowIndex.' + AN'.$this->rowIndex.' + AZ'.$this->rowIndex;
    $response[56] = '=Q'.$this->rowIndex.' + AC'.$this->rowIndex.' + AO'.$this->rowIndex.' + BA'.$this->rowIndex;
    $response[57] = '=(R'.$this->rowIndex.' + AD'.$this->rowIndex.' + AP'.$this->rowIndex.' + BB'.$this->rowIndex.') / 4';

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
        $sheet->mergeCells('G1:I1');
        $sheet->mergeCells('J1:L1');
        $sheet->mergeCells('M1:O1');
        $sheet->mergeCells('P1:R1');
        $sheet->mergeCells('S1:U1');
        $sheet->mergeCells('V1:X1');
        $sheet->mergeCells('Y1:AA1');
        $sheet->mergeCells('AB1:AD1');
        $sheet->mergeCells('AE1:AG1');
        $sheet->mergeCells('AH1:AJ1');
        $sheet->mergeCells('AK1:AM1');
        $sheet->mergeCells('AN1:AP1');
        $sheet->mergeCells('AQ1:AS1');
        $sheet->mergeCells('AT1:AV1');
        $sheet->mergeCells('AW1:AY1');
        $sheet->mergeCells('AZ1:BB1');
        $sheet->mergeCells('BC1:BE1');

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
