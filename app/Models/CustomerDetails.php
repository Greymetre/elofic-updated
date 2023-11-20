<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDetails extends Model
{
    use HasFactory;

    protected $table = 'customer_details';

    protected $fillable = [  'active', 'customer_id', 'gstin_no', 'pan_no', 'aadhar_no', 'otherid_no', 'enrollment_date', 'approval_date', 'shop_image', 'visiting_card', 'grade', 'visit_status', 'deleted_at', 'created_at', 'updated_at'];

    public function save_data($request)
    {
        try
        {
            
            
            $customer = CustomerDetails::firstOrNew(array('customer_id' => $request['customer_id']));
            $customer->active = 'Y';
            $customer->customer_id = isset($request['customer_id'])? $request['customer_id']:null;
            $customer->gstin_no = isset($request['gstin_no'])? ucfirst($request['gstin_no']):'';
            $customer->pan_no = isset($request['pan_no'])? ucfirst($request['pan_no']):'';
            $customer->aadhar_no = isset($request['aadhar_no'])? ucfirst($request['aadhar_no']):'';
            $customer->otherid_no = isset($request['otherid_no'])? ucfirst($request['otherid_no']):'';
            $customer->enrollment_date = isset($request['enrollment_date'])? $request['enrollment_date']:null;
            $customer->approval_date = isset($request['approval_date'])? $request['approval_date']:null;
            if($request['visit_status'])
            {
                $customer->visit_status = isset($request['visit_status'])? $request['visit_status']:'';
            }
            if($request['grade'])
            {
                $customer->grade = isset($request['grade'])? $request['grade']:'';
            }
            $customer->created_at = getcurentDateTime();
            if($customer->save())
            {
                return $response = array('status' => 'success', 'message' => 'Profile Update Successfully');
            }
            return $response = array('status' => 'error', 'message' => 'Error in Profile Update');
        }
        catch(\Exception $e)
        {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }
}
