<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\SalesDetails;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class SalesExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct(Request $request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->dividion_id = $request->input('dividion_id');
        $this->customer_type_id = $request->input('customer_type_id');
        $this->userids = getUsersReportingToAuth();
    }
    
    public function collection()
    {
        return SalesDetails::with('sales','sales.buyers')->whereHas('sales',function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('created_by', $this->userids);
                                }
                                if($this->startdate)
                                {
                                    $query->whereDate('created_at','>=',$this->startdate);
                                }
                                if($this->enddate)
                                {
                                    $query->whereDate('created_at','<=',$this->enddate);
                                }
                                if ($this->dividion_id) {
                                    $order_ids = Order::where('product_cat_id', $this->dividion_id)->pluck('id');
                                    $query->whereIn('order_id', $order_ids);
                                    // $query->where('orders.product_cat_id',$this->dividion_id);
                                }
                
                                if ($this->customer_type_id && $this->customer_type_id != '') {
                                    $Order_ids = Order::with('buyers')
                                        ->whereHas('buyers', function ($query) {
                                            $query->where('customertype', $this->customer_type_id);
                                        })->pluck('id');
                                    $query->whereIn('order_id', $order_ids);
                                }
                            })->select('id','sales_id', 'product_id','product_detail_id', 'quantity', 'price', 'tax_amount', 'line_total')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','Retailer ID','Retailer Name', 'Dealer ID','Dealer Name','Invoice Date', 'invoice No', 'Order No', 'Order ID', 'Sub Total', 'Grand Total', 'Status', 'Product Name', 'Product ID', 'Product Detail', 'Quantity', 'Price', 'Total', 'Transport Details', 'LR Number'];
    }

    public function map($data): array
    {
        // dd($data['sales']['orders']);
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
            isset($data['sales']['transport_details']) ? $data['sales']['transport_details'] :'',
            isset($data['sales']['lr_no']) ? $data['sales']['lr_no'] :'',
        ];
    }
}