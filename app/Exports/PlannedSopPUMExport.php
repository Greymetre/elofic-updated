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
use App\Models\PrimarySales;
use App\Models\Product;

class PlannedSopPUMExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    protected $filters;
    protected $year;

    public function __construct(Request $request)
    {
        $this->filters = $request->all();
    }

   public function collection()
    {
        $start_date = "";
        $end_date   = "";
        if(isset($this->filters['financial_year'])){
            $this->year = explode('-',$this->filters['financial_year']);
            $start_date = $this->year[0].'-04-01';
            $end_date   = $this->year[1].'-03-31';
        }


        $data = PlannedSOP::with(['getProduct.subcategories' , 'getProduct.productdetails', 'getProduct.categories', 'getBranch']);       
        if (isset($start_date) && isset($end_date)) {
            $data->whereBetween('planning_month', [$start_date, $end_date]);
        }
        foreach ($this->filters as $key => $value) {
               if (isset($value))  {
                switch ($key) {
                    case "created_by" : 
                    case 'top_sku':
                        $data->where($key, 'like', "%$value%");
                        break;
                    case "product_name" :
                    case "description": 
                    case "product_code":
                        $data->whereHas('getProduct', function ($q) use ($value , $key) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                    case "branch_name":
                        $data->whereHas('getBranch', function ($q) use ($value , $key) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                    case "division_id" : 
                        $data->whereHas('getProduct.categories', function ($q) use ($value) {
                            $q->where('id', $value);
                        });
                        break;
                    case "group_name" : 
                        $data->whereHas('getProduct.subcategories', function ($q) use ($value) {
                            $q->where('subcategory_name', 'like', "%$value%");
                        });
                        break;
                    case 'plan_next_month':
                    case 'budget_for_month':
                    case 'last_month_sale':
                    case 'last_three_month_avg':
                    case 'last_year_month_sale':
                    case 'sku_unit_price':
                    case 's_op_val':
                    case 'status':
                     $data->where($key, 'like', "$value");
                     break;
                }
            }
        }
        if(isset($this->filters['planning_month'])){
          try{
            $formatted_date = Carbon::createFromFormat('F Y', $this->filters['planning_month'])->startOfMonth();
            $planning_month = $formatted_date->format("Y-m-d");
            $data->whereDate('planning_month' , $planning_month);
          }catch(\Exception $e){
             $data->latest()->get();
          }
        }
        return $data->latest()->get();
    }

    public function headings(): array
    {
        if(isset($this->filters['financial_year'])){
            $this->year = explode('-',$this->filters['financial_year']);
        }
        $heading1 = ["Order Id" , "Planing Month","Branch Name","Division Name", "Group Name", "Sap Code", "Product Name","Sale : Quantity","","","","","","","","","","","","","","","CY Opening Stock (Branch)","","Open Order (Prod.)","","Forecast (Sales Plan)","","For Prodcution", "" ,'Created By', 'Verify By' ,'Created At'];

        $heading2 = ["","","","","","",""];
        for ($i=4; $i <= 12 ; $i++) {
            $month = ($i < 10 ? '0' . $i : $i) . '/' . ($this->year[0] - 1);            
            array_push($heading2, $month);
        }
        for ($i=1; $i <= 3 ; $i++) {
            $month = '0'.$i.'/'.($this->year[0]);
            array_push($heading2, $month);
        }

        $heading2 = array_merge($heading2, ['Min', "Max", "Avg", 'CY Stk Qty', 'CY Value',
            'Open Order Qty', 'Open Order Value', 'Forecast Qty', 'Forecast Value','For Prodcution qty' , 'For Prodcution Value']);


        $headings = array($heading1, $heading2); // Merge arrays
        return $headings;
    }


    public function map($data): array
    {
        if(isset($this->filters['financial_year'])){
            $this->year = explode('-',$this->filters['financial_year']);
        }
        $startYear = $this->year[0] - 1;
        $endYear = $startYear + 1;
        $months = [];
        for ($m = 4; $m <= 12; $m++) {
            $months["$startYear-" . str_pad($m, 2, '0', STR_PAD_LEFT)] = 0;
        }
        for ($m = 1; $m <= 3; $m++) {
            $months["$endYear-" . str_pad($m, 2, '0', STR_PAD_LEFT)] = 0;
        }


        $salesData = PrimarySales::where(['product_id'=> $data->product_id , 'branch_id' => $data->branch_id])
                ->whereBetween('invoice_date', ["$startYear-04-01", "$endYear-03-31"])
                ->selectRaw('DATE_FORMAT(invoice_date, "%Y-%m") as month, SUM(quantity) as total_qty')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total_qty', 'month')
                ->toArray();

        $salesByMonth = array_merge($months, $salesData);
        $salesValues = array_filter($salesByMonth, function ($value) {
            return $value > 0; // Exclude zero values
        });
        $min = count($salesValues) > 0 ? min($salesValues) : 0;
        $max = count($salesValues) > 0 ? max($salesValues) : 0;
        $avg = count($salesValues) > 0 ? round(array_sum($salesValues) / count($salesByMonth), 2) : 0;
        $result = (int) ($data['plan_next_month'] ?? 0) - (int) ($data['dispatch_against_plan'] ?? 0);

        $opening_stock = isset($data['opening_stock']) && is_numeric($data['opening_stock']) 
            ? (int) $data['opening_stock'] 
            : 0;

        $open_order_stock = isset($data['open_order_qty']) && is_numeric($data['open_order_qty']) 
            ? (int) $data['open_order_qty'] 
            : 0;

        $production_qty = isset($data['production_qty']) && is_numeric($data['production_qty']) 
            ? (int) $data['production_qty'] 
            : 0;

        $product_price = isset($data['getProduct']['productdetails'][0]['price']) && is_numeric($data['getProduct']['productdetails'][0]['price']) 
            ? (float) $data['getProduct']['productdetails'][0]['price']
            : 0;

        $new_price = ($product_price*41)/100;
        $product_price = $product_price - $new_price;

        $opening_stock_value = $product_price * $opening_stock;
        $openderValue        = $product_price * $open_order_stock;
        $production_value    = $product_price * abs($production_qty); 
        $plan_next_month = isset($data['plan_next_month']) && is_numeric($data['plan_next_month']) 
            ? (int) $data['plan_next_month'] 
            : 0;

        $plan_next_month_value = $product_price * $plan_next_month;
        return array_merge([
            $data['order_id'] ?? '',
            isset($data['planning_month']) ? \Carbon\Carbon::parse($data['planning_month'])->format('F Y') : '',
            $data['getBranch']['branch_name'] ?? '',
            $data['getProduct']['categories']['category_name'] ?? '',
            $data['getProduct']['subcategories']['subcategory_name'] ?? '',
            $data['getProduct']['sap_code'] ?? '',
            $data['getProduct']['product_name'] ?? ''
        ], array_values($salesByMonth), [  // Append sales values dynamically
            $min, // Minimum value (excluding zero)
            $max, // Maximum value
            $avg,
            $opening_stock ?? "0",
            $opening_stock_value ?? "0",
            $open_order_stock ?? "0",
            $openderValue ?? "0",
            $plan_next_month ?? "0",
            $plan_next_month_value ?? "0",
            $production_qty ?? "0",
            $production_value ?? "0",
            $data['created_by'] ?? '',
            $data['verify_by']  ?? '',
            isset($data['created_at']) ? \Carbon\Carbon::parse($data['created_at'])->format('d-m-Y H:i:s') : '',
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestDataRow();
                $lastColumn = $sheet->getHighestDataColumn();

                // Merge Cells for Headers
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:D2');
                $sheet->mergeCells('E1:E2');
                $sheet->mergeCells('F1:F2');
                $sheet->mergeCells('G1:G2');
                $sheet->mergeCells('H1:V1');
                $sheet->mergeCells('W1:X1');
                $sheet->mergeCells('Y1:Z1');
                $sheet->mergeCells('AA1:AB1');
                $sheet->mergeCells('AC1:AD1');
                $sheet->mergeCells('AE1:AE2');
                $sheet->mergeCells('AF1:AF2');
                $sheet->mergeCells('AG1:AG2');


                // Style for First Row (Header)
                $firstRowRange = 'A1:' . $lastColumn . '2';
                $sheet->getRowDimension(1)->setRowHeight(40); // Increase Header Row Height
                $sheet->getRowDimension(2)->setRowHeight(40);
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

                // Apply Text Alignment and Border to All Rows
                $allRowsRange = 'A1:' . $lastColumn . $lastRow;
                $event->sheet->getStyle($allRowsRange)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Center text
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Center vertically
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Set Row Height for All Rows
                for ($i = 3; $i <= $lastRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(30); // Increase row height for better readability
                }
            },
        ];
    }

}
