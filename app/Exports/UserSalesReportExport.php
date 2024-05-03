<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class UserSalesReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
        $this->designation_id = $request->input('designation_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
    }

    public function collection()
    {

        $query = User::with('reportinginfo', 'getbranch', 'getdivision', 'getdesignation', 'all_attendance_details', 'visits', 'customers');
        if ($this->user_id && $this->user_id != '' && $this->user_id != NULL) {
            $query->where('id', $this->user_id);
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
        $query = $query->latest()->get();

        return $query;
    }

    public function headings(): array
    {
        return ['Employees Code', 'User Name', 'Designation', 'Branch Name', 'Division', 'Reporting Manager', 'Field Working Days', 'Other Days', 'Total Days', 'Distributor Visit Total', 'Distributor Visit Unique', 'Dealer Visit Total', 'Dealer Visit Unique', 'Retailer Visit Total', 'Retailer Visit Unique', 'Service Center Visit Total', 'Service Center Visit Unique', 'Influencer Visit Total', 'Influencer Visit Unique', 'Total Visit', 'Total Visit Unique', 'Distributor New Registration', 'Dealer New Registration', 'Retailer New Registration', 'Service Center New Registration', 'Influencer-New Registration', 'Total New Registration'];
    }

    public function map($data): array
    {


        return [
            $data['employee_codes'] ?? '',
            $data['name'] ?? '',
            $data['getdesignation']['designation_name'] ?? '',
            $data['getbranch']['branch_name'] ?? '',
            $data['getdivision']['division_name'] ?? '',
            $data['reportinginfo']['name'] ?? '',
            (count($data->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Leave', 'Holiday'])->whereBetween('punchin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Leave', 'Holiday'])->whereBetween('punchin_date', [$this->start_date, $this->end_date])) : '0',
            (count($data->all_attendance_details->whereIn('working_type', ['Office Work', 'Leave', 'Holiday'])->whereBetween('punchin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->all_attendance_details->whereIn('working_type', ['Office Work', 'Leave', 'Holiday'])->whereBetween('punchin_date', [$this->start_date, $this->end_date])) : '0',
            (count($data->all_attendance_details->whereBetween('punchin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->all_attendance_details->whereBetween('punchin_date', [$this->start_date, $this->end_date])) : '0',
            (count($data->visits->where('customers.customertype', '1')->whereBetween('created_at', [$this->start_date, $this->end_date])) > 0) ? count($data->visits->where('customers.customertype', '1')->whereBetween('created_at', [$this->start_date, $this->end_date])) : '0',
            (count($data->visits->where('customers.customertype', '1')->groupBy('customers.id')) > 0) ? count($data->visits->where('customers.customertype', '1')->groupBy('customers.id')) : '0',
            (count($data->visits->where('customers.customertype', '3')) > 0) ? count($data->visits->where('customers.customertype', '3')) : '0',
            (count($data->visits->where('customers.customertype', '3')->groupBy('customers.id')) > 0) ? count($data->visits->where('customers.customertype', '3')->groupBy('customers.id')) : '0',
            (count($data->visits->where('customers.customertype', '2')) > 0) ? count($data->visits->where('customers.customertype', '2')) : '0',
            (count($data->visits->where('customers.customertype', '2')->groupBy('customers.id')) > 0) ? count($data->visits->where('customers.customertype', '2')->groupBy('customers.id')) : '0',
            (count($data->visits->where('customers.customertype', '4')) > 0) ? count($data->visits->where('customers.customertype', '4')) : '0',
            (count($data->visits->where('customers.customertype', '4')->groupBy('customers.id')) > 0) ? count($data->visits->where('customers.customertype', '4')->groupBy('customers.id')) : '0',
            (count($data->visits->where('customers.customertype', '5')) > 0) ? count($data->visits->where('customers.customertype', '5')) : '0',
            (count($data->visits->where('customers.customertype', '5')->groupBy('customers.id')) > 0) ? count($data->visits->where('customers.customertype', '5')->groupBy('customers.id')) : '0',
            (count($data->visits) > 0) ? count($data->visits) : '0',
            (count($data->visits->groupBy('customers.id')) > 0) ? count($data->visits->groupBy('customers.id')) : '0',
            (count($data->customers->where('customertype', '1')) > 0) ? count($data->customers->where('customertype', '1')) : '0',
            (count($data->customers->where('customertype', '3')) > 0) ? count($data->customers->where('customertype', '3')) : '0',
            (count($data->customers->where('customertype', '2')) > 0) ? count($data->customers->where('customertype', '2')) : '0',
            (count($data->customers->where('customertype', '4')) > 0) ? count($data->customers->where('customertype', '4')) : '0',
            (count($data->customers->where('customertype', '5')) > 0) ? count($data->customers->where('customertype', '5')) : '0',
            (count($data->customers) > 0) ? count($data->customers) : '0'
        ];
    }
}
