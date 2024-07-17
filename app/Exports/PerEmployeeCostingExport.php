<?php

namespace App\Exports;

use App\Models\PrimarySales;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use DB;

class PerEmployeeCostingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{

    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->branch_id = $request->input('branch_id');
        $this->division_id = $request->input('division');
        $this->dealer_id = $request->input('dealer_id');
        $this->product_model = $request->input('product_model');
        $this->new_group = $request->input('new_group');
        $this->executive_id = $request->input('executive_id');
        $this->financial_year = $request->input('financial_year');
        $this->month = $request->input('month');
        $this->months = [];
        $this->t_data = '';
    }
    public function collection()
    {
        $currentDate = Carbon::now();
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $query = User::with('primarySales', 'getdesignation', 'getbranch', 'getdivision', 'userinfo')->where('active', 'Y');

        if ($this->division_id && $this->division_id != '' && $this->division_id != null) {
            $query->where('division_id', $this->division_id);
        }

        $data = $query->orderBy('id', 'desc')->get();

        if ($this->branch_id && $this->branch_id != '' && $this->branch_id != null) {
            $query->where('branch_id', $this->branch_id);
        }


        if ($this->dealer_id && $this->dealer_id != '' && $this->dealer_id != null) {
            $query->where('dealer', 'like', '%' . $this->dealer_id . '%');
        }

        if ($this->product_model && $this->product_model != '' && $this->product_model != null) {
            $query->where('model_name', $this->product_model);
        }

        if ($this->new_group && $this->new_group != '' && $this->new_group != null) {
            $query->where('new_group', $this->new_group);
        }

        if ($this->executive_id && $this->executive_id != '' && $this->executive_id != null) {
            $query->where('id', $this->executive_id);
        }

        $query = $query->get();
        return $query->get();
    }

    public function headings(): array
    {
        $label1 = [
            'Division',
            'Branch',
            'Emp Code',
            'Designation',
            'DOJ',
            'Sales/Othe/FOS/FOS-C'
        ];

        if ($this->month && is_array($this->month) && count($this->month) > 0 && $this->financial_year && !empty($this->financial_year)) {
            $f_year_array = explode('-', $this->financial_year);

            // Determine if months are in Jan-Mar and set the correct year
            $isJanToMar = in_array('Jan', $this->month) || in_array('Feb', $this->month) || in_array('Mar', $this->month);
            $currentYear = $isJanToMar ? $f_year_array[1] : $f_year_array[0];

            // Get the first and last months from the array
            $firstMonth = $this->month[0];
            $lastMonth = $this->month[count($this->month) - 1];

            // Format the month and create start and end dates
            $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$firstMonth")->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$lastMonth")->endOfMonth();

            // Convert to date strings
            $startDateFormatted = $startDate->toDateString();
            $endDateFormatted = $endDate->toDateString();

            $startDate = Carbon::createFromFormat('Y-m-d', $startDateFormatted);
            $endDate = Carbon::createFromFormat('Y-m-d', $endDateFormatted);
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $monthName = $currentDate->format('F');
                if (!in_array($monthName, $this->months)) {
                    $this->months[] = $monthName;
                }
                $currentDate->addMonth()->startOfMonth();
            }
        } elseif ($this->financial_year && $this->financial_year != '' && $this->financial_year != null) {
            $f_year_array = explode('-', $this->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $startDate = Carbon::createFromFormat('Y-m-d', $financial_year_start);
            $endDate = Carbon::createFromFormat('Y-m-d', $financial_year_end);
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $monthName = $currentDate->format('F');
                if (!in_array($monthName, $this->months)) {
                    $this->months[] = $monthName;
                }
                $currentDate->addMonth()->startOfMonth();
            }
        } else {
            $currentDate = Carbon::now();
            $startDatethree = $currentDate->copy()->subMonthsNoOverflow(3)->firstOfMonth()->format('Y-m-d');
            $endDatethree = $currentDate->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d');
            $startDate = Carbon::createFromFormat('Y-m-d', $startDatethree);
            $endDate = Carbon::createFromFormat('Y-m-d', $endDatethree);
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $monthName = $currentDate->format('F');
                if (!in_array($monthName, $this->months)) {
                    $this->months[] = $monthName;
                }
                $currentDate->addMonth()->startOfMonth();
            }
        }

        $label2 = [];

        foreach ($this->months as $key => $value) {
            $label2[] = $value . ' QTY';
            $label2[] = $value . ' SALE';
        }

        $label3 = [
            'T-QTY',
            'T-SALE',
            'Qty Wise Cont %',
            'Value Wise Cont %'
        ];

        $headings = array_merge($label1, $label2, $label3);
        return $headings;
    }

    public function map($data): array
    {
        $invoice_dates = explode(',', $data->invoice_dates);
        $quantitys = explode(',', $data->quantitys);
        $net_amounts = explode(',', $data->net_amounts);
        $response[1] = $data->new_group;
        $response[0] = $data->final_branch;
        $indx = 0;
        foreach ($this->months as $k => $val) {
            $tsale = 0;
            $tqty = 0;
            foreach ($invoice_dates as $key => $value) {
                $invDate = Carbon::createFromFormat('Y-m-d', $value);
                $currentDate = $invDate->copy();
                $monthName = $currentDate->format('F');
                if ($monthName == $val) {
                    $tsale += $net_amounts[$key];
                    $tqty += $quantitys[$key];
                }
            }
            if ($tqty > 0) {
                $response[2 + $indx] = $tqty;
                $indx++;
            } else {
                $response[2 + $indx] = "0";
                $indx++;
            }
            if ($tsale > 0) {
                $response[2 + $indx] = number_format(($tsale / 100000), 2, '.', '');
                $indx++;
            } else {
                $response[2 + $indx] = "0";
                $indx++;
            }
        }

        $response[2 + $indx] = $data->total_quantitys;
        $response[3 + $indx] = number_format(($data->total_net_amounts / 100000), 2, '.', '');
        $response[4 + $indx] = number_format((($data->total_quantitys / $this->t_data[0]->total_quantitys) * 100), 2, '.', '') . "%";;
        $response[5 + $indx] = number_format((($data->total_net_amounts / $this->t_data[0]->total_net_amounts) * 100), 2, '.', '') . "%";;

        return $response;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();

                $event->sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
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
