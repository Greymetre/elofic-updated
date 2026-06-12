<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDistributor extends Model
{
    use HasFactory;

    protected $table = 'master_distributors';
    

    protected $fillable = [

        /* ================= BASIC INFO ================= */
        'legal_name',
        'trade_name',
        'distributor_code',
        'category',
        'business_status',
        'business_start_date',
        'shop_image',
        'profile_image',

        /* ================= CONTACT ================= */
        'contact_person',
        'designation',
        'mobile',
        'alternate_mobile',
        'email',
        'secondary_email',

        /* ================= BILLING ADDRESS ================= */
        'billing_address',
        'billing_city',
        'billing_district',
        'billing_state',
        'billing_country',
        'billing_pincode',

        /* ================= SHIPPING ADDRESS ================= */
        'shipping_address',
        'shipping_city',
        'shipping_district',
        'shipping_state',
        'shipping_country',
        'shipping_pincode',
        'same_as_billing',

        /* ================= BUSINESS & OPERATION ================= */
        'sales_zone',
        'area_territory',
        'beat_route',
        'market_classification',
        'competitor_brands',

        /* ================= COMPLIANCE / KYC ================= */
        'gst_number',
        'pan_number',
        'registration_type',
        'documents',
        'mou_file',

        /* ================= BANKING ================= */
        'bank_name',
        'account_holder',
        'account_number',
        'ifsc',
        'branch_name',
        'credit_limit',
        'credit_days',
        'avg_monthly_purchase',
        'outstanding_balance',
        'preferred_payment_method',
        'cancelled_cheque',

        /* ================= SALES ================= */
        'monthly_sales',
        'product_categories',
        'secondary_sales_required',
        'last_12_months_sales',
        'sales_executive_id',
        'supervisor_id',
        'customer_segment',

        /* ================= ADDITIONAL ================= */
        'weekly_tai_alert',
        'target_vs_achievement',
        'schemes_updates',
        'new_launch_update',
        'payment_alert',
        'pending_orders',
        'inventory_status',

        /* ================= CAPACITY ================= */
        'turnover',
        'staff_strength',
        'vehicles_capacity',
        'area_coverage',
        'other_brands_handled',
        'warehouse_size',
        'beat_id'
        

        
    ];
    protected $casts = [
        'sales_executive_id' => 'array',
        'same_as_billing' => 'boolean',
        'shipping_address' => 'array',  
        
    ];

    // Multiple Sales Executives
    public function salesExecutives()
    {
        $ids = is_array($this->sales_executive_id) 
            ? $this->sales_executive_id 
            : json_decode($this->sales_executive_id, true) ?? [];

        return User::whereIn('id', $ids)->get();
    }

    // Supervisor (single)
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // Billing Address Accessors
public function getCountryIdAttribute()
{
    if ($this->billing_country) {
        return \App\Models\Country::where('country_name', $this->billing_country)->first()?->id;
    }
    return null;
}

public function getStateIdAttribute()
{
    if ($this->billing_state) {
        return \App\Models\State::where('state_name', $this->billing_state)->first()?->id;
    }
    return null;
}

public function getDistrictIdAttribute()
{
    if ($this->billing_district) {
        return \App\Models\District::where('district_name', $this->billing_district)->first()?->id;
    }
    return null;
}

public function getCityIdAttribute()
{
    if ($this->billing_city) {
        return \App\Models\City::where('city_name', $this->billing_city)->first()?->id;
    }
    return null;
}

public function getPincodeIdAttribute()
{
    if ($this->billing_pincode) {
        return \App\Models\Pincode::where('pincode', $this->billing_pincode)->first()?->id;
    }
    return null;
}

// Shipping Address Accessors
public function getShippingCountryIdAttribute()
{
    if ($this->shipping_country) {
        return \App\Models\Country::where('country_name', $this->shipping_country)->first()?->id;
    }
    return null;
}

public function getShippingStateIdAttribute()
{
    if ($this->shipping_state) {
        return \App\Models\State::where('state_name', $this->shipping_state)->first()?->id;
    }
    return null;
}

public function getShippingDistrictIdAttribute()
{
    if ($this->shipping_district) {
        return \App\Models\District::where('district_name', $this->shipping_district)->first()?->id;
    }
    return null;
}

public function getShippingCityIdAttribute()
{
    if ($this->shipping_city) {
        return \App\Models\City::where('city_name', $this->shipping_city)->first()?->id;
    }
    return null;
}

public function getShippingPincodeIdAttribute()
{
    if ($this->shipping_pincode) {
        return \App\Models\Pincode::where('pincode', $this->shipping_pincode)->first()?->id;
    }
    return null;
}

// Sales Executive IDs as array
public function getSalesExecutiveIdsAttribute()
{
    return is_array($this->sales_executive_id) 
        ? $this->sales_executive_id 
        : json_decode($this->sales_executive_id, true) ?? [];
}
public function orders()
{
    return $this->hasMany(\App\Models\Order::class, 'seller_id');
}


public function getBillingCountryExportNameAttribute()
{
    if (!$this->billing_country) {
        return null;
    }

    // if ID stored
    if (is_numeric($this->billing_country)) {
        return \App\Models\Country::find($this->billing_country)?->country_name;
    }

    // already name
    return $this->billing_country;
}

public function getBillingCountryExportIdAttribute()
{
    if (!$this->billing_country) {
        return null;
    }

    // if already ID
    if (is_numeric($this->billing_country)) {
        return $this->billing_country;
    }

    // if name stored
    return \App\Models\Country::where('country_name', $this->billing_country)
        ->value('id');
}

public function getBillingStateExportNameAttribute()
{
    if (!$this->billing_state) {
        return null;
    }

    if (is_numeric($this->billing_state)) {
        return \App\Models\State::find($this->billing_state)?->state_name;
    }

    return $this->billing_state;
}


public function getBillingStateExportIdAttribute()
{
    if (!$this->billing_state) {
        return null;
    }

    if (is_numeric($this->billing_state)) {
        return $this->billing_state;
    }

    return \App\Models\State::where('state_name', $this->billing_state)
        ->value('id');
}

public function getBillingDistrictExportNameAttribute()
{
    if (!$this->billing_district) {
        return null;
    }

    if (is_numeric($this->billing_district)) {
        return \App\Models\District::find($this->billing_district)?->district_name;
    }

    return $this->billing_district;
}

public function getBillingDistrictExportIdAttribute()
{
    if (!$this->billing_district) {
        return null;
    }

    if (is_numeric($this->billing_district)) {
        return $this->billing_district;
    }

    return \App\Models\District::where('district_name', $this->billing_district)
        ->value('id');
}

public function getBillingCityExportNameAttribute()
{
    if (!$this->billing_city) {
        return null;
    }

    if (is_numeric($this->billing_city)) {
        return \App\Models\City::find($this->billing_city)?->city_name;
    }

    return $this->billing_city;
}

public function getBillingCityExportIdAttribute()
{
    if (!$this->billing_city) {
        return null;
    }

    if (is_numeric($this->billing_city)) {
        return $this->billing_city;
    }

    return \App\Models\City::where('city_name', $this->billing_city)
        ->value('id');
}

public function getBillingPincodeExportNameAttribute()
{
    if (!$this->billing_pincode) {
        return null;
    }

    // Check if value exists as ID
    $pincodeById = \App\Models\Pincode::find($this->billing_pincode);

    if ($pincodeById) {
        return $pincodeById->pincode;
    }

    // Otherwise assume actual pincode stored
    return $this->billing_pincode;
}

public function getBillingPincodeExportIdAttribute()
{
    if (!$this->billing_pincode) {
        return null;
    }

    // Check if already valid ID
    $pincodeById = \App\Models\Pincode::find($this->billing_pincode);

    if ($pincodeById) {
        return $pincodeById->id;
    }

    // Otherwise find ID using actual pincode
    return \App\Models\Pincode::where(
        'pincode',
        $this->billing_pincode
    )->value('id');
}

public function state()
{
    return $this->belongsTo(\App\Models\State::class, 'billing_state');
}

public function city()
{
    return $this->belongsTo(\App\Models\City::class, 'billing_city');
}

public function district()
{
    return $this->belongsTo(\App\Models\District::class, 'billing_district');
}


public function beat()
{
    return $this->belongsTo(\App\Models\Beat::class, 'beat_id', 'id');
}
}
