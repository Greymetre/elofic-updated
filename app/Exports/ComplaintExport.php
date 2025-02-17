<?php

namespace App\Exports;

use App\Models\Complaint;
use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\User;
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

class ComplaintExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    protected $user_id;
    protected $start_date;
    protected $end_date;
    public function __construct(Request $request)
    {
        $this->user_id = $request->input('user_id');
        $this->start_date = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
        $this->end_date = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
    }

    public function collection()
    {

        $query = Complaint::with([
            'party',
            'service_center_details',
            'customer',
            'complaint_type_details',
            'product_details.categories',
            'division_details',
            'complaint_time_line', // Ensure this is included
            'service_bill',
            'purchased_branch_details',
            'product_details.categories',
            'createdbyname',
            'complaint_work_dones',
        ]);

        if ($this->start_date && $this->end_date) {
            $query->whereBetween('created_at', [$this->start_date, $this->end_date]);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Complaint Number',
            'Service Center Name',
            'Service Center Code',
            'Seller Name',
            'Customer Name',
            'Customer Email',
            'Customer Number',
            'Address',
            'Place',
            'Pincode',
            'Country',
            'State',
            'City',
            'Customer Complaint Type', // Keep the reference point
            
            // Newly added fields
            'Division',
            'Product Name',
            'Product Code',
            'Product Category',
            'Product Serial No',
            'HP',
            'Stage',
            'Phase',
            'Warranty Customer Bill Date',
            'Service Paid/Free',
            'Work Done Time',
            'Action Done By ASC',
            'Service Center Remark',
            'Last Status Update Time',
            'Pending TAT',
            'Open TAT',
            'Cancelled TAT',
            'Work Done TAT',
            'Completed TAT',
            'Close TAT',

            // Remaining existing fields
            'Customer Number',
            'End User',
            'Complaint Date',
            'Complaint Type',
            'Purchased Party Name',
            'Service Center',
            'User Assign',
            'Product Category',
            'Product Serial Number',
            'Product Code',
            'Complaint Status',
        ];
    }


    public function map($data): array
    {
        if($data->complaint_status == '0'){
            $status = 'Open';
        }elseif($data->complaint_status == '1'){
            $status = 'Pending';
        }elseif($data->complaint_status == '2'){
            $status = 'Work Done';
        }elseif($data->complaint_status == '3'){
            $status = 'Completed';
        }elseif($data->complaint_status == '4'){
            $status = 'Closed';
        }elseif($data->complaint_status == '5'){
            $status = 'Cancel';
        }


        return [
            getDateInIndFomate($data['complaint_date']) ?? '',
            $data['complaint_number'] ?? '',
            $data['service_center_details']['name'] ?? '',
            $data['service_center_details']['customer_code'] ?? '',
            $data['seller'] ?? '',
            $data['customer']['customer_name'] ??  '',
            $data['customer']['customer_email']?$data['customer']['customer_email'] : '',
            $data['customer']['customer_number']?$data['customer']['customer_number'] : '',
            $data['customer']['customer_address']?$data['customer']['customer_address'] : '',
            $data['customer']['customer_place']?$data['customer']['customer_place'] : '',
            getPincode($data['customer']['customer_pindcode'])?? '',
            $data['customer']['customer_country']?$data['customer']['customer_country'] : '',
            $data['customer']['customer_state']?$data['customer']['customer_state'] : '',
            $data['customer']['customer_city']?$data['customer']['customer_city'] : '',
            $data['complaint_type_details']['name'] ??  '',
            $data['product_details']['categories']['category_name'] ??  '',
            $data['product_details']['product_name'] ??  '',
            $data['product_details']['product_code'] ??  '',
            $data['product_details']['categories']['category_name'] ??  '',
            $data['product_serail_number'] ??  '',
            $data['product_details']['specification'] ??  '',
            $data['product_details']['product_no'] ??  '',
            $data['product_details']['phase'] ??  '',
            $data['customer_bill_date'] ??  '',
            $data['service_type'] ??  '',
            isset($data['complaint_time_line']->where('status',2)->sortByDesc('id')->first()->created_at) ?Carbon::parse($data['complaint_time_line']->where('status',2)->sortByDesc('id')->first()->created_at)->format('d-m-Y h:i a') : '',
            $data['customer']?$data['customer']['name'] : '',
            $data['complaint_date'] ?date("d/M/Y", strtotime($data->complaint_date)):'',
            $data['complaint_type_details']['name'] ?? '',
            $data['party']['name'] ?? '',
            $data['service_center_details']['name'] ?? '',
            $data['assign_user_details']['name'] ?? '',
            $data['category'] ?? '',
            $data['product_serail_number'] ?? '',
            $data['product_code'] ?? '',
            $status,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 1;
              
                $event->sheet->getStyle('A1:Z1')->applyFromArray([
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
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'], // Border color
                        ],
                    ],
                ]);
            },
        ];
    }
}
