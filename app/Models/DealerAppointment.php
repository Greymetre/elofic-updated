<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerAppointment extends Model
{
    use HasFactory;

    protected $fillable = ['branch','district','city','appointment_date','customertype','division','SDPUMPMOTORS','SDF&A','gst_type','gst_no','firm_type','firm_name','cin_no','related_firm_name','line_business','office_address','office_pincode','office_mobile','office_email','godown_address','godown_pincode','godown_mobile','godown_email','status','ppd_name_1','ppd_adhar_1','ppd_pan_1','ppd_name_2','ppd_adhar_2','ppd_pan_2','ppd_name_3','ppd_adhar_3','ppd_pan_3','ppd_name_4','ppd_adhar_4','ppd_pan_4','contact_person_name','mobile_email','bank_name','bank_address','account_type','account_number','ifsc_code','payment_term','credit_period','cheque_no_1','cheque_account_number_1','cheque_bank_1','cheque_no_2','cheque_account_number_2','cheque_bank_2','manufacture_company_1','manufacture_product_1','manufacture_business_1','manufacture_turn_over_1','manufacture_company_2','manufacture_product_2','manufacture_business_2','manufacture_turn_over_2','present_annual_turnover','motor_anticipated_business_1','motor_next_year_business_1','pump_anticipated_business_1','pump_next_year_business_1','F&A_anticipated_business_1','F&A_next_year_business_1','lighting_anticipated_business_1','lighting_next_year_business_1','agri_anticipated_business_1','agri_next_year_business_1','solar_anticipated_business_1','solar_next_year_business_1','anticipated_business_total', 'created_at', 'updated_at'];

    public $timestamps = true;


    public function branch_details()
    {
     return $this->belongsTo(Branch::class, 'branch', 'id');
    }

    public function district_details()
    {
     return $this->belongsTo(District::class, 'district', 'id');
    }

    public function city_details()
    {
     return $this->belongsTo(City::class, 'city', 'id');
    }
}
