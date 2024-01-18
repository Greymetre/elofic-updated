<?php

namespace App\Exports;

use App\Models\SalesDetails;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class SalesExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }
    
    public function collection()
    {
        return SalesDetails::with('sales')->whereHas('sales',function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('created_by', $this->userids);
                                }
                            })->select('id','sales_id', 'product_id','product_detail_id', 'quantity', 'price', 'tax_amount', 'line_total')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','Retailer ID','Retailer Name', 'Dealer ID','Dealer Name','Invoice Date', 'invoice No', 'Order No', 'Order ID', 'Sub Total', 'Grand Total', 'Status', 'Product Name', 'Product ID', 'Product Detail', 'Quantity', 'Price', 'Total'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            isset($data['sales']['buyer_id']) ? $data['sales']['buyer_id'] :'',
            isset($data['sales']['buyers']['name']) ? $data['sales']['buyers']['name'] :'',
            isset($data['sales']['seller_id']) ? $data['sales']['seller_id'] :'',
            isset($data['sales']['sellers']['name']) ? $data['sales']['sellers']['name'] :'',
            isset($data['sales']['invoice_date']) ? date('Y-m-d', strtotime($data['sales']['invoice_date'])) :'',
            isset($data['sales']['invoice_no']) ? $data['sales']['invoice_no'] :'',
            isset($data['sales']['orders']['orderno']) ? $data['sales']['orders']['orderno'] :'',
            isset($data['sales']['order_id']) ? $data['sales']['order_id'] :'',
            isset($data['sales']['sub_total']) ? $data['sales']['sub_total'] :'',
            isset($data['sales']['grand_total']) ? $data['sales']['grand_total'] :'',
            isset($data['sales']['statusname']['status_name']) ? $data['sales']['statusname']['status_name'] :'',
            isset($data['products']['product_name']) ? $data['products']['product_name'] :'',
            isset($data['product_id'])? $data['product_id'] :'',
            isset($data['productdetails']['detail_title']) ? $data['productdetails']['detail_title'] :'',
            isset($data['quantity'])? $data['quantity'] :'',
            isset($data['price'])? $data['price'] :'',
            isset($data['line_total'])? $data['line_total'] :'',
        ];
    }
}