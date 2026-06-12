<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Division;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\{CustomerOutstanting, ParentDetail, TransactionHistory, Redemption, MobileUserLoginDetails};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class CutomerOutstantingExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        DB::statement("SET SESSION group_concat_max_len = 100000000");
        $data = CustomerOutstanting::select(
            'posting_date',
            'customer_code',
            'customer_id',
            'reference',
            'customer_name',
            'due_date',
            'payment_term',
            

            DB::raw('SUM(amount) as total_amount'),

            DB::raw("SUM(CASE WHEN days='0-30' THEN amount ELSE 0 END) as slot_0_30"),
            DB::raw("SUM(CASE WHEN days='31-60' THEN amount ELSE 0 END) as slot_31_60"),
            DB::raw("SUM(CASE WHEN days='61-90' THEN amount ELSE 0 END) as slot_61_90"),
            DB::raw("SUM(CASE WHEN days='91-120' THEN amount ELSE 0 END) as slot_91_120"),
            DB::raw("SUM(CASE WHEN days='121-150' THEN amount ELSE 0 END) as slot_121_150"),
            DB::raw("SUM(CASE WHEN days='151-180' THEN amount ELSE 0 END) as slot_151_180"),
            DB::raw("SUM(CASE WHEN days='181-210' THEN amount ELSE 0 END) as slot_181_210"),
            DB::raw("SUM(CASE WHEN days IN ('210','210+') THEN amount ELSE 0 END) as slot_210_plus")
        );
        
        if($this->request->customer_id && !empty($this->request->customer_id)){
            $data->where('customer_id', $this->request->customer_id);                
        }
        if($this->request->branch_id && !empty($this->request->branch_id)){
            $data->where('branch_id', $this->request->branch_id);                
        }
        if($this->request->division_id && !empty($this->request->division_id)){
            $data->where('division_id', $this->request->division_id);                
        }
        if ($this->request->balance_date) {
            $data->whereDate('posting_date', $this->request->balance_date);
        }
        
        $data = $data->groupBy(
            'posting_date',
            'customer_code',
            'customer_id',
            'reference',
            'customer_name',
            'due_date',
            'payment_term'
        )->get();

        return $data;
    }

    public function headings(): array
    {
        return [
            'Posting Date',
            'Customer Code',
            // 'Customer ID',
            'Reference',
            'Customer Name',
            'Ageing Days',
            'Due Date',
            
            '0-30',
            '31-60',
            '61-90',
            '91-120',
            '121-150',
            '151-180',
            '181-210',
            '210+',
            'Total Amount',
            'Payment Term'
        ];
    }




    public function map($data): array
    {

        $ageingDays = '-';

        if (!empty($data->due_date)) {
            $ageingDays = Carbon::today()->diffInDays(
                Carbon::parse($data->due_date),
                false
            ) * -1;
        }
        return [
            $data->posting_date ?? '-',
            $data->customer_code ?? '-',
            // $data->customer_id ?? '-',
            $data->reference ?? '-',
            $data->customer_name ?? '-',
            $ageingDays,
            $data->due_date ?? '-',
            

            

            $data->slot_0_30 ?? 0,
            $data->slot_31_60 ?? 0,
            $data->slot_61_90 ?? 0,
            $data->slot_91_120 ?? 0,
            $data->slot_121_150 ?? 0,
            $data->slot_151_180 ?? 0,
            $data->slot_181_210 ?? 0,
            $data->slot_210_plus ?? 0,
            $data->total_amount ?? 0,
            $data->payment_term ?? 0,
        ];
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
