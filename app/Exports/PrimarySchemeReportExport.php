<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\CustomerOutstanting;
use App\Models\PrimarySales;
use App\Models\PrimaryScheme;
use App\Models\PrimarySchemeDetail;
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
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PrimarySchemeReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithStyles, WithEvents
{

    private $rowIndex = 3;

    public function __construct($request)
    {
        $this->months = array();
        $this->branch_id = $request->input('branch_id');
        $this->financial_year = $request->input('financial_year');
        $this->quarter = $request->input('quarter');
        $this->scheme_id = $request->input('scheme_id');
        $this->division = $request->input('division');
        $this->quarter_name = '';
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);
        $pSchemesGroup = PrimarySchemeDetail::whereIn('primary_scheme_id', $this->scheme_id)->groupBy('groups')->pluck('groups');
        $data = PrimarySales::with(['user', 'user.getdesignation', 'user.getdivision', 'branch', 'customer'])->select([
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(net_amount) as total_net_amount'),
            DB::raw('emp_code'),
            DB::raw('branch_id'),
            DB::raw('customer_id'),
            DB::raw('division'),
            DB::raw('new_group_name'),
        ])->whereIn('new_group_name', $pSchemesGroup);

        if ($this->quarter && !empty($this->quarter)) {
            if ($this->quarter == '1') {
                $this->quarter_name = 'Q1';
                $data->where(function ($query) use ($f_year_array) {
                    $query->whereYear('invoice_date', '=', $f_year_array[0])
                        ->whereIn('month', ['Apr', 'May', 'Jun']);
                });
                $this->months = ['Apr', 'May', 'Jun'];
            } elseif ($this->quarter == '2') {
                $this->quarter_name = 'Q2';
                $data->where(function ($query) use ($f_year_array) {
                    $query->whereYear('invoice_date', '=', $f_year_array[0])
                        ->whereIn('month', ['Jul', 'Aug', 'Sep']);
                });
                $this->months = ['Jul', 'Aug', 'Sep'];
            } elseif ($this->quarter == '3') {
                $this->quarter_name = 'Q3';
                $data->where(function ($query) use ($f_year_array) {
                    $query->whereYear('invoice_date', '=', $f_year_array[0])
                        ->whereIn('month', ['Oct', 'Nov', 'Dec']);
                });
                $this->months = ['Oct', 'Nov', 'Dec'];
            } elseif ($this->quarter == '4') {
                $this->quarter_name = 'Q4';
                $data->where(function ($query) use ($f_year_array) {
                    $query->whereYear('invoice_date', '=', $f_year_array[1])
                        ->whereIn('month', ['Jan', 'Feb', 'Mar']);
                });
                $this->months = ['Oct', 'Nov', 'Dec'];
            }
        }

        if($this->division && !empty($this->division)){
            $data->whereIn('division', $this->division);
        }

        $data = $data->groupBy('customer_id','emp_code','branch_id','division','new_group_name')->orderBy('month')->get();

        // dd($data);

        return $data;
    }


    public function headings(): array
    {
        // $f_year_array = explode('-', $this->financial_year);

        // $startYear = $f_year_array[0];

        // $endYear = $f_year_array[1];

        $headings = ['FY','Quarter','Div','Dealer','City','State','Final Branch','Sales person', 'Emp Code', 'New Group Name', 'Sale Return Qty', 'Sale Return Value','Sales Quantity','Sales Net Amount','After  Sales Return Quantity','After Sales Return Net Amount','Discount (CN)','Scheme Name'];


        // if ($this->quarter && !empty($this->quarter)) {
        //     if ($this->quarter == '1') {
        //         $headings[] = 'Apr-' . $startYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'May-' . $startYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Jun-' . $startYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Q1';
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = '';
        //     } elseif ($this->quarter == '2') {
        //         $headings[] = 'Jul-' . $startYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Aug-' . $startYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Sep-' . $startYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Q2';
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = '';
        //     } elseif ($this->quarter == '3') {
        //         $headings[] = 'Oct-' . $startYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Nov-' . $startYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Dec-' . $startYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Q3';
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = '';
        //     } elseif ($this->quarter == '4') {
        //         $headings[] = 'Jan-' . $endYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Feb-' . $endYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Mar-' . $endYear;
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = 'Q4';
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = '';
        //         $headings[] = '';
        //     }
        // }

        // $headings[] = 'Total Outstanding Value';
        // $headings[] = 'Outstanding Value (>60 Days)';
        // $headings[] = 'Outstanding Value (>60 Days) %';
        // $headings[] = 'Total Stock Value';
        // $headings[] = 'Stock Value (>90 Days)';
        // $headings[] = 'Stock Value (>90 Days) %';
        // $headings[] = 'Total Incentive';
        // $headings[] = 'Total Incentive as per weightage';

        // $sub_headings = ['', '', '', '', '', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Fresh Sales Return', 'Net Sales', 'Target Achievement (%)'];

        // $final_heading = [$headings, $sub_headings];

        return $headings;
    }


    public function map($data): array
    {
        $CM = PrimarySchemeDetail::where('groups',$data['new_group_name'])->where('min', '<=', $data['total_quantity'])->where('max', '>=', $data['total_quantity'])->first();
        // dd($CM);
        $response = array();
        $response[0] = $this->financial_year;
        $response[1] = 'Q'.$this->quarter;
        $response[2] = $data['division'] ?? '';
        $response[3] = $data['customer'] ? $data['customer']['name'] : '-';
        $response[4] = $data['customer']['customeraddress'] ? ($data['customer']['customeraddress']['cityname']?$data['customer']['customeraddress']['cityname']['city_name']:'-') : '-';
        $response[5] = $data['customer']['customeraddress'] ? ($data['customer']['customeraddress']['statename']?$data['customer']['customeraddress']['statename']['state_name']:'-') : '-';
        $response[6] = $data['branch'] ? $data['branch']['branch_name'] : '-';
        $response[7] = $data['user'] ? $data['user']['name'] : '-';
        $response[8] = $data['emp_code'] ?? '-';
        $response[9] = $data['new_group_name'] ?? '-';
        $response[10] = '-';
        $response[11] = '-';
        $response[12] = $data['total_quantity'] ?? '-';
        $response[13] = $data['total_net_amount'] ?? '-';
        $response[14] = '-';
        $response[15] = '-';
        $response[16] = $CM?$CM->points.'%':'0%';
        $response[17] = $CM?$CM->primaryscheme->scheme_name:'-';
        
        return $response;
    }

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '336677'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();

                $event->sheet->getStyle('A2:' . $lastColumn . '' . ($lastRow - 2))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
            },
        ];
    }
}
