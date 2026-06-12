<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;

class SecondaryCustomersTemplateExport implements 
    FromCollection, 
    WithHeadings, 
    ShouldAutoSize,
    WithEvents
{
    protected $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Blank rows (sirf 1 empty row daal rahe hain example ke liye)
     */
    public function collection()
    {
        return new Collection([
            // Ek blank row example ke liye (optional)
            array_fill(0, 19, ''), 
        ]);
    }

    /**
     * Headers – bilkul wohi jo final export mein hain
     */
    public function headings(): array
    {
        return [
            // 'ID',
         
            'Type *',
            'Sub Type *',
            'Owner Name *',
            'Shop Name *',
            'Mobile Number *',
            'WhatsApp Number', 
            'Vehicle Segment *', 
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
            'Opportunity Status *', 
            'Awareness Status',
            'Employee Name',
            'Employee Code *',
            // 'Created By',
            // 'Gmap Address',
            'GPS Location',
            // 'Created At',
        ];
    }

    /**
     * Styling: Headers bold + background color
     */
    public function registerEvents(): array
{
    return [
        AfterSheet::class => function(AfterSheet $event) {

            $sheet = $event->sheet->getDelegate();

            // Headers style
            $sheet->getStyle('A1:AB1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE0E0E0'],
                ],
            ]);

            $awarenessLabel = in_array($this->type, ['RETAILER', 'WORKSHOP'])
                ? 'Nistha'
                : 'Saathi';

            // Instructions
            $sheet->setCellValue('A3', 'Instructions:');

            $sheet->setCellValue(
                'A4',
                '1. Mandatory Fields (*): Type, Sub Type, Owner Name, Shop Name, Mobile Number, Vehicle Segment, Opportunity Status, Employee Code'
            );

            $sheet->setCellValue(
                'A5',
                '2. Type Allowed Values: MECHANIC, GARAGE, RETAILER, WORKSHOP'
            );

            $sheet->setCellValue(
                'A6',
                '3. Opportunity Status Allowed Values: HOT, WARM, COLD, LOST'
            );

            $sheet->setCellValue(
                'A7',
                "4. {$awarenessLabel} Awareness Status Allowed Values: Done / Not Done"
            );

            $sheet->setCellValue(
                'A8',
                '5. Mobile Number must be exactly 10 digits'
            );

            $sheet->setCellValue(
                'A9',
                '6. Vehicle Segment Allowed Values: 2W, 3W, AGRICULTURE – Tractor, COOLANT, EARTH MOVING EQUIPMENT, HCV, LCV, LUBRICANT, PASSENGER VEHICLE (PV)'
            );

            $sheet->setCellValue(
                'A10',
                '7. Employee Code can contain multiple comma-separated employee codes'
            );

            $sheet->setCellValue(
                'A11',
                '8. GPS Location format: latitude,longitude'
            );

            $sheet->setCellValue(
                'A12',
                '9. Type column must match selected import type: ' . strtoupper($this->type)
            );

            // Subtype instructions
            if ($this->type === 'MECHANIC') {

                $sheet->setCellValue(
                    'A13',
                    '10. Mechanic Sub Types: Two-Wheeler Mechanic, Car / 4W Mechanic, HCV-LCV Mechanic, Tractor / Agri Machine, Diesel/FIP Mechanic'
                );

            } elseif ($this->type === 'GARAGE') {

                $sheet->setCellValue(
                    'A13',
                    '10. Garage Sub Types: ROADSIDE GARAGE, MULTI EMPLOYEE GARAGE, ONE-MAN GARAGE'
                );

            } elseif ($this->type === 'RETAILER') {

                $sheet->setCellValue(
                    'A13',
                    '10. Retailer Sub Types: AUTO SPARE PARTS RETAILER, LUBRICANT RETAILER, TWO WHEELER PARTS SHOP, CAR ACCESSORIES & PARTS SHOP, TRACTOR PARTS SHOP, HCV-LCV SHOP'
                );

            } elseif ($this->type === 'WORKSHOP') {

                $sheet->setCellValue(
                    'A13',
                    '10. Workshop Sub Types: Lube & Filter Change Workshop, Two-Wheeler Service Workshop, Car Service Workshop, HCV - LCV Workshop'
                );
            }

            // Instruction styling
            $sheet->getStyle('A3:A13')->getFont()->setBold(true);
            $sheet->getStyle('A3:A13')->getFont()->setSize(11);

            $sheet->getStyle('A3:A13')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);

            $sheet->getStyle('A3:A13')
                ->getFill()
                ->getStartColor()
                ->setARGB('FFF0F0F0');
        },
    ];
}
}