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
class SchemeTepmlate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Scheme::select('scheme_name', 'scheme_description', 'start_date', 'end_date', 'scheme_image', 'scheme_type', 'point_value')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['scheme_name', 'scheme_description', 'start_date', 'end_date', 'scheme_image', 'scheme_type', 'point_value'];
    }

}
}
