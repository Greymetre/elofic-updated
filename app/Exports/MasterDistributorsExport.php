<?php

namespace App\Exports;

use App\Models\MasterDistributor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MasterDistributorsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $distributors;
    protected $maxShippingAddresses = 0;

    public function __construct($distributors, $isTemplate = false)
    {
        $this->distributors = $distributors;

        // Find maximum shipping addresses count
        $this->maxShippingAddresses = $distributors->max(function ($distributor) {

        $addresses = $distributor->shipping_address;

            return is_array($addresses)
                ? count($addresses)
                : 0;
        });
       
    }

    public function collection()
    {
       

        return $this->distributors;
    }

    public function headings(): array
    {

        $headings = [
            'ID',
            'Distributor Code',
            'Plant',
            'Legal Name',
            'Trade Name',
            'Category',
            'Business Status',
            'Business Start Date',
            'Shop Image',
            'Profile Image',
            'Contact Person',
            'Designation',
            'Mobile',
            'Alternate Mobile',
            'Email',
            'Secondary Email',
            'Billing Address',
            'Billing City',
            'Billing City ID',
            'Billing District',
            'Billing District ID',
            'Billing State',
            'Billing State ID',
            'Billing Country',
            'Billing Country ID',
            'Billing Pincode',
            'Billing Pincode ID',
        ];

        // Dynamic Shipping Address Columns
        for ($i = 1; $i <= $this->maxShippingAddresses; $i++) {
            $headings[] = "Shipping Address {$i}";
        }

        $remaining = [
            // 'Shipping City',
            // 'Shipping District',
            // 'Shipping State',
            // 'Shipping Country',
            // 'Shipping Pincode',
            'Sales Zone',
            'Area Territory',
            'Beat Route',
            'Beat ID',
            'Market Classification',
            'Competitor Brands',
            'GST Number',
            'PAN Number',
            'Registration Type',
            'Documents',
            'Bank Name',
            'Account Holder',
            'Account Number',
            'IFSC',
            'Branch Name',
            'Credit Limit',
            'Credit Days',
            'Avg Monthly Purchase',
            'Outstanding Balance',
            'Preferred Payment Method',
            'Cancelled Cheque',
            'Monthly Sales',
            'Product Categories',
            'Secondary Sales Required',
            'Last 12 Months Sales',
            'Sales Executive ID (JSON)',
            'Sales Executive Names',
            'Supervisor ID',
            'Supervisor Name',
            'Customer Segment',
            'Weekly TAI Alert',
            'Target vs Achievement',
            'Schemes Updates',
            'New Launch Update',
            'Payment Alert',
            'Pending Orders',
            'Inventory Status',
            'Turnover',
            'Staff Strength',
            'Vehicles Capacity',
            'Area Coverage',
            'Other Brands Handled',
            'Warehouse Size',
            'Created At',
            'Updated At',
        ];
        return array_merge($headings, $remaining);
    }

    public function map($distributor): array
    {

        $salesExecutiveNames = collect($distributor->salesExecutives())
        ->pluck('name')
        ->implode(', ');

        // Supervisor Name
        $supervisorName = optional($distributor->supervisor)->name;
$shippingAddresses = $distributor->shipping_address;
        // $shippingAddresses = json_decode($distributor->shipping_address, true);

        $shippingAddresses = is_array($shippingAddresses)
            ? $shippingAddresses
            : [];

        return [
            $distributor->id,
            $distributor->distributor_code,
            $distributor->plant,
            $distributor->legal_name,
            $distributor->trade_name,
            $distributor->category,
            $distributor->business_status,
            $distributor->business_start_date,
            $distributor->shop_image ? 'Yes' : 'No',
            $distributor->profile_image ? 'Yes' : 'No',
            $distributor->contact_person,
            $distributor->designation,
            $distributor->mobile,
            $distributor->alternate_mobile,
            $distributor->email,
            $distributor->secondary_email,
            $distributor->billing_address,
            $distributor->billing_city_export_name,
            $distributor->billing_city_export_id,

            $distributor->billing_district_export_name,
            $distributor->billing_district_export_id,

            $distributor->billing_state_export_name,
            $distributor->billing_state_export_id,

            $distributor->billing_country_export_name,
            $distributor->billing_country_export_id,

            $distributor->billing_pincode_export_name,
            $distributor->billing_pincode_export_id,
            // $distributor->shipping_address,
            // is_array(json_decode($distributor->shipping_address, true))
            //     ? collect(json_decode($distributor->shipping_address, true))
            //         ->map(fn($item, $index) => ($index + 1) . '. ' . $item)
            //         ->implode("\n")
            //     : $distributor->shipping_address,
            ...array_pad(
                $shippingAddresses,
                $this->maxShippingAddresses,
                ''
            ),
            // $distributor->shipping_city,
            // $distributor->shipping_district,
            // $distributor->shipping_state,
            // $distributor->shipping_country,
            // $distributor->shipping_pincode,
            $distributor->sales_zone,
            $distributor->area_territory,
            $distributor->beat_route,
            $distributor->beat_id,
            $distributor->market_classification,
            $distributor->competitor_brands,
            $distributor->gst_number,
            $distributor->pan_number,
            $distributor->registration_type,
            $distributor->documents ? 'Yes (Multiple)' : 'No',
            $distributor->bank_name,
            $distributor->account_holder,
            $distributor->account_number,
            $distributor->ifsc,
            $distributor->branch_name,
            $distributor->credit_limit,
            $distributor->credit_days,
            $distributor->avg_monthly_purchase,
            $distributor->outstanding_balance,
            $distributor->preferred_payment_method,
            $distributor->cancelled_cheque ? 'Yes' : 'No',
            $distributor->monthly_sales,
            $distributor->product_categories,
            $distributor->secondary_sales_required,
            $distributor->last_12_months_sales,
            $distributor->sales_executive_id, // JSON stored hai
            $salesExecutiveNames,
            $distributor->supervisor_id,
            $supervisorName,
            $distributor->customer_segment,
            $distributor->weekly_tai_alert,
            $distributor->target_vs_achievement,
            $distributor->schemes_updates,
            $distributor->new_launch_update,
            $distributor->payment_alert,
            $distributor->pending_orders,
            $distributor->inventory_status,
            $distributor->turnover,
            $distributor->staff_strength,
            $distributor->vehicles_capacity,
            $distributor->area_coverage,
            $distributor->other_brands_handled,
            $distributor->warehouse_size,
            $distributor->created_at?->format('d-m-Y H:i'),
            $distributor->updated_at?->format('d-m-Y H:i'),
        ];
    }
}
