<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = ['complaint_number','complaint_date','seller','end_user_id','party_name','product_laying','service_center','assign_user','product_id','product_serail_number','product_code','category','specification','product_no','phase','seller_branch','purchased_branch','product_group','company_sale_bill_no','company_sale_bill_date','customer_bill_date','customer_bill_no','company_bill_date_month','under_warranty','service_type','customer_bill_date_month','warranty_bill','fault_type','service_centre_remark','remark','division','register_by','complaint_type','description','created_at','updated_at'];

    public $timestamps = true;

    public function party()
    {
        return $this->belongsTo(Customers::class, 'party_name', 'id');
    }

    public function service_center_details()
    {
        return $this->belongsTo(Customers::class, 'service_center', 'id');
    }

    public function seller_details()
    {
        return $this->belongsTo(Customers::class, 'seller', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(EndUser::class, 'end_user_id', 'id');
    }

    public function complaint_type_details()
    {
        return $this->belongsTo(ComplaintType::class, 'complaint_type', 'id');
    }
}
