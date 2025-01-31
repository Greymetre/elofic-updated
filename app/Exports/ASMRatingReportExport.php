<?php

namespace App\Exports;

use App\Models\BranchStock;
use App\Models\City;
use App\Models\CustomerOutstanting;
use App\Models\Customers;
use App\Models\District;
use App\Models\EmployeeDetail;
use App\Models\MobileUserLoginDetails;
use App\Models\MspActivity;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PrimarySales;
use App\Models\Redemption;
use App\Models\TransactionHistory;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;
use Excel;
use DB;


class ASMRatingReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->start_date = '';
        $this->end_date = '';
        $this->designation_id = $request->input('designation_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
        $this->srno = 0;
    }

    public function collection()
    {
        $user_ids = getUsersReportingToAuth();
        $query = User::with('getbranch', 'getdivision', 'getdesignation', 'all_attendance_details', 'visits', 'customers', 'userinfo', 'target', 'primarySales');
        if ($this->user_id && $this->user_id != '' && $this->user_id != NULL) {
            $query->where('id', $this->user_id);
        } else {
            $query->whereIn('id', $user_ids);
        }
        if ($this->designation_id && $this->designation_id != '' && $this->designation_id != NULL) {
            $query->where('designation_id', $this->designation_id);
        }
        if ($this->division_id && $this->division_id != '' && $this->division_id != NULL) {
            $query->where('division_id', $this->division_id);
        }
        if ($this->branch_id && $this->branch_id != '' && $this->branch_id != NULL) {
            $query->where('branch_id', $this->branch_id);
        }

        $query = $query->where('sales_type', 'Primary')->latest()->get();

        if ($this->financial_year && $this->financial_year != '' && $this->financial_year != null) {
            $f_year_array = explode('-', $this->financial_year);

            $this->start_date = $f_year_array[0] . '-04-01';
            $this->end_date = $f_year_array[1] . '-03-31';
        }

        if ($this->month && $this->month != '' && $this->month != null && $this->financial_year && $this->financial_year != '' && $this->financial_year != null) {

            $f_year_array = explode('-', $this->financial_year);
            if (array_intersect($this->month, ['Jan', 'Feb', 'Mar'])) {
                $currentYear = $f_year_array[1];
                $monthNumbers = array_map(function ($month) {
                    return Carbon::parse($month. ' 01 2025')->month;
                }, $this->month);

                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);

                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $this->start_date = $firstDate->toDateString();
                $this->end_date = $lastDate->toDateString();
                dd($this->start_date, $this->end_date, 'mONTH');
            } else {
                $currentYear = $f_year_array[0];
                $monthNumbers = array_map(function ($month) {
                    return Carbon::parse($month. ' 01 2025')->month;
                }, $this->month);

                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);

                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $this->start_date = $firstDate->toDateString();
                $this->end_date = $lastDate->toDateString();
            }
        }
        return $query;
    }

    public function headings(): array
    {
        return [['Branch', 'Emp Code', 'ASM', 'DOJ', 'Final Rating', 'Number of days  dedicated to market visits', '', '', '', 'All Customer Visit', '', '', '', 'Number of new market place (white) mapped', '', '', '', 'Target Vs Ach', '', '', '', '', 'sales from new dealers as 40% of total Sales', '', '', '', '', 'sales from New products as 60% of total  sales', '', '', '', '', 'Debtors', '', '', '', '', 'Weightage', 'Saarthi Activation', '', '', '', 'MSP activity', '', '', ''], ['', '', '', '', '', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Target', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Target', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Target', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'TOTAL SALES FROM APR TO AUG', 'AVR PER DAYS SALES', 'TOTAL DEBTORS', 'DAYS', 'For Rating %', 'Final Rating', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Ach', '% ACHD', 'For Rating %', 'Final Rating']];
    }

    public function map($query): array
    {
        $f_year_array = explode('-', $this->financial_year);
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        $monthCount = $startDate->diffInMonths($endDate) + 1;
        $selectedmonths = [];
        while ($startDate->lessThanOrEqualTo($endDate)) {
            $selectedmonths[] = $startDate->format('M');
            $startDate->addMonth();
        }

        $working_days_trg = 20 * $monthCount;
        $visit_count_trg = 120 * $monthCount;
        $unique_visit_count_trg = 8 * $monthCount;
        $active_customer_trg = 8 * $monthCount;
        $msp_activity_trg = 4 * $monthCount;
        $working_days = $query->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Office Meeting', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '>=', $this->start_date)->where('punchin_date', '<=', $this->end_date)->count();
        $visit_count = $query->visits->where('checkin_date', '>=', $this->start_date)->where('checkin_date', '<=', $this->end_date)->count() > 0 ? $query->visits->where('checkin_date', '>=', $this->start_date)->where('checkin_date', '<=', $this->end_date)->count() : "0";
        $unique_visit_count = $query->visits->where('checkin_date', '>=', $this->start_date)->where('checkin_date', '<=', $this->end_date)->map(fn($visit) => optional(optional($visit->customers)->customeraddress)->city_id)->filter()->unique()->count();
        $user_target = $query->target->whereIn('month', $selectedmonths)->sum('target');
        $user_achiv = $query->primarySales->where('invoice_date', '>=', $this->start_date)->where('invoice_date', '<=', $this->end_date)->sum('net_amount');
        $user_achiv_new_dealer = $query->primarySales()->where('invoice_date', '>=', $this->start_date)->where('invoice_date', '<=', $this->end_date)->where('new_dealer', 'Y')->sum('net_amount');
        $user_achiv_new_product = $query->primarySales()->where('invoice_date', '>=', $this->start_date)->where('invoice_date', '<=', $this->end_date)->where('new_product', 'Y')->sum('net_amount');
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $user_ids = getUsersReportingToAuth($query->id);
        $total_assign_customer_ids = EmployeeDetail::where('user_id', $query->id)->pluck('customer_id')->toArray();
        $active_customer = 0;

        foreach (array_chunk($total_assign_customer_ids, 500) as $chunk) {
            $active_customer += TransactionHistory::whereBetween('created_at', [$this->start_date, $this->end_date])
                ->whereIn('customer_id', $chunk)
                ->whereNotIn('customer_id', function ($query) {
                    $query->select('customer_id')
                        ->from('transaction_histories')
                        ->where('created_at', '<', $this->start_date);
                })
                ->groupBy('customer_id')
                ->selectRaw('customer_id')
                ->get()
                ->count();
        }

        $debtors_start_date = $f_year_array[0] . '-04-01';
        $debtors_end_date = $f_year_array[0].'-12-31';
        // $debtors_end_date = now()->toDateString();

        $debtors_start_date_or = Carbon::createFromFormat('Y-m-d', $f_year_array[0] . '-04-01');
        $debtors_end_date_or = now();

        // $days_difference = $debtors_start_date_or->diffInDays($debtors_end_date_or);
        $days_difference = 270;

        $debtors_sales = PrimarySales::where('branch_id', $query->branch_id)->where('invoice_date', '>=', $debtors_start_date)->where('invoice_date', '<=', $debtors_end_date)->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount');
        $total_debtors = CustomerOutstanting::where('branch_id', $query->branch_id)->whereIn('division_id', ['10', '18'])->where('year', $f_year_array[0])->sum('amount');

        $msp_activitys = MspActivity::where('emp_code', $query->employee_codes);
        if(isset($this->month) && count($this->month) > 0){
            $msp_activitys->whereIn('month', $this->month);
        }
        $msp_activitys = $msp_activitys->where('fyear', getCurrentFinancialYear($this->financial_year))->sum('msp_count');

        static $rowNumber = 3;
        $result = [
            $query['getbranch'] ? $query['getbranch']['branch_name'] : '-',
            $query['employee_codes'],
            $query['name'],
            $query['userinfo'] ? date('d M Y', strtotime($query['userinfo']['date_of_joining'])) : '',

            "=AT{$rowNumber}",

            $working_days,
            round(($working_days / $working_days_trg) * 100, 0) . '%',
            (($working_days / $working_days_trg) * 100 >= 100) ? '100%' : round(($working_days / $working_days_trg) * 100, 0) . '%',
            $number_days = (($working_days / $working_days_trg) * 100 >= 100) ? '5' : (round((5 * (($working_days / $working_days_trg) * 100)) / 100, 0) > 0 ? round((5 * (($working_days / $working_days_trg) * 100)) / 100, 0) : '0'),

            $visit_count,
            round(($visit_count / $visit_count_trg) * 100, 0) . '%',
            (($visit_count / $visit_count_trg) * 100 >= 100) ? '100%' : round(($visit_count / $visit_count_trg) * 100, 0) . '%',
            $all_cust = (($visit_count / $visit_count_trg) * 100 >= 100) ? '5' : (round((5 * (($visit_count / $visit_count_trg) * 100)) / 100, 0) > 0 ? round((5 * (($visit_count / $visit_count_trg) * 100)) / 100, 0) : '0'),

            $unique_visit_count,
            round(($unique_visit_count / $unique_visit_count_trg) * 100, 0) . '%',
            (($unique_visit_count / $unique_visit_count_trg) * 100 >= 100) ? '100%' : round(($unique_visit_count / $unique_visit_count_trg) * 100, 0) . '%',
            $uniq_cust = (($unique_visit_count / $unique_visit_count_trg) * 100 >= 100) ? '5' : (round((5 * (($unique_visit_count / $unique_visit_count_trg) * 100)) / 100, 0) > 0 ? round((5 * (($unique_visit_count / $unique_visit_count_trg) * 100)) / 100, 0) : '0'),

            $user_target,
            $user_achiv > 0 ? round(($user_achiv / 100000), 2) : '0',
            $user_target > 0 ? round((($user_achiv / 100000) / $user_target) * 100, 0) . '%' : '0%',
            $user_target > 0 ? (((($user_achiv / 100000) / $user_target) * 100) >= 100 ? '100%' : round((($user_achiv / 100000) / $user_target) * 100, 0) . '%') : '0%',
            $targets = $user_target > 0 ? (((($user_achiv / 100000) / $user_target) * 100) >= 100 ? '40' : round(40 * ((($user_achiv / 100000) / $user_target) * 100) / 100, 0)) : '0',

            $user_achiv > 0 ? round((($user_achiv / 100000) * 40) / 100, 1) : '0',
            $user_achiv_new_dealer > 0 ? round(($user_achiv_new_dealer / 100000), 2) : '0',
            ((($user_achiv / 100000) * 40) / 100) > 0 ? round((($user_achiv_new_dealer / 100000) / ((($user_achiv / 100000) * 40) / 100)) * 100, 0) . '%' : '0%',
            ((($user_achiv / 100000) * 40) / 100) > 0 ? (((($user_achiv_new_dealer / 100000) / ((($user_achiv / 100000) * 40) / 100)) * 100) >= 100 ? '100%' : round((($user_achiv_new_dealer / 100000) / ((($user_achiv / 100000) * 40) / 100)) * 100, 0) . '%') : '0%',
            $new_sale = ((($user_achiv / 100000) * 40) / 100) > 0 ? (((($user_achiv_new_dealer / 100000) / ((($user_achiv / 100000) * 40) / 100)) * 100) >= 100 ? '10' : round(10 * ((($user_achiv_new_dealer / 100000) / ((($user_achiv / 100000) * 40) / 100)) * 100) / 100, 0)) : '0',

            $user_achiv > 0 ? round((($user_achiv / 100000) * 60) / 100, 2) : '0',
            $user_achiv_new_product > 0 ? round(($user_achiv_new_product / 100000), 2) : '0',
            ((($user_achiv / 100000) * 60) / 100) > 0 ? round((($user_achiv_new_product / 100000) / ((($user_achiv / 100000) * 60) / 100)) * 100, 0) . '%' : '0%',
            ((($user_achiv / 100000) * 60) / 100) > 0 ? (((($user_achiv_new_product / 100000) / ((($user_achiv / 100000) * 60) / 100)) * 100) >= 100 ? '100%' : round((($user_achiv_new_product / 100000) / ((($user_achiv / 100000) * 60) / 100)) * 100, 0) . '%') : '0%',
            $newpro = ((($user_achiv / 100000) * 60) / 100) > 0 ? (((($user_achiv_new_product / 100000) / ((($user_achiv / 100000) * 60) / 100)) * 100) >= 100 ? '10' : round(10 * ((($user_achiv_new_product / 100000) / ((($user_achiv / 100000) * 60) / 100)) * 100) / 100, 0)) : '0',

            $debtors_sales > 0 ? round(($debtors_sales / 100000), 2) : '0',
            $debtors_sales > 0 ? round((($debtors_sales / 100000) / $days_difference), 2) : '0',
            $total_debtors > 0 ? round($total_debtors, 1) : '0',
            $days = ($debtors_sales / 100000) / 270 > 0 && $total_debtors > 0 ? round(($total_debtors / (($debtors_sales / 100000) / $days_difference)), 0) : '100',
            $percentage = $days <= 30 ? '100%' : ($days <= 60 ? '80%' : ($days <= 90 ? '50%' : '0%')),
            $debtor = (20*(int)$percentage)/100,
            
            $active_customer > 0 ? $active_customer : '0',
            round(($active_customer / $active_customer_trg) * 100, 0) . '%',
            (($active_customer / $active_customer_trg) * 100 >= 100) ? '100%' : round(($active_customer / $active_customer_trg) * 100, 0) . '%',
            $sarthi_custo = (($active_customer / $active_customer_trg) * 100 >= 100) ? '5' : (round((5 * (($active_customer / $active_customer_trg) * 100)) / 100, 0) > 0 ? round((5 * (($active_customer / $active_customer_trg) * 100)) / 100, 0) : '0'),

            $msp_activitys > 0 ? $msp_activitys : '0',
            $msp_activitys > 0 ? round(($msp_activitys/$msp_activity_trg)*100,   0) : '0',
            $msp_final = (($msp_activitys/$msp_activity_trg)*100 >= 100) ? '5' : (round((5 * (($msp_activitys/$msp_activity_trg)*100)) / 100, 0) > 0 ? round((5 * (($msp_activitys/$msp_activity_trg)*100)) / 100, 0) : '0'),

            $sarthi_custo + $debtor + $newpro + $new_sale + $all_cust + $uniq_cust + $targets + $number_days + $msp_final,

        ];
        $rowNumber++;

        return $result;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();
                $rowCount = $event->sheet->getHighestDataRow();
                $event->sheet->mergeCells('A1:A2');
                $event->sheet->mergeCells('B1:B2');
                $event->sheet->mergeCells('C1:C2');
                $event->sheet->mergeCells('D1:D2');
                $event->sheet->mergeCells('E1:E2');
                $event->sheet->mergeCells('F1:I1');
                $event->sheet->mergeCells('J1:M1');
                $event->sheet->mergeCells('N1:Q1');
                $event->sheet->mergeCells('R1:V1');
                $event->sheet->mergeCells('W1:AA1');
                $event->sheet->mergeCells('AB1:AF1');
                $event->sheet->mergeCells('AG1:AK1');
                $event->sheet->mergeCells('AM1:AP1');
                $event->sheet->mergeCells('AQ1:AT1');

                // for ($row = 1; $row <= $rowCount; $row++) {
                //     $cellValue = $event->sheet->getCell('AC' . $row)->getValue();
                //     $color = self::getColorBasedOnValue($cellValue);

                //     $event->sheet->getStyle('AC' . $row)->applyFromArray([
                //         'fill' => [
                //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                //             'startColor' => ['rgb' => $color],
                //         ],
                //     ]);
                //     $event->sheet->getStyle('C' . $row)->applyFromArray([
                //         'fill' => [
                //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                //             'startColor' => ['rgb' => $color],
                //         ],
                //     ]);
                // }

                $event->sheet->getStyle('A1:AT2')->applyFromArray([
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

                $event->sheet->getStyle('A' . $lastRow . ':AT' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'], // Border color
                        ],
                    ],
                ]);
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

    private static function getColorBasedOnValue($value)
    {
        if ($value <= 24.99) {
            return 'FF0000'; // Red
        } elseif ($value >= 25 && $value <= 29.99) {
            return 'FFFF00'; // Yellow
        } elseif ($value >= 29.99) {
            return '00FF00'; // Green
        }
    }
}
