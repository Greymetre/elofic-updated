<?php

namespace App\Exports;

use App\Models\Subcategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class SubcategoryExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Subcategory::select('id','subcategory_name', 'sap_code','category_id')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','subcategory_name', 'Sap Code','category_id','category_name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['subcategory_name'],
            $data['sap_code'],
            $data['category_id'],
            $data['categories']['category_name'],
        ];
    }

}