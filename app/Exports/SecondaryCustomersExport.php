<?php

namespace App\Exports;

use App\Models\SecondaryCustomer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SecondaryCustomersExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize,
    WithEvents
{
    protected $filters;
    protected $type;
    protected $limit;
    protected $page;

    /** Users keyed by id, preloaded once so map() never queries per row. */
    protected $employees;

    public function __construct(array $filters, string $type)
    {
        $this->filters = $filters;
        $this->type = $type;
        $this->limit = isset($filters['export_limit'])
        ? (int) $filters['export_limit']
        : 1000;

    $this->page = isset($filters['export_page'])
        ? (int) $filters['export_page']
        : 1;

    $this->employees = collect();
    }

    public function collection()
    {
        $rows = SecondaryCustomer::select([
            'id',
            'type',
            'sub_type',
            'owner_name',
            'shop_name',
            'mobile_number',
            'whatsapp_number',
            'vehicle_segment',
            'sales_exception_assignment',
            'address_line',
            'belt_area_market_name',
            'country_id',
            'state_id',
            'district_id',
            'city_id',
            'pincode_id',
            'beat_id',
            'opportunity_status',
            'nistha_awareness_status',
            'saathi_awareness_status',
            'gps_location',
            'employee_id',
            'gmap', 
            'distributor_name',
            'created_by',
            'created_at',

            // ✅ Added for hyperlinks
            'owner_photo',
            'shop_photo',
            // 'gst_attachment',
            // 'pan_attachment',
        ])
        ->with([
            'country:id,country_name',
            'state:id,state_name',
            'district:id,district_name',
            'city:id,city_name',
            'pincode:id,pincode',
            'beat:id,beat_name',
            'creator:id,name',
            'distributor:id,trade_name,legal_name,distributor_code'
        ])
        ->where('type', $this->type)
        ->when(!empty($this->filters['owner_name']), fn($q) => $q->where('owner_name', 'like', '%' . $this->filters['owner_name'] . '%'))
        ->when(!empty($this->filters['shop_name']), fn($q) => $q->where('shop_name', 'like', '%' . $this->filters['shop_name'] . '%'))
        ->when(!empty($this->filters['mobile']), fn($q) => $q->where('mobile_number', 'like', '%' . $this->filters['mobile'] . '%'))
        ->when(!empty($this->filters['beat_id']), fn($q) => $q->where('beat_id', $this->filters['beat_id']))
        ->when(!empty($this->filters['state_id']), fn($q) => $q->where('state_id', $this->filters['state_id']))
        ->when(!empty($this->filters['city_id']), fn($q) => $q->where('city_id', $this->filters['city_id']))
        ->when(!empty($this->filters['opportunity_status']), fn($q) => $q->where('opportunity_status', $this->filters['opportunity_status']))
        ->when(!empty($this->filters['awareness_status']), function($q) {
            $status = $this->filters['awareness_status'] === 'Done' ? 'Done' : 'Not Done';
            if (in_array($this->type, ['RETAILER', 'WORKSHOP'])) {
                $q->where('nistha_awareness_status', $status);
            } else {
                $q->where('saathi_awareness_status', $status);
            }
        })
        ->when(!empty($this->filters['start_date']) && !empty($this->filters['end_date']), function ($q) {

            $startDate = Carbon::parse($this->filters['start_date'])
    ->format('Y-m-d');

            $endDate = Carbon::parse($this->filters['end_date'])
                ->format('Y-m-d');

            $q->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);
        })
        ->when(!empty($this->filters['start_date']) && empty($this->filters['end_date']), function ($q) {

            $startDate = Carbon::parse($this->filters['start_date'])
    ->format('Y-m-d');

            $q->whereDate('created_at', '>=', $startDate);
        })
        ->when(empty($this->filters['start_date']) && !empty($this->filters['end_date']), function ($q) {

            $endDate = Carbon::parse($this->filters['end_date'])
                ->format('Y-m-d');

            $q->whereDate('created_at', '<=', $endDate);
        })
        ->when(!empty($this->filters['global_search']), function ($q) {

            $search = $this->filters['global_search'];

            $q->where(function ($query) use ($search) {

                $query->where('owner_name', 'like', '%' . $search . '%')
                    ->orWhere('shop_name', 'like', '%' . $search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $search . '%');

            });
        })
        ->latest()
        ->offset(($this->page - 1) * $this->limit)
        ->limit($this->limit)
        ->get();

        $this->loadEmployees($rows);

        return $rows;
    }

    /**
     * employee_id holds a comma separated list of user ids. Resolve all of them
     * in a single query up front, so map() can read both the name and the code.
     */
    private function loadEmployees($rows): void
    {
        $ids = $rows->flatMap(fn($row) => $this->employeeIds($row))->unique()->values();

        if ($ids->isEmpty()) {
            return;
        }

        $this->employees = \App\Models\User::whereIn('id', $ids)
            ->get(['id', 'name', 'employee_codes'])
            ->keyBy('id');
    }

    private function employeeIds($row)
    {
        return collect(explode(',', (string) $row->employee_id))
            ->map(fn($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values();
    }

    public function headings(): array
    {
        return [
            'ID', 
            'Type',
            'Sub Type', 
            'Owner Name', 
            'Shop Name', 
            'Mobile Number',
            'WhatsApp Number', 
            'Vehicle Segment', 
            'Sales Exception Assignment',
            'Distributor Name',
            'Distributor ID',
            'Address Line', 
            'Belt Area Market Name', 
            'Country', 
            'Country ID',
            'State', 
            'State ID', 
            'District', 
            'District ID', 
            'City', 
            'City ID', 
            'Pincode', 
            'Pincode ID', 
            'Beat', 
            'Beat ID', 
            'Opportunity Status', 
            'Awareness Status',
            'Employee Name',
            'Employee Code',
            'Created By',
            'Gmap Address',
            'GPS Location',
            'Created At',

            // ✅ New Columns for Hyperlinks
            'Owner Photo',
            'Shop Photo',
            // 'GST Attachment',
            // 'PAN Attachment',
        ];
    }

    public function map($row): array
    {
        // Employee Logic
        $employees = $this->employeeIds($row)
            ->map(fn($id) => $this->employees->get($id))
            ->filter();

        // Keep both lists positionally aligned, so the Nth code belongs to the Nth name.
        $employeeNames = $employees->isEmpty()
            ? '-'
            : $employees->map(fn($user) => filled($user->name) ? Str::title($user->name) : '-')->implode(', ');

        $employeeCodes = $employees->isEmpty()
            ? '-'
            : $employees->map(fn($user) => filled($user->employee_codes) ? $user->employee_codes : '-')->implode(', ');

        $awarenessLabel = in_array($row->type, ['RETAILER', 'WORKSHOP']) ? 'Nistha' : 'Saathi';
        $awarenessStatus = in_array($row->type, ['RETAILER', 'WORKSHOP'])
            ? ($row->nistha_awareness_status ?? 'Not Done')
            : ($row->saathi_awareness_status ?? 'Not Done');

        return [
            $row->id,
            $row->type ?? '-',
            $row->sub_type ?? '-',
            $row->owner_name ?? '-',
            $row->shop_name ?? '-',
            $row->mobile_number ?? '-',
            $row->whatsapp_number ?? '-',
            is_array($row->vehicle_segment) ? implode(', ', $row->vehicle_segment) : ($row->vehicle_segment ?? '-'),
            $row->sales_exception_assignment ?? '-',
            $row->distributor?->trade_name ?? $row->distributor?->legal_name ?? '-',
            $row->distributor?->id ?? '-',
            $row->address_line ?? '-',
            $row->belt_area_market_name ?? '-',
            $row->country?->country_name ?? '-',
            $row->country_id ?? '-',
            $row->state?->state_name ?? '-',
            $row->state_id ?? '-',
            $row->district?->district_name ?? '-',
            $row->district_id ?? '-',
            $row->city?->city_name ?? '-',
            $row->city_id ?? '-',
            $row->pincode?->pincode ?? '-',
            $row->pincode_id ?? '-',
            $row->beat?->beat_name ?? '-',
            $row->beat_id ?? '-',
            $row->opportunity_status ?? '-',
            $awarenessLabel . ': ' . $awarenessStatus,
            $employeeNames,
            $employeeCodes,
            $row->creator?->name ?? '-',
            $row->gmap ?? '-',
            $row->gps_location ?? '-',
            $row->created_at ? $row->created_at->format('d-m-Y') : '-',

            // ✅ Hyperlinks
            $this->getHyperlink($row->owner_photo, 'View Owner Photo'),
            $this->getHyperlink($row->shop_photo, 'View Shop Photo'),
            // $this->getHyperlink($row->gst_attachment, 'View GST Attachment'),
            // $this->getHyperlink($row->pan_attachment, 'View PAN Attachment'),
        ];
    }

    /**
     * Generate Clickable Hyperlink
     */
    private function getHyperlink(?string $path, string $displayText): string
    {
        if (empty($path)) {
            return '-';
        }

        $path = ltrim($path, '/');
        $fullUrl = 'https://elofic.fieldkonnect.io/public/storage/' . $path;

        return '=HYPERLINK("' . $fullUrl . '", "' . $displayText . '")';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Header Styling (Now up to Column AJ)
                $event->sheet->getStyle('A1:AJ1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE0E0E0'],
                    ],
                ]);

                // Optional: Make hyperlink columns blue & underlined
                $lastRow = $event->sheet->getHighestRow();
                $event->sheet->getStyle('AG2:AJ' . $lastRow)
                    ->getFont()
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0000FF')) // Blue
                    ->setUnderline(true);
            },
        ];
    }
}
