<?php

namespace App\Exports;

use App\Models\City;
use App\Models\Customers;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderDetails;
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


class FOSRatingReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
        $this->designation_id = $request->input('designation_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
        $this->srno = 0;
    }

    public function collection()
    {
        $user_ids = getUsersReportingToAuth();
        $query = User::with('reportinginfo', 'getbranch', 'getdivision', 'getdesignation', 'all_attendance_details', 'visits', 'customers', 'userinfo', 'cities');
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
        $query = $query->where('sales_type', 'Secondary')->latest()->get();

        return $query;
    }

    public function headings(): array
    {
        return ['S No', 'Emp Code', 'FOS Name', 'Area Of Operation (Districts Covered)', 'Date of Appointment', 'Yesterday Productivity vs Visit', 'Yesterday Retailer Order Count', 'Yesterday Retailer Order Value in Lacs', 'Yesterday Counter Visit', 'Weekly Visit Count (Last 7 Day)', 'Order Value Jun\'24 (in Lacs After 35%)', 'Total Retailer Registred Fieldkonnect Jun\'24 (Nos)', 'Total Retailer Visited Fieldkonnect Jun\'24 (Nos)', 'Total New Retailer (First_TimeOrder) (Nos)', 'Total Order Value in Lacs After 35%', 'Total No of Field working days till date', 'Average Per day sale', 'Sale Index', 'Total Retailer Registred Till Date Fieldkonnect (Nos)', 'Average No of RetailersRegistered /day', 'Registration Index', 'Total Visited Till Date Fieldkonnect (Nos)', 'Average No of Retailers visited /day', 'Visit Index', 'Total Retailer Registered under Saarthi (Nos)', 'Average No of Retailers Registered under Sarathi / day', 'Sarathi Registration Index', 'Total Active Retailer under Saarthi (Nos)', 'Average No of Activation under Sarathi /day', 'Activation Index', 'Total New Retailer  Activated (Order) (Nos)', 'Performance Rating', 'Total Mobile App Downloaded', 'Total Active Retailer', 'Total Coupon Scan Count', 'Total Points', 'Total Unique Redemption', 'Total Redemption Value', 'last Week Performance Rating'];
    }

    public function map($query): array
    {
        $currentDate = Carbon::now();
        $dateBeforeSixDays = $currentDate->subDays(6)->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        $dis_ids = City::whereIn('id', $query->cities->pluck('city_id')->toArray())->pluck('district_id');
        $retailers = Customers::where('customertype', '2')->pluck('id');
        $order_counts = Order::where(['order_date' => $yesterday, 'created_by' => $query->id])->whereIn('buyer_id', $retailers)->count('id');
        $order_value = Order::where(['order_date' => $yesterday, 'created_by' => $query->id])->whereIn('buyer_id', $retailers)->sum('sub_total');
        $yesterday_visit = $query->visits->where('checkin_date', $yesterday)->count('id');
        $weekly_visit = $query->visits->where('checkin_date', '>=', $dateBeforeSixDays)->count('id');
        if ($order_counts < 1) {
            $yesterday_productivity_visit = "0.00%";
        } else {
            $productvity = number_format((($order_counts / $yesterday_visit) * 100), 2);
            $yesterday_productivity_visit = $productvity . "%";
        }
        $month_order_value = Order::where('order_date', '>=', date('Y-m-01'))->where('created_by', $query->id)->sum('sub_total');
        $month_registered_retailers = Customers::where('created_at', '>=', date('Y-m-01'))->where(['customertype' => '2', 'created_by' => $query->id])->count('id');
        $month_visit = $query->visits->where('checkin_date', '>=', date('Y-m-01'))->whereIn('customer_id', $retailers)->count('id');
        $this_month_visit_unique = $query->visits->where('checkin_date', '>=', date('Y-m-01'))->whereIn('customer_id', $retailers)->groupBy('customer_id');
        $old_visit_unique = $query->visits->where('checkin_date', '<', date('Y-m-01'))->whereIn('customer_id', $retailers)->groupBy('customer_id');
        if($query->id != 584 && count($old_visit_unique) > 0){
            dd($this_month_visit_unique, $old_visit_unique, $query->id);
        }
        return [
            ++$this->srno,
            $query['employee_codes'] ?? '',
            $query['name'] ?? '',
            // implode(", ", District::whereIn('id', $dis_ids)->pluck('district_name')->toArray()),
            '',
            date('d M y', strtotime($query->userinfo->date_of_joining)),
            $yesterday_productivity_visit,
            $order_counts>0?$order_counts:"0",
            $order_value>0?number_format(($order_value/100000),2):"0.00",
            $yesterday_visit>0?$yesterday_visit:"0",
            $weekly_visit>0?$weekly_visit:"0",
            $month_order_value>0?number_format(($month_order_value/100000),2):"0.00",
            $month_registered_retailers>0?$month_registered_retailers:"0",
            $month_visit>0?$month_visit:"0",
            // $monthly_visit_unique>0?$monthly_visit_unique:"0",

        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $event->sheet->mergeCells('A' . $lastRow . ':F' . $lastRow);

                $event->sheet->getStyle('A1:AA1')->applyFromArray([
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

                $event->sheet->getStyle('A' . $lastRow . ':AA' . $lastRow)->applyFromArray([
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
                $event->sheet->setCellValue('A' . $lastRow, 'Total');
                $event->sheet->setCellValue('G' . $lastRow, '=SUM(G3:G' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('H' . $lastRow, '=SUM(H3:H' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('I' . $lastRow, '=SUM(I3:I' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('J' . $lastRow, '=SUM(J3:J' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('K' . $lastRow, '=SUM(K3:K' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('L' . $lastRow, '=SUM(L3:L' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('M' . $lastRow, '=SUM(M3:M' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('N' . $lastRow, '=SUM(N3:N' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('O' . $lastRow, '=SUM(O3:O' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('P' . $lastRow, '=SUM(P3:P' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('Q' . $lastRow, '=SUM(Q3:Q' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('R' . $lastRow, '=SUM(R3:R' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('S' . $lastRow, '=SUM(S3:S' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('T' . $lastRow, '=SUM(T3:T' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('U' . $lastRow, '=SUM(U3:U' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('V' . $lastRow, '=SUM(V3:V' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('W' . $lastRow, '=SUM(W3:W' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('X' . $lastRow, '=SUM(X3:X' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('Y' . $lastRow, '=SUM(Y3:Y' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('Z' . $lastRow, '=SUM(Z3:Z' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('AA' . $lastRow, '=SUM(AA3:AA' . ($lastRow - 2) . ')');
            },
        ];
    }
}
