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
        $this->pending_status = $request->input('pending_status');
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->order_id = $request->input('order_id');

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

            if($this->pending_status == '2'){

                 return OrderDetails::with('orders','orders.createdbyname')->where('status_id',$this->pending_status)->whereHas('orders',function ($query)  {
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
                    if($this->order_id)
                    {
                        $query->where('id',$this->order_id);
                    }  
                   
                })->select('id','order_id', 'product_id', 'product_detail_id', 'quantity', 'shipped_qty', 'price', 'discount', 'discount_amount', 'tax_amount', 'line_total', 'status_id', 'created_at','ebd_name','ebd_discount','ebd_amount','cluster_discount','cluster_amount','deal_discount','deal_amount','distributor_discount','distributor_amount')->latest()->get();      

            }else{

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
                    if($this->order_id)
                    {
                        $query->where('id',$this->order_id);
                    }  
                   
                })->select('id','order_id', 'product_id', 'product_detail_id', 'quantity', 'shipped_qty', 'price', 'discount', 'discount_amount', 'tax_amount', 'line_total', 'status_id', 'created_at','ebd_name','ebd_discount','ebd_amount','cluster_discount','cluster_amount','deal_discount','deal_amount','distributor_discount','distributor_amount')->latest()->get();          


            }  


    }

    public function headings(): array
    {
        return ['id','Order Date','Retailer ID','Customer','Customer Name', 'Dealer ID','Dealer & Distributor Name', 'User Name' ,'Order No','Invoice No','Invoice Date','Order ID', 'Order Status', 'Product Name', 'Product ID', 'Product Detail', 'Product Stage','kW', 'HP','Suc x Del','Category','Subcategory','Quantity', 'Shipped Qty','Pending Qty','Rate(LP)','Trade Discount%','Scheme Discount%', 'Scheme Name','CLuster Discount%','Deal Dicount%','Special Discount%','Sub Total','Tax%','Total', 'Status','Employee Code','Branch','Division','Designation'];
    }

    public function map($data): array
    {
            //$subtotal = $data['price']*$data['quantity'];
        
         $grandtotal;

         if(!empty($data['products']['productpriceinfo']['gst'])){
          $gstdis = $data['products']['productpriceinfo']['gst'];
            $gstamnt = $data['line_total']*$gstdis/100;

           $grandtotal = number_format($data['line_total']+$gstamnt, 2);

         }else{
          $grandtotal = number_format($data['line_total']);
         }

        $pending_qty = 0;
        $qty = $data['quantity']??0;
        $ship_qty = $data['shipped_qty']??0;
        $pending_qty = $qty-$ship_qty;


        return [
            $data['id'],
            isset($data['orders']['order_date']) ? date('Y-m-d', strtotime($data['orders']['order_date'])) :'',
            isset($data['orders']['buyer_id']) ? $data['orders']['buyer_id'] :'',
            isset($data['orders']['buyers']['customertypes']['customertype_name']) ? $data['orders']['buyers']['customertypes']['customertype_name'] :'',
            isset($data['orders']['buyers']['name']) ? $data['orders']['buyers']['name'] :'',
            isset($data['orders']['seller_id']) ? $data['orders']['seller_id'] :'',
            isset($data['orders']['sellers']['name']) ? $data['orders']['sellers']['name'] :'',
            isset($data['orders']['createdbyname']['name']) ? $data['orders']['createdbyname']['name'] :'',
            isset($data['orders']['orderno']) ? $data['orders']['orderno'] :'',
            isset($data['orders']['getsalesdetail']['invoice_no']) ? $data['orders']['getsalesdetail']['invoice_no'] :'',
            isset($data['orders']['getsalesdetail']['invoice_date']) ? $data['orders']['getsalesdetail']['invoice_date'] :'',
            isset($data['order_id']) ? $data['order_id'] :'',
         
            // isset($data['orders']['sub_total']) ? $data['orders']['sub_total'] :'',
            // isset($data['orders']['grand_total']) ? $data['orders']['grand_total'] :'',
            // isset($data['orders']['statusname']['status_name']) ? $data['orders']['statusname']['status_name'] :'',
            isset($data['statusname']['status_name']) ? $data['statusname']['status_name'] :'',
            isset($data['products']['product_name']) ? $data['products']['product_name'] :'',
            isset($data['product_id'])? $data['product_id'] :'',
            isset($data['products']['description']) ? $data['products']['description'] :'',
            isset($data['products']['product_no']) ? $data['products']['product_no'] :'',
            isset($data['products']['part_no']) ? $data['products']['part_no'] :'',
            isset($data['products']['specification']) ? $data['products']['specification'] :'',
            isset($data['products']['suc_del']) ? $data['products']['suc_del'] :'',
            isset($data['products']['categories']['category_name']) ? $data['products']['categories']['category_name'] :'',
            isset($data['products']['subcategories']['subcategory_name']) ? $data['products']['subcategories']['subcategory_name'] :'',
            isset($data['quantity'])? $data['quantity'] :'',
            isset($data['shipped_qty'])? $data['shipped_qty'] :'',
            $pending_qty??0,
             //isset($data['price'])? $data['price'] :'',
             isset($data['products']['productpriceinfo']['mrp']) ? $data['products']['productpriceinfo']['mrp'] :'',

            isset($data['products']['productpriceinfo']['discount']) ? $data['products']['productpriceinfo']['discount'] :'',

            // isset($data['products']['getSchemeDetail']['points']) ? $data['products']['getSchemeDetail']['points'] :'',

            // isset($data['products']['getSchemeDetail']['orderscheme']['scheme_name']) ? $data['products']['getSchemeDetail']['orderscheme']['scheme_name'] :'',

            isset($data['ebd_discount']) ? $data['ebd_discount'] :'',
            isset($data['ebd_name']) ? $data['ebd_name'] :'',
            isset($data['cluster_discount']) ? $data['cluster_discount'] :'',
            isset($data['deal_discount']) ? $data['deal_discount'] :'',
            isset($data['distributor_discount']) ? $data['distributor_discount'] :'',
            isset($data['line_total'])? $data['line_total'] :'',
            isset($data['products']['productpriceinfo']['gst']) ? $data['products']['productpriceinfo']['gst'] :'',

            //isset($data['line_total'])? $data['line_total'] :'',
            $grandtotal,

            isset($data['statusname']['status_name']) ? $data['statusname']['status_name'] :'',
            isset($data['orders']['getuserdetails']['employee_codes']) ? $data['orders']['getuserdetails']['employee_codes'] :'',
            isset($data['orders']['getuserdetails']['getbranch']['branch_name']) ? $data['orders']['getuserdetails']['getbranch']['branch_name'] :'',
            isset($data['orders']['getuserdetails']['getdivision']['division_name']) ? $data['orders']['getuserdetails']['getdivision']['division_name'] :'',
            isset($data['orders']['getuserdetails']['getdesignation']['designation_name']) ? $data['orders']['getuserdetails']['getdesignation']['designation_name'] :'',


        ];
    }

}
