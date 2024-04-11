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
        $data = SalesTargetUsers::with(['user','user.getdesignation','user.getdivision','user.getbranch'])->select([
         DB::raw('GROUP_CONCAT(target) as targets'),
         DB::raw('GROUP_CONCAT(achievement) as achievements'),
         DB::raw('GROUP_CONCAT(month) as months'),  
         DB::raw('GROUP_CONCAT(year) as years'),
         DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
         DB::raw('user_id'),
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

        // dd($data);

        return $data;
    }


    public function headings(): array
    {
     $f_year_array = explode('-', $this->financial_year);

     $startYear = $f_year_array[0];

     $endYear = $f_year_array[1];

     $headings = ['Emp Code', 'User Name', 'Designation', 'Branch Name', 'Division'];

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

       $sub_headings = ['','','','','','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%'];

       $final_heading = [$headings, $sub_headings];

       return $final_heading;
   }


   public function map($data): array
   {
    $response = array();
    $response[0] = $data['user']['employee_codes'];
    $response[1] = $data['user']['name'];
    $response[2] = $data['user']['getdesignation']['designation_name']??'';
    $response[3] = $data['user']['getbranch']['branch_name'] ?? '';
    $response[4] = $data['user']['getdivision']['division_name']??'';
    $f_year_array = explode('-', $this->financial_year);
    $data['months'] = explode(',', $data['months']);
    $data['targets'] = explode(',', $data['targets']);
    $data['achievements'] = explode(',', $data['achievements']);
    $data['achievement_percents'] = explode(',', $data['achievement_percents']);


    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']); 

        if($month == 'Apr' && $f_year_array[0] == $year[$key]) {
            $response[5] = $data['targets'][$key];
            $response[6] = $data['achievements'][$key]??'';
            if(isset($response[5]) && isset($response[6]) && !empty($response[6]) && !empty($response[5])) {
                $achievementPercent = ($response[5] == 0) ? 0 : ($response[6] * 100 / $response[5]);
            }else{
                $achievementPercent = '';
            }   
            $response[7] = $achievementPercent;
        }
        else{
            if(!isset($response[5])) {
                $response[5] = '';
            }
            if(!isset($response[6])) {
                $response[6] = '';
            }
            if(!isset($response[7])) {
                $response[7] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'May' && $f_year_array[0] == $year[$key]) {
            $response[8] = $data['targets'][$key];
            $response[9] = $data['achievements'][$key]??'';
            if(isset($response[8]) && isset($response[9]) && !empty($response[9]) && !empty($response[8])) {
                $achievementPercent = ($response[8] == 0) ? 0 : ($response[9] * 100 / $response[8]);
            }else{
                $achievementPercent = '';
            }
            $response[10] = $achievementPercent;
        }
        else{
            if(!isset($response[8])) {
                $response[8] = '';
            }
            if(!isset($response[9])) {
                $response[9] = '';
            }
            if(!isset($response[10])) {
                $response[10] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Jun' && $f_year_array[0] == $year[$key]) {
            $response[11] = $data['targets'][$key];
            $response[12] = $data['achievements'][$key]??'';
            if(isset($response[11]) && isset($response[12]) && !empty($response[12]) && !empty($response[11])) {
                $achievementPercent = ($response[11] == 0) ? 0 : ($response[12] * 100 / $response[11]);
            }else{
                $achievementPercent = '';
            }
            $response[13] = $achievementPercent;
        }else{
            if(!isset($response[11])) {
                $response[11] = '';
            }
            if(!isset($response[12])) {
                $response[12] = '';
            }
            if(!isset($response[13])) {
                $response[13] = '';
            }
        }
    }

    $response[15] = '=F'.$this->rowIndex.' + I'.$this->rowIndex.' + L'.$this->rowIndex;
    $response[16] = '=G'.$this->rowIndex.' + J'.$this->rowIndex.' + M'.$this->rowIndex;
    $response[17] = '=(H'.$this->rowIndex.' + K'.$this->rowIndex.' + N'.$this->rowIndex.') / 3';

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Jul' && $f_year_array[0] == $year[$key]) {
            $response[18] = $data['targets'][$key];
            $response[19] = $data['achievements'][$key]??'';
            if(isset($response[18]) && isset($response[19]) && !empty($response[19]) && !empty($response[18])) {
                $achievementPercent = ($response[18] == 0) ? 0 : ($response[19] * 100 / $response[18]);
            }else{
                $achievementPercent = '';
            }
            $response[20] = $achievementPercent;
        }else{
            if(!isset($response[18])) {
                $response[18] = '';
            }
            if(!isset($response[19])) {
                $response[19] = '';
            }
            if(!isset($response[20])) {
                $response[20] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Aug' && $f_year_array[0] == $year[$key]) {
            $response[21] = $data['targets'][$key];
            $response[22] = $data['achievements'][$key]??'';
            if(isset($response[21]) && isset($response[22]) && !empty($response[22]) && !empty($response[21])) {
                $achievementPercent = ($response[21] == 0) ? 0 : ($response[22] * 100 / $response[21]);
            }else{
                $achievementPercent = '';
            }
            $response[23] = $achievementPercent;
        }else{
            if(!isset($response[21])) {
                $response[21] = '';
            }
            if(!isset($response[22])) {
                $response[22] = '';
            }
            if(!isset($response[23])) {
                $response[23] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Sep' && $f_year_array[0] == $year[$key]) {
            $response[24] = $data['targets'][$key];
            $response[25] = $data['achievements'][$key]??'';
            if(isset($response[24]) && isset($response[25]) && !empty($response[25]) && !empty($response[24])) {
                $achievementPercent = ($response[24] == 0) ? 0 : ($response[25] * 100 / $response[24]);
            }else{
                $achievementPercent = '';
            }
            $response[26] = $achievementPercent;
        }else{
            if(!isset($response[24])) {
               $response[24] = '';
            }
            if(!isset($response[25])) {
               $response[25] = '';
            }
            if(!isset($response[26])) {
               $response[26] = '';
            }
        }
    }

    $response[27] = '=R'.$this->rowIndex.' + U'.$this->rowIndex.' + X'.$this->rowIndex;
    $response[28] = '=S'.$this->rowIndex.' + V'.$this->rowIndex.' + Y'.$this->rowIndex;
    $response[29] = '=(T'.$this->rowIndex.' + W'.$this->rowIndex.' + Z'.$this->rowIndex.') / 3';


    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Oct' && $f_year_array[0] == $year[$key]) {
            $response[30] = $data['targets'][$key];
            $response[31] = $data['achievements'][$key]??'';
            if(isset($response[30]) && isset($response[31]) && !empty($response[31]) && !empty($response[30])) {
                $achievementPercent = ($response[30] == 0) ? 0 : ($response[31] * 100 / $response[30]);
            }else{
                $achievementPercent = '';
            }
            $response[32] = $achievementPercent;
        }else{
            if(!isset($response[30])) {
               $response[30] = '';
            }
            if(!isset($response[31])) {
               $response[31] = '';
            }
            if(!isset($response[32])) {
               $response[32] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Nov' && $f_year_array[0] == $year[$key]) {
            $response[33] = $data['targets'][$key];
            $response[34] = $data['achievements'][$key]??'';
            if(isset($response[33]) && isset($response[34]) && !empty($response[34]) && !empty($response[33])) {
                $achievementPercent = ($response[33] == 0) ? 0 : ($response[34] * 100 / $response[33]);
            }else{
                $achievementPercent = '';
            }
            $response[35] = $achievementPercent;
        }else{
            if(!isset($response[33])) {
               $response[33] = '';
            }
            if(!isset($response[34])) {
               $response[34] = '';
            }
            if(!isset($response[35])) {
               $response[35] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Dec' && $f_year_array[0] == $year[$key]) {
            $response[36] = $data['targets'][$key];
            $response[37] = $data['achievements'][$key]??'';
            if(isset($response[36]) && isset($response[37]) && !empty($response[37]) && !empty($response[36])) {
                $achievementPercent = ($response[36] == 0) ? 0 : ($response[37] * 100 / $response[36]);
            }else{
                $achievementPercent = '';
            }
            $response[38] = $achievementPercent;
        }else{
            if(!isset($response[36])) {
               $response[36] = '';
            }
            if(!isset($response[37])) {
               $response[37] = '';
            }
            if(!isset($response[38])) {
               $response[38] = '';
            }
        }
    }

    $response[39] = '=AD'.$this->rowIndex.' + AJ'.$this->rowIndex.' + AI'.$this->rowIndex;
    $response[40] = '=AE'.$this->rowIndex.' + AH'.$this->rowIndex.' + AK'.$this->rowIndex;
    $response[41] = '=(AF'.$this->rowIndex.' + AI'.$this->rowIndex.' + AL'.$this->rowIndex.') / 3';

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Jan' && $f_year_array[1] == $year[$key]) {
            $response[42] = $data['targets'][$key];
            $response[43] = $data['achievements'][$key]??'';
            if(isset($response[42]) && isset($response[43]) && !empty($response[43]) && !empty($response[42])) {
                $achievementPercent = ($response[42] == 0) ? 0 : ($response[43] * 100 / $response[42]);
            }else{
                $achievementPercent = '';
            }
            $response[44] = $achievementPercent;
        }else{
            if(!isset($response[42])) {
               $response[42] = '';
            }
            if(!isset($response[43])) {
               $response[43] = '';
            }
            if(!isset($response[44])) {
               $response[44] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Feb' && $f_year_array[1] == $year[$key]) {
            $response[45] = $data['targets'][$key];
            $response[46] = $data['achievements'][$key]??'';
            if(isset($response[45]) && isset($response[46]) && !empty($response[46]) && !empty($response[45])) {
                $achievementPercent = ($response[45] == 0) ? 0 : ($response[46] * 100 / $response[45]);
            }else{
                $achievementPercent = '';
            }
            $response[47] = $achievementPercent;
        }else{
            if(!isset($response[45])) {
               $response[45] = '';
            }
            if(!isset($response[46])) {
               $response[46] = '';
            }
            if(!isset($response[47])) {
               $response[47] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        if($month == 'Mar' && $f_year_array[1] == $year[$key]) {
            $response[48] = $data['targets'][$key];
            $response[49] = $data['achievements'][$key]??'';
            if(isset($response[48]) && isset($response[49]) && !empty($response[49]) && !empty($response[48])) {
                $achievementPercent = ($response[48] == 0) ? 0 : ($response[49] * 100 / $response[48]);
            }else{
                $achievementPercent = '';
            }
            $response[50] = $achievementPercent;
        }else{
            if(!isset($response[48])) {
                $response[48] = '';
            }
            if(!isset($response[49])) {
                $response[49] = '';
            }
            if(!isset($response[50])) {
                $response[50] = '';
            }
        }
    }

    $response[51] = '=AP'.$this->rowIndex.' + AS'.$this->rowIndex.' + AV'.$this->rowIndex;
    $response[52] = '=AQ'.$this->rowIndex.' + AT'.$this->rowIndex.' + AW'.$this->rowIndex;
    $response[53] = '=(AR'.$this->rowIndex.' + AU'.$this->rowIndex.' + AX'.$this->rowIndex.') / 3';

    $response[54] = '=O'.$this->rowIndex.' + AA'.$this->rowIndex.' + AM'.$this->rowIndex.' + AY'.$this->rowIndex;
    $response[55] = '=P'.$this->rowIndex.' + AB'.$this->rowIndex.' + AN'.$this->rowIndex.' + AZ'.$this->rowIndex;
    $response[56] = '=(Q'.$this->rowIndex.' + AC'.$this->rowIndex.' + AO'.$this->rowIndex.' + BA'.$this->rowIndex.') / 4';

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
        $sheet->mergeCells('F1:H1');
        $sheet->mergeCells('I1:K1');
        $sheet->mergeCells('L1:N1');
        $sheet->mergeCells('O1:Q1');
        $sheet->mergeCells('R1:T1');
        $sheet->mergeCells('U1:W1');
        $sheet->mergeCells('X1:Z1');
        $sheet->mergeCells('AA1:AC1');
        $sheet->mergeCells('AD1:AF1');
        $sheet->mergeCells('AG1:AI1');
        $sheet->mergeCells('AJ1:AL1');
        $sheet->mergeCells('AM1:AO1');
        $sheet->mergeCells('AP1:AR1');
        $sheet->mergeCells('AS1:AU1');
        $sheet->mergeCells('AV1:AX1');
        $sheet->mergeCells('AY1:BA1');
        $sheet->mergeCells('BB1:BD1');

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
