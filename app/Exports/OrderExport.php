<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Auth;


class OrderExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->pending_status = $request->input('pending_status');
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->order_id = $request->input('order_id');
        $this->user_id = $request->input('user_id');
        $this->dividion_id = $request->input('dividion_id');
        $this->customer_type_id = $request->input('customer_type_id');

        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        $final = collect(); // Final result

        // return OrderDetails::with('orders','orders.createdbyname')->whereHas('orders',function ($query)  {
        //                         if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

        //                         {
        //                             $query->whereIn('created_by', $this->userids);
        //                         }
        //                     })->select('id','order_id', 'product_id', 'product_detail_id', 'quantity', 'shipped_qty', 'price', 'discount', 'discount_amount', 'tax_amount', 'line_total', 'status_id', 'created_at')->latest()->get();  

        if ($this->pending_status != '' && $this->pending_status != NULL) {
            $query = OrderDetails::with('orders', 'orders.createdbyname', 'orders.buyers')->whereHas('orders', function ($query) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('created_by', $this->userids);
                }
                $status = match ((string) $this->pending_status) {
                    '0' => 'pending',
                    '2' => 'partial',
                    '1' => 'dispatched',
                    default => '',
                };
                $query->dispatchStatus($status);
                if ($this->startdate) {
                    $query->where('order_date', '>=', $this->startdate);
                }
                if ($this->enddate) {
                    $query->where('order_date', '<=', $this->enddate);
                }
                if ($this->order_id) {
                    $query->where('id', $this->order_id);
                }
                if ($this->user_id) {
                    $query->where('executive_id', $this->user_id);
                }
                if ($this->dividion_id) {
                    $order_ids = Order::where('product_cat_id', $this->dividion_id)->pluck('id');
                    $query->whereIn('id', $order_ids);
                    // $query->where('orders.product_cat_id',$this->dividion_id);
                }

                if ($this->customer_type_id && $this->customer_type_id != '') {
                    $this->applyCustomerTypeFilter($query);
                }
            });

            $query->chunk(1000, function ($results) use (&$final) {
                foreach ($results as $row) {
                    $row->orders?->resolveCustomerRelations();
                    $final->push($row);
                }
            });

            return $final;
        } else {
            $query =  OrderDetails::with('orders', 'orders.createdbyname')->whereHas('orders', function ($query) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('created_by', $this->userids);
                }

                if ($this->order_id) {
                    $query->where('id', $this->order_id);
                }
                if ($this->dividion_id) {
                    $order_ids = Order::where(function ($query) {
                        $query->where('product_cat_id', $this->dividion_id)
                            ->orWhereNull('product_cat_id');
                    });
                    if ($this->startdate) {
                        $order_ids->where('order_date', '>=', $this->startdate);
                    }
                    if ($this->enddate) {
                        $order_ids->where('order_date', '<=', $this->enddate);
                    }
                    $order_ids = $order_ids->pluck('id');
                    $query->whereIn('id', $order_ids);
                    if ($this->order_id) {
                        $query->where('id', $this->order_id);
                    }
                    if ($this->user_id) {
                        $query->where('executive_id', $this->user_id);
                    }
                    if ($this->dividion_id) {
                        $order_ids = Order::where('product_cat_id', $this->dividion_id)->pluck('id');
                        $query->whereIn('id', $order_ids);
                        // $query->where('orders.product_cat_id',$this->dividion_id);
                    }

                    if ($this->customer_type_id && $this->customer_type_id != '') {
                        $this->applyCustomerTypeFilter($query);
                    }
                    // $query->where('orders.product_cat_id',$this->dividion_id);
                } else {
                    if ($this->startdate) {
                        $query->where('order_date', '>=', $this->startdate);
                    }
                    if ($this->enddate) {
                        $query->where('order_date', '<=', $this->enddate);
                    }
                    if ($this->order_id) {
                        $query->where('id', $this->order_id);
                    }
                    if ($this->user_id) {
                        $query->where('executive_id', $this->user_id);
                    }
                    if ($this->dividion_id) {
                        $order_ids = Order::where('product_cat_id', $this->dividion_id)->pluck('id');
                        $query->whereIn('id', $order_ids);
                        // $query->where('orders.product_cat_id',$this->dividion_id);
                    }

                    if ($this->customer_type_id && $this->customer_type_id != '') {
                        $this->applyCustomerTypeFilter($query);
                    }
                }
            })->select('id', 'order_id', 'product_id', 'product_detail_id', 'quantity', 'shipped_qty', 'price', 'discount', 'discount_amount', 'tax_amount', 'line_total', 'status_id', 'created_at', 'scheme_name', 'scheme_discount', 'scheme_amount', 'cluster_discount', 'cluster_amount', 'deal_discount', 'deal_amount', 'distributor_discount', 'distributor_amount');


            $query->chunk(1000, function ($results) use (&$final) {
                foreach ($results as $row) {
                    $row->orders?->resolveCustomerRelations();
                    $final->push($row);
                }
            });

            return $final;
        }
    }

    public function headings(): array
    {
        if ($this->isDistributorExport()) {
            return [
                'Sale Order',
                'Document Type',
                'Plant',
                'Sales Org',
                'Dist Chnl',
                'Division',
                'Customer PO No',
                'Customer PO Dt',
                'Material Code',
                'Quantity',
                'Delivery Date',
                'Customer',
                'Storage Location',
                'PO Amendment No',
                'PO Amendment Dt',
                'Customer Material No',
                'Ship To Party',
                'Dealer & Distributor Name',
                'Category',
                'Subcategory',
                'Product Name',
                'Shipped Qty',
                'Pending Qty',
                'Rate',
                'Total',
                'Order Amount',
                'Employee Code',
                'User Name',
                'Branch',
                'Division',
                'Designation',
                'Status',
            ];
        }

        return [
            'Order Date',
            'Buyer Code',
            'Customer Type',
            'Buyer Name',
            'Seller or Parent Code',
            'Seller or Parent Name',
            'Order No',
            'Category',
            'Subcategory',
            'Product Code',
            'Product Name',
            'Quantity',
            'Shipped Qty',
            'Pending Qty',
            'Rate',
            'Total',
            'Order Amount',
            'Order Remark',
            'Employee Code',
            'User Name',
            'Branch',
            'Division',
            'Designation',
            'Status',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestDataRow();
                $lastColumn = $sheet->getHighestDataColumn();
                $headingRange = 'A1:' . $lastColumn . '1';

                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getStyle($headingRange)->getAlignment()->setWrapText(true);
                $sheet->getStyle($headingRange)->getFont()->setSize(14);

                $sheet->getStyle($headingRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '00AADB'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                if ($lastRow >= 2) {
                    $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }
            },
        ];
    }

    public function map($data): array
    {
        //$subtotal = $data['price']*$data['quantity'];

        $grandtotal;

        if (!empty($data['products']['productpriceinfo']['gst'])) {
            $gstdis = $data['products']['productpriceinfo']['gst'];
            $gstamnt = $data['line_total'] * $gstdis / 100;

            $grandtotal = number_format($data['line_total'] + $gstamnt, 2);
        } else {
            $grandtotal = number_format($data['line_total']);
        }

        $pending_qty = 0;
        $qty = $data['quantity'] ?? 0;
        $ship_qty = $data['shipped_qty'] ?? 0;
        $pending_qty = $qty - $ship_qty;
        $order = $data['orders'] ?? [];
        $orderModel = $data->orders;
        $customerType = strtoupper((string) ($order['customer_type'] ?? $order['buyers']['type'] ?? ''));
        $customerType = $customerType === 'DISTRIBUTER' ? 'DISTRIBUTOR' : $customerType;

        if ($this->isDistributorExport()) {
            $distributorCode = $order['sellers']['distributor_code'] ?? '';
            $distributorName = $order['sellers']['trade_name']
                ?? $order['sellers']['legal_name']
                ?? '';

            return [
                '',
                'ZAFM',
                $order['sellers']['plant'] ?? '',
                '1000',
                '20',
                '0',
                $order['orderno'] ?? '',
                !empty($order['order_date']) ? date('Y-m-d', strtotime($order['order_date'])) : '',
                $data['products']['product_code'] ?? '',
                $data['quantity'] ?? '',
                !empty($order['completed_date']) ? date('Y-m-d', strtotime($order['completed_date'])) : '',
                $distributorCode,
                '',
                '',
                '',
                '',
                $distributorCode,
                $distributorName,
                $data['products']['categories']['category_name'] ?? '',
                $data['products']['subcategories']['subcategory_name'] ?? '',
                $data['products']['product_name'] ?? '',
                $data['shipped_qty'] ?? '',
                $pending_qty,
                $data['products']['productpriceinfo']['mrp'] ?? '',
                $data['line_total'] ?? '',
                $order['grand_total'] ?? '',
                $order['getuserdetails']['employee_codes'] ?? '',
                $order['createdbyname']['name'] ?? '',
                $order['getuserdetails']['getbranch']['branch_name'] ?? '',
                $order['getuserdetails']['getdivision']['division_name'] ?? '',
                $order['getuserdetails']['getdesignation']['designation_name'] ?? '',
                $orderModel?->dispatch_status ?? 'Pending',
            ];
        }

        return [
            isset($order['order_date']) ? date('Y-m-d', strtotime($order['order_date'])) : '',
            $order['buyer_id'] ?? $order['seller_id'] ?? '',
            $customerType,
            $order['buyers']['shop_name']
                ?? $order['sellers']['trade_name']
                ?? $order['sellers']['legal_name']
                ?? '',
            ($order['seller_type'] ?? 'DISTRIBUTOR') === 'RETAILER'
                ? ($order['sellers']['id'] ?? '')
                : ($order['sellers']['distributor_code'] ?? ''),
            $order['sellers']['shop_name'] ?? $order['sellers']['trade_name'] ?? $order['sellers']['legal_name'] ?? '',
            $order['orderno'] ?? '',
            $data['products']['categories']['category_name'] ?? '',
            $data['products']['subcategories']['subcategory_name'] ?? '',
            $data['products']['product_code'] ?? '',
            $data['products']['product_name'] ?? '',
            $data['quantity'] ?? '',
            $data['shipped_qty'] ?? '',
            $pending_qty,
            $data['products']['productpriceinfo']['mrp'] ?? '',
            $data['line_total'] ?? '',
            $order['grand_total'] ?? '',
            $order['order_remark'] ?? '',
            $order['getuserdetails']['employee_codes'] ?? '',
            $order['createdbyname']['name'] ?? '',
            $order['getuserdetails']['getbranch']['branch_name'] ?? '',
            $order['getuserdetails']['getdivision']['division_name'] ?? '',
            $order['getuserdetails']['getdesignation']['designation_name'] ?? '',
            $orderModel?->dispatch_status ?? 'Pending',
        ];

        if ($this->dividion_id == '1') {
            return [
                $data['id'],
                isset($data['orders']['order_date']) ? date('Y-m-d', strtotime($data['orders']['order_date'])) : '',
                isset($data['orders']['getuserdetails']['employee_codes']) ? $data['orders']['getuserdetails']['employee_codes'] : '',
                isset($data['orders']['createdbyname']['name']) ? $data['orders']['createdbyname']['name'] : '',
                isset($data['orders']['getuserdetails']['getbranch']['branch_name']) ? $data['orders']['getuserdetails']['getbranch']['branch_name'] : '',
                isset($data['orders']['getuserdetails']['getdivision']['division_name']) ? $data['orders']['getuserdetails']['getdivision']['division_name'] : '',
                isset($data['orders']['getuserdetails']['getdesignation']['designation_name']) ? $data['orders']['getuserdetails']['getdesignation']['designation_name'] : '',
                isset($data['orders']['buyer_id']) ? $data['orders']['buyer_id'] : '',
                isset($data['orders']['buyers']['customertypes']['customertype_name']) ? $data['orders']['buyers']['customertypes']['customertype_name'] : '',
                isset($data['orders']['buyers']['name']) ? $data['orders']['buyers']['name'] : '',
                isset($data['orders']['seller_id']) ? $data['orders']['seller_id'] : '',
                isset($data['orders']['sellers']['name']) ? $data['orders']['sellers']['name'] : '',
                isset($data['orders']['sellers']['sap_code']) ? $data['orders']['sellers']['sap_code'] : '',
                isset($data['orders']['orderno']) ? $data['orders']['orderno'] : '',
                isset($data['orders']['id']) ? $data['orders']['id'] : '',

                isset($data['products']['categories']['category_name']) ? $data['products']['categories']['category_name'] : '',
                isset($data['products']['subcategories']['subcategory_name']) ? $data['products']['subcategories']['subcategory_name'] : '',
                isset($data['products']['product_code']) ? $data['products']['product_code'] : '',
                isset($data['products']['product_name']) ? $data['products']['product_name'] : '',
                isset($data['product_id']) ? $data['product_id'] : '',
                isset($data['products']['product_no']) ? $data['products']['product_no'] : '',
                isset($data['products']['part_no']) ? $data['products']['part_no'] : '',
                isset($data['products']['specification']) ? $data['products']['specification'] : '',
                isset($data['products']['suc_del']) ? $data['products']['suc_del'] : '',
                isset($data['quantity']) ? $data['quantity'] : '',
                isset($data['shipped_qty']) ? $data['shipped_qty'] : '',
                $pending_qty ?? 0,

                isset($data['products']['productpriceinfo']['mrp']) ? $data['products']['productpriceinfo']['mrp'] : '',

                isset($data['products']['productpriceinfo']['discount']) ? $data['products']['productpriceinfo']['discount'] : '',

                isset($data['scheme_discount']) ? $data['scheme_discount'] : '',
                isset($data['scheme_name']) ? $data['scheme_name'] : '',
                isset($data['orders']['ebd_discount']) ? $data['orders']['ebd_discount'] : '',
                isset($data['orders']['distributor_discount']) ? $data['orders']['distributor_discount'] : '',
                isset($data['orders']['special_discount']) ? $data['orders']['special_discount'] : '',
                isset($data['orders']['frieght_discount']) ? $data['orders']['frieght_discount'] : '',
                isset($data['orders']['cluster_discount']) ? $data['orders']['cluster_discount'] : '',
                isset($data['orders']['deal_discount']) ? $data['orders']['deal_discount'] : '',
                isset($data['orders']['cash_discount']) ? $data['orders']['cash_discount'] : '',
                $data['products'] && isset($data['products']['productpriceinfo'], $data['products']['productpriceinfo']['mrp'], $data['quantity']) && $data['products']['productpriceinfo']['mrp'] > 0 && $data['quantity'] > 0 ? number_format(((1 - ($data['line_total'] / ($data['products']['productpriceinfo']['mrp'] * $data['quantity']))) * 100), 2) : '0',
                isset($data['products']['productpriceinfo']['gst']) ? $data['products']['productpriceinfo']['gst'] : '',
                isset($data['line_total']) ? $data['line_total'] : '',
                isset($data['orders']['grand_total'])  ? $data['orders']['grand_total'] : '',
                // $grandtotal,
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['updatedbyname']) ? $data['orders']['updatedbyname']['name'] : '',

                isset($data['orders']['statusname']) ? $data['orders']['statusname']['status_name'] : 'Pending',
            ];
        } elseif ($this->dividion_id == '2') {
            return [
                $data['id'],
                isset($data['orders']['order_date']) ? date('Y-m-d', strtotime($data['orders']['order_date'])) : '',
                isset($data['orders']['getuserdetails']['employee_codes']) ? $data['orders']['getuserdetails']['employee_codes'] : '',
                isset($data['orders']['createdbyname']['name']) ? $data['orders']['createdbyname']['name'] : '',
                isset($data['orders']['getuserdetails']['getbranch']['branch_name']) ? $data['orders']['getuserdetails']['getbranch']['branch_name'] : '',
                isset($data['orders']['getuserdetails']['getdivision']['division_name']) ? $data['orders']['getuserdetails']['getdivision']['division_name'] : '',
                isset($data['orders']['getuserdetails']['getdesignation']['designation_name']) ? $data['orders']['getuserdetails']['getdesignation']['designation_name'] : '',
                isset($data['orders']['buyer_id']) ? $data['orders']['buyer_id'] : '',
                isset($data['orders']['buyers']['customertypes']['customertype_name']) ? $data['orders']['buyers']['customertypes']['customertype_name'] : '',
                isset($data['orders']['buyers']['name']) ? $data['orders']['buyers']['name'] : '',
                isset($data['orders']['seller_id']) ? $data['orders']['seller_id'] : '',
                isset($data['orders']['sellers']['name']) ? $data['orders']['sellers']['name'] : '',
                isset($data['orders']['sellers']['sap_code']) ? $data['orders']['sellers']['sap_code'] : '',
                isset($data['orders']['orderno']) ? $data['orders']['orderno'] : '',
                isset($data['orders']['id']) ? $data['orders']['id'] : '',

                isset($data['products']['categories']['category_name']) ? $data['products']['categories']['category_name'] : '',
                isset($data['products']['subcategories']['subcategory_name']) ? $data['products']['subcategories']['subcategory_name'] : '',
                isset($data['products']['product_code']) ? $data['products']['product_code'] : '',
                isset($data['products']['product_name']) ? $data['products']['product_name'] : '',
                isset($data['product_id']) ? $data['product_id'] : '',
                isset($data['quantity']) ? $data['quantity'] : '',
                isset($data['shipped_qty']) ? $data['shipped_qty'] : '',
                $pending_qty ?? 0,

                isset($data['products']['productpriceinfo']['mrp']) ? $data['products']['productpriceinfo']['mrp'] : '',

                isset($data['orders']['dod_discount']) ? $data['orders']['dod_discount'] : '',
                isset($data['orders']['special_distribution_discount']) ? $data['orders']['special_distribution_discount'] : '',
                isset($data['orders']['distribution_margin_discount']) ? $data['orders']['distribution_margin_discount'] : '',
                isset($data['orders']['cash_discount']) ? $data['orders']['cash_discount'] : '',
                isset($data['orders']['total_fan_discount']) ? $data['orders']['total_fan_discount'] : '',
                ($data['orders']['sub_total'] / (1 - $data['orders']['total_fan_discount'] / 100)) - $data['orders']['sub_total'],

                isset($data['products']['productpriceinfo']['gst']) ? $data['products']['productpriceinfo']['gst'] : '',
                isset($data['line_total']) ? $data['line_total'] : '',
                isset($data['orders']['grand_total'])  ? $data['orders']['grand_total'] : '',
                // $grandtotal,
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['updatedbyname']) ? $data['orders']['updatedbyname']['name'] : '',

                isset($data['orders']['statusname']) ? $data['orders']['statusname']['status_name'] : 'Pending',
            ];
        } else {
            return [
                $data['id'],
                isset($data['orders']['order_date']) ? date('Y-m-d', strtotime($data['orders']['order_date'])) : '',
                isset($data['orders']['getuserdetails']['employee_codes']) ? $data['orders']['getuserdetails']['employee_codes'] : '',
                isset($data['orders']['createdbyname']['name']) ? $data['orders']['createdbyname']['name'] : '',
                isset($data['orders']['getuserdetails']['getbranch']['branch_name']) ? $data['orders']['getuserdetails']['getbranch']['branch_name'] : '',
                isset($data['orders']['getuserdetails']['getdivision']['division_name']) ? $data['orders']['getuserdetails']['getdivision']['division_name'] : '',
                isset($data['orders']['getuserdetails']['getdesignation']['designation_name']) ? $data['orders']['getuserdetails']['getdesignation']['designation_name'] : '',
                isset($data['orders']['buyer_id']) ? $data['orders']['buyer_id'] : '',
                isset($data['orders']['buyers']['customertypes']['customertype_name']) ? $data['orders']['buyers']['customertypes']['customertype_name'] : '',
                isset($data['orders']['buyers']['name']) ? $data['orders']['buyers']['name'] : '',
                isset($data['orders']['seller_id']) ? $data['orders']['seller_id'] : '',
                isset($data['orders']['sellers']['name']) ? $data['orders']['sellers']['name'] : '',
                isset($data['orders']['sellers']['sap_code']) ? $data['orders']['sellers']['sap_code'] : '',
                isset($data['orders']['orderno']) ? $data['orders']['orderno'] : '',
                isset($data['orders']['id']) ? $data['orders']['id'] : '',

                isset($data['products']['categories']['category_name']) ? $data['products']['categories']['category_name'] : '',
                isset($data['products']['subcategories']['subcategory_name']) ? $data['products']['subcategories']['subcategory_name'] : '',
                isset($data['products']['product_code']) ? $data['products']['product_code'] : '',
                isset($data['products']['product_name']) ? $data['products']['product_name'] : '',
                isset($data['product_id']) ? $data['product_id'] : '',
                isset($data['products']['product_no']) ? $data['products']['product_no'] : '',
                isset($data['products']['part_no']) ? $data['products']['part_no'] : '',
                isset($data['products']['specification']) ? $data['products']['specification'] : '',
                isset($data['products']['suc_del']) ? $data['products']['suc_del'] : '',
                isset($data['quantity']) ? $data['quantity'] : '',
                isset($data['shipped_qty']) ? $data['shipped_qty'] : '',
                $pending_qty ?? 0,

                isset($data['products']['productpriceinfo']['mrp']) ? $data['products']['productpriceinfo']['mrp'] : '',

                isset($data['products']['productpriceinfo']['gst']) ? $data['products']['productpriceinfo']['gst'] : '',
                isset($data['line_total']) ? $data['line_total'] : '',
                isset($data['orders']['grand_total'])  ? $data['orders']['grand_total'] : '',
                // $grandtotal,
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['updatedbyname']) ? $data['orders']['updatedbyname']['name'] : '',

                isset($data['orders']['statusname']) ? $data['orders']['statusname']['status_name'] : 'Pending',
            ];
        }
    }

    private function applyCustomerTypeFilter($query): void
    {
        $customerType = strtoupper((string) $this->customer_type_id);

        if ($customerType === 'DISTRIBUTOR') {
            $query->whereIn('order_type', ['MASTER_DISTRIBUTER', 'MASTER_DISTRIBUTOR']);
            return;
        }

        $query->whereHas('buyers', function ($buyerQuery) use ($customerType) {
            $buyerQuery->where('type', $customerType);
        });
    }

    private function isDistributorExport(): bool
    {
        return strtoupper((string) $this->customer_type_id) === 'DISTRIBUTOR';
    }
}
