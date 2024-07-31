<?php

namespace App\Exports;

use App\Models\PrimarySales;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class PrimarySalesExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return PrimarySales::select('id','invoiceno','invoice_date','month','division','dealer',
        'city','state','final_branch','sales_person','emp_code','product_name','model_name','quantity','rate','net_amount','tax_amount','cgst_amount','sgst_amount','igst_amount','total_amount', 'store_name','new_group','new_group_name','branch','product_id','delete_this')->latest()->get();   
    }

    public function headings(): array
    {
        return [
            'id',
            'Invoice No',  
            'Invoice Date',
            'Month',
            'DIV', 
            'Dealer',
            'City',
            'State',
            'Final Branch',
            'Sales person',
            'Emp Code',
            'Model Name',
            'Product Name',
            'Quantity',
            'Rate',
            'Net Amount',
            'Tax %',
            'CGST Amt',
            'SGST Amt',
            'IGST Amt',
            'Total',
            'Store Name',
            'Group',
            'Branch',
            'New Group Name',
            'Product ID',   
            'Delete This',   
        ];
    }

}