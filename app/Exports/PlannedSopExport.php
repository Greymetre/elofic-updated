<?php

namespace App\Exports;

use App\Models\PlannedSOP;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlannedSopExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    protected $filters;

    public function __construct(Request $request)
    {
        $this->filters = $request->all();
    }

   public function collection()
    {
        $query = PlannedSOP::with(['getProduct.subcategories' , 'getProduct.categories', 'getBranch']);
        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
             "Branch Name", "Group Name",  "Item Name" ,  "Product Desc." ,  "Opening stock as on 1st (Qty)",   "S&OP Plan for Next running month (M+1) (Qty.)" ,  "Budget for the month (Qty.)", "LM Sale (Qty.)" , "L3M Avg Sale (Qty.)" ,"LY same month sale (Qty.)" ,  "SKU Unit Price" , "S&OP Val_L (Unit Price *Qty.)"  , "TOP 20 SKU for the Branch (*)",   "Created at"
        ];
    }


    public function map($data): array
    {
        return [
            $data['getBranch']['branch_name'] ?? '',
            $data['getProduct']['subcategories']['subcategory_name'] ?? '',
            $data['getProduct']['categories']['category_name'] ?? '',
            $data['getProduct']['description'] ?? '',
            $data['opening_stock'] ?? '',
            $data['plan_next_month'] ?? '',
            $data['budget_for_month'] ?? 0,
            $data['last_month_sale'] ?? 0,
            $data['last_three_month_avg'] ?? 0,
            $data['last_year_month_sale'] ?? 0,
            $data['sku_unit_price'] ?? '',
            $data['s_op_val'] ?? '',
            $data['top_sku'] ?? '',
            $data['created_by'] ?? '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestDataRow();
                $lastColumn = $sheet->getHighestDataColumn();

                $firstRowRange = 'A1:' . $lastColumn . '1';
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getStyle($firstRowRange)->getAlignment()->setWrapText(true);
                $sheet->getStyle($firstRowRange)->getFont()->setSize(14);

                $event->sheet->getStyle($firstRowRange)->applyFromArray([
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
                        'startColor' => ['rgb' => '00aadb'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->getStyle('A1:' . $lastColumn . '' . $lastRow)->applyFromArray([
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
