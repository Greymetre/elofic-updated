<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasterDistributorsTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            array_map(fn ($heading) => match ($heading) {
                'ID' => '',
                'Category' => 'Diamond/Platinum/Gold/Silver/Bronze',
                'Business Status' => 'Active/Inactive/On Hold',
                'Business Start Date' => '2026-01-01',
                'Mobile', 'Alternate Mobile' => '0000000000',
                'Email', 'Secondary Email' => 'user@example.com',
                'Shipping Address 1' => 'address 1',
                'Sales Zone', 'Area Territory' => 'Northwest/Northeast/HO/Central/North/South/East/West',
                'Market Classification' => 'Urban/Rural/Semi-Urban',
                'Registration Type' => 'Proprietorship/Partnership/Pvt Ltd/LLP',
                'Sales Executive ID (JSON)' => '41926, 41931',
                'Supervisor ID' => '41926',
                'Customer Segment' => '2W/3W/LCV/HCV/Tractor',
                'Shop Image', 'Profile Image', 'Documents', 'Cancelled Cheque',
                'Sales Executive Names', 'Supervisor Name', 'Created At', 'Updated At' => '',
                default => '-',
            }, $this->headings()),
        ]);
    }

    public function headings(): array
    {
        $export = new MasterDistributorsExport(collect([
            (object) ['shipping_address' => ['address 1']],
        ]));

        return $export->headings();
    }
}
