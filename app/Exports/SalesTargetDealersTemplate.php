<?php

namespace App\Exports;

use App\Models\SalesTargetCustomers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class SalesTargetDealersTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return SalesTargetCustomers::select('customer_id', 'month', 'year', 'target')->limit(0)->get();   
    }

    public function headings(): array
    {
        return [['Branch Id','Customer Id','Div Id','Type','Dealer','04/24','05/24','06/24','07/24','08/24','09/24','10/24','11/24','12/24','01/25','02/25','03/25'],['','','','Add primary or secondary value only.please remove this row before upload.']];
    }

}