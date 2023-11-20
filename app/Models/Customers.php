<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [ 'active', 'name', 'first_name', 'last_name', 'mobile', 'email', 'password', 'notification_id', 'latitude', 'longitude', 'device_type', 'gender', 'profile_image', 'customer_code', 'status_id', 'region_id', 'customertype', 'firmtype', 'created_by', 'updated_by', 'executive_id', 'deleted_at', 'created_at', 'updated_at', 'beatscheduleid', 'manager_name', 'manager_phone'];

    public function message()
    {
        return [
            'name.required' => 'Enter Firm Name',
        ];
    }

    public function insertrules()
    {
        
        return [
            'name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            'email' => 'nullable|email|unique:customers,email',
            'mobile'        => 'required|min:6666666666|max:9999999999|numeric|unique:customers,mobile',
        ];
    }
    public function updaterules($id ='')
    {
        return [
            'name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            //'email' => 'email|unique:customers,email',
            //'mobile' => 'required|numeric|unique:customers,mobile,'.$id,
        ];
    }

    public function save_data($request)
    {
        try
        {
            
            $created_at = getcurentDateTime();
            if(strlen(preg_replace('/\s+/', '', $request['mobile'])) == 10)
            {
                $request['mobile'] = '+91'.preg_replace('/\s+/', '', $request['mobile']);
            }
            if( $customer_id = Customers::insertGetId([
                'active' => 'Y',
                'name' => !empty($request['name'])? ucfirst($request['name']):'',
                'first_name' => !empty($request['first_name'])? ucfirst($request['first_name']):'',
                'last_name' => !empty($request['last_name'])? ucfirst($request['last_name']):'',
                'mobile' => $request['mobile'],
                'email' => !empty($request['email'])? $request['email']:null,
                'password' => !empty($request['password'])? Hash::make($request['password']) :'',
                'notification_id' => !empty($request['notification_id'])? $request['notification_id']:'',
                'latitude' => !empty($request['latitude'])? $request['latitude']:null,
                'longitude' => !empty($request['longitude'])? $request['longitude']:null,
                'device_type' => !empty($request['device_type'])? ucfirst($request['device_type']):'',
                'gender' => !empty($request['gender'])? ucfirst($request['gender']):'',
                'customer_code' => !empty($request['customer_code'])? $request['customer_code']:'',
                'profile_image' =>  !empty($request['profile_image'])? $request['profile_image'] :'',
                'status_id' =>  !empty($request['status_id'])? $request['status_id'] :2,
                'customertype' =>  !empty($request['customertype'])? $request['customertype'] :1,
                'firmtype' =>  !empty($request['firmtype'])? $request['firmtype'] :null,
                'executive_id' =>  !empty($request['executive_id'])? $request['executive_id'] : $request['created_by'],
                'created_by' =>  !empty($request['created_by'])? $request['created_by'] :null,
                'manager_name' => !empty($request['manager_name'])? $request['manager_name'] :'',
                'manager_phone' => !empty($request['manager_phone'])? $request['manager_phone'] :'',
                'created_at' => $created_at ,
                'updated_at' => $created_at
            ]) )
            {
                return $response = array('status' => 'success', 'message' => 'Customer Insert Successfully','customer_id' => $customer_id);
            }
            return $response = array('status' => 'error', 'message' => 'Error in Customer Store');
        }
        catch(\Exception $e)
        {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }

    public function update_data($request)
    {
        try
        {
            if(strlen(preg_replace('/\s+/', '', $request['mobile'])) == 10)
            {
                $request['mobile'] = '+91'.preg_replace('/\s+/', '', $request['mobile']);
            }
            
            $customers = Customers::find($request['customer_id']);
            $customers->name = !empty($request['name'])? $request['name'] :'';
            $customers->first_name = !empty($request['first_name'])? ucfirst($request['first_name']):'';
            $customers->last_name = !empty($request['last_name'])? ucfirst($request['last_name']):'';
            $customers->gender = !empty($request['gender'])? ucfirst($request['gender']):'';
            $customers->customer_code = !empty($request['customer_code'])? $request['customer_code']:'';
            $customers->customertype =  !empty($request['customertype'])? $request['customertype'] :1;
            $customers->firmtype = !empty($request['firmtype'])? $request['firmtype']:null;
            $customers->executive_id = !empty($request['executive_id'])? $request['executive_id']:null;
            if($request['password'])
            {
                $customers->password = !empty($request['password'])? Hash::make($request['password']) :'';
            }
            if(!empty($request['mobile']))
            {
                $customers->mobile = $request['mobile'];
            }
            if(!empty($request['email']))
            {
                $customers->email = !empty($request['email'])? $request['email'] :null;
            }
            if(!empty($request['profile_image']))
            {
                $customers->profile_image = !empty($request['profile_image'])? $request['profile_image'] :'';
            }
            if(!empty($request['manager_name']))
            {
                $customers->manager_name = !empty($request['manager_name'])? $request['manager_name'] :'';
            }
            if(!empty($request['manager_phone']))
            {
                $customers->manager_phone = !empty($request['manager_phone'])? $request['manager_phone'] :'';
            }
            if(!empty($request['latitude']) && !empty($request['longitude']))
            {
                $customers->latitude = !empty($request['latitude'])? $request['latitude'] :null;
                $customers->longitude = !empty($request['longitude'])? $request['longitude'] :null;
            }
            $customers->updated_at = getcurentDateTime();
            if($customers->save())
            {
                return $response = array('status' => 'success', 'message' => 'User Update Successfully');
            } 
            return $response = array('status' => 'error', 'message' => 'Error in User Profile Update');
        }
        catch(\Exception $e)
        {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name','profile_image');
    }

    public function employeename()
    {
        return $this->belongsTo('App\Models\User', 'executive_id', 'id')->select('id','name');
    }

    public function customertypes()
    {
        return $this->belongsTo('App\Models\CustomerType', 'customertype', 'id')->select('id','customertype_name');
    }

    public function firmtypes()
    {
        return $this->belongsTo('App\Models\FirmType', 'firmtype', 'id')->select('id','firmtype_name');
    }

    public function customerdetails()
    {
        return $this->belongsTo('App\Models\CustomerDetails', 'id', 'customer_id')->select('customer_id', 'gstin_no', 'pan_no', 'aadhar_no', 'otherid_no', 'enrollment_date', 'approval_date','shop_image','visiting_card','grade','visit_status');
    }

    public function customeraddress()
    {
        return $this->belongsTo('App\Models\Address', 'id', 'customer_id')->select('id','address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id','district_id' ,'city_id', 'pincode_id','zipcode');
    }
    public function addresslists()
    {
        return $this->hasMany('App\Models\Address','customer_id','id')->select('id','address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id','district_id' ,'city_id', 'pincode_id');
    }

    public function customerdocuments()
    {
        return $this->hasMany('App\Models\Attachment','customer_id','id')->select('customer_id','file_path','document_name');
    }

    public function statusname()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name');
    }

    public function beatdetails()
    {
        return $this->belongsTo('App\Models\BeatCustomer','id','customer_id')->select('beat_id','customer_id');
    }

    public function surveys()
    {
        return $this->hasMany('App\Models\SurveyData','customer_id','id')->select('field_id', 'customer_id', 'value');
    }
    public function visitsinfo()
    {
        return $this->hasMany('App\Models\VisitReport','customer_id','id')->orderBy('created_at', 'desc')->select('id', 'customer_id', 'description','report_title','visit_image','user_id','created_at');
    }

    public function customerdeals()
    {
        return $this->hasMany('App\Models\DealIn','customer_id','id')->select('customer_id', 'types', 'hcv', 'mav', 'lmv', 'lcv', 'other', 'tractor');
    }
}
