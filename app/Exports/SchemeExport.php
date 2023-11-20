<?php

namespace App\Exports;

use App\Models\Scheme;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class SchemeExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Brand::select('id','scheme_name', 'scheme_description', 'start_date', 'end_date', 'scheme_image', 'scheme_type', 'point_value')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','scheme_name', 'scheme_description', 'start_date', 'end_date', 'scheme_image', 'scheme_type', 'point_value'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['scheme_name'],
            $data['scheme_description'],
            Date::dateTimeToExcel($data['start_date']),
            Date::dateTimeToExcel($data['end_date']),
            $data['scheme_image'],
            $data['scheme_type'],
            $data['point_value'],
        ];
    }

}