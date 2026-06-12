<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasterDistributorsTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            [
                '650',
                '-',
                '-',
                '-',
                'Diamond/Platinum/Gold/Silver/Bronze',

                'Active/Inactive/On Hold',
                '2026-01-01',

                '-',
                '-',
                '0000000000',
                '0000000000',
                'user@example.com',
                'user@example.com',

                '-',
                'INDORE',
                '2972',
                'Indore',
                '334',
                'Madhya Pradesh',
                '1',
                'India',
                '1',
                '452001',
                '10727',

                'address 1',

                'Northwest/ Northeast/ HO/ Central/ North/ South/ East/ West',
                'Northwest/ Northeast/ HO/ Central/ North/ South/ East/ West',
                '-',
                '-',

                'Urban/ Rural/ Semi-Urban',
                '-',
                '-',
                '-',
                'Proprietorship/ Partnership/ Pvt Ltd/ LLP',

                '-',
                '-',
                '-',
                '-',
                '-',

                '-',
                '-',
                '-',
                '-',
                '-',

                '-',
                '-',
                '-',
                '-',

                '41926, 41931',

                '41926',

                '2W/ 3W/ LCV/ HCV/ Tractor',
                'A',
                '-',
                'A',
                'A',
                'A',
                'B',
                'A',

                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Distributor Code',
            'Legal Name',
            'Trade Name',
            'Category',

            'Business Status',
            'Business Start Date',

            'Contact Person',
            'Designation',
            'Mobile',
            'Alternate Mobile',
            'Email',
            'Secondary Email',

            'Billing Address',
            'Billing City',
            'Billing City ID',
            'Billing District',
            'Billing District ID',
            'Billing State',
            'Billing State ID',
            'Billing Country',
            'Billing Country ID',
            'Billing Pincode',
            'Billing Pincode ID',

            'Shipping Address 1',

            'Sales Zone',
            'Area Territory',
            'Beat Route',
            'Beat ID',

            'Market Classification',
            'Competitor Brands',
            'GST Number',
            'PAN Number',
            'Registration Type',

            'Bank Name',
            'Account Holder',
            'Account Number',
            'IFSC',
            'Branch Name',

            'Credit Limit',
            'Credit Days',
            'Avg Monthly Purchase',
            'Outstanding Balance',
            'Preferred Payment Method',

            'Monthly Sales',
            'Product Categories',
            'Secondary Sales Required',
            'Last 12 Months Sales',

            'Sales Executive ID (JSON)',

            'Supervisor ID',

            'Customer Segment',
            'Weekly TAI Alert',
            'Target vs Achievement',
            'Schemes Updates',
            'New Launch Update',
            'Payment Alert',
            'Pending Orders',
            'Inventory Status',

            'Turnover',
            'Staff Strength',
            'Vehicles Capacity',
            'Area Coverage',
            'Other Brands Handled',
            'Warehouse Size',
        ];
    }
}