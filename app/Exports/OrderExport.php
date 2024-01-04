<?php

namespace App\Exports;

use App\Models\OrderDetails;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class OrderExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');

        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        // return OrderDetails::with('orders','orders.createdbyname')->whereHas('orders',function ($query)  {
        //                         if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

        //                         {
        //                             $query->whereIn('created_by', $this->userids);
        //                         }
        //                     })->select('id','order_id', 'product_id', 'product_detail_id', 'quantity', 'shipped_qty', 'price', 'discount', 'discount_amount', 'tax_amount', 'line_total', 'status_id', 'created_at')->latest()->get();  

      return OrderDetails::with('orders','orders.createdbyname')->whereHas('orders',function ($query)  {
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

                })->select('id','order_id', 'product_id', 'product_detail_id', 'quantity', 'shipped_qty', 'price', 'discount', 'discount_amount', 'tax_amount', 'line_total', 'status_id', 'created_at')->latest()->get();  


    }

    public function headings(): array
    {
        return ['id','Order Date','Retailer ID','Retailer Name', 'Dealer ID','Dealer Name', 'User Name' ,'Order No', 'Order ID','Suc x Del', 'Sub Total', 'Grand Total', 'Order Status', 'Product Name', 'Product ID', 'Product Detail', 'Product Stage', 'kW', 'HP','Category','Subcategory','Quantity', 'Shipped Qty', 'Price', 'Total', 'Status','Employee Code','Branch','Division','Designation'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            isset($data['orders']['order_date']) ? date('Y-m-d', strtotime($data['orders']['order_date'])) :'',
            isset($data['orders']['buyer_id']) ? $data['orders']['buyer_id'] :'',
            isset($data['orders']['buyers']['name']) ? $data['orders']['buyers']['name'] :'',
            isset($data['orders']['seller_id']) ? $data['orders']['seller_id'] :'',
            isset($data['orders']['sellers']['name']) ? $data['orders']['sellers']['name'] :'',
            isset($data['orders']['createdbyname']['name']) ? $data['orders']['createdbyname']['name'] :'',
            isset($data['orders']['orderno']) ? $data['orders']['orderno'] :'',
            isset($data['order_id']) ? $data['order_id'] :'',
            isset($data['products']['suc_del']) ? $data['products']['suc_del'] :'',
            isset($data['orders']['sub_total']) ? $data['orders']['sub_total'] :'',
            isset($data['orders']['grand_total']) ? $data['orders']['grand_total'] :'',
            isset($data['orders']['statusname']['status_name']) ? $data['orders']['statusname']['status_name'] :'',
            isset($data['products']['product_name']) ? $data['products']['product_name'] :'',
            isset($data['product_id'])? $data['product_id'] :'',
            isset($data['products']['description']) ? $data['products']['description'] :'',
            isset($data['products']['product_no']) ? $data['products']['product_no'] :'',
            isset($data['products']['part_no']) ? $data['products']['part_no'] :'',
            isset($data['products']['specification']) ? $data['products']['specification'] :'',
            isset($data['products']['categories']['category_name']) ? $data['products']['categories']['category_name'] :'',
            isset($data['products']['subcategories']['subcategory_name']) ? $data['products']['subcategories']['subcategory_name'] :'',
            isset($data['quantity'])? $data['quantity'] :'',
            isset($data['shipped_qty'])? $data['shipped_qty'] :'',
            isset($data['price'])? $data['price'] :'',
            isset($data['line_total'])? $data['line_total'] :'',
            isset($data['statusname']['status_name']) ? $data['statusname']['status_name'] :'',
            isset($data['orders']['getuserdetails']['employee_codes']) ? $data['orders']['getuserdetails']['employee_codes'] :'',
            isset($data['orders']['getuserdetails']['getbranch']['branch_name']) ? $data['orders']['getuserdetails']['getbranch']['branch_name'] :'',
            isset($data['orders']['getuserdetails']['getdepartment']['division_name']) ? $data['orders']['getuserdetails']['getdepartment']['division_name'] :'',
            isset($data['orders']['getuserdetails']['getdesignation']['designation_name']) ? $data['orders']['getuserdetails']['getdesignation']['designation_name'] :'',


        ];
    }

}
