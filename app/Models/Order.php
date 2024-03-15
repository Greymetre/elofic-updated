<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [ 'active', 'buyer_id', 'seller_id', 'total_qty', 'shipped_qty', 'orderno', 'order_date', 'completed_date', 'total_gst', 'sub_total', 'grand_total', 'order_taking','status_id', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at','beatscheduleid' , 'suc_del','order_remark'];

    public function message()
    {
        return [
            'buyer_id.required' => 'Enter Buyer',
            'seller_id.required' => 'Enter Seller',
            'orderno.required' => 'Enter Invoice No',
            'order_date.required' => 'Enter Invoice Date',
            'grand_total.required' => 'Enter Invoice Amount',
        ];
    }

    public function insertrules()
    {
        return [
            'buyer_id' => 'required|exists:customers,id',
            'seller_id' => 'required|exists:customers,id',
        ];
    }
    public function updaterules($id ='')
    {
        return [
            'buyer_id' => 'required|exists:customers,id',
            'seller_id' => 'required|exists:customers,id',
        ];
    }

    public function save_data($request)
    {
        try
        {
            
            $created_at = getcurentDateTime();
            $request['orderno'] = !empty($request['orderno']) ? $request['orderno'] : date('Y').'_'.$request['seller_id'].'_'.autoIncrementId('Order','id');

                 if(!empty($request['buyer_id'])){
                   $buyer = $request['buyer_id'];
                   }else{
                    $buyer = $request['seller_id'];
                   }

            if( $order_id = Order::insertGetId([
                'active' => 'Y',
                // 'buyer_id' => isset($request['buyer_id'])? $request['buyer_id']:null,
                // 'seller_id' => isset($request['seller_id'])? $request['seller_id']:null,
                'buyer_id' => isset($request['seller_id'])? $request['seller_id']:null,
                'seller_id' => $buyer,
                 'total_qty' => 0,
                 //'total_qty' => isset($request['quantity'])? $request['quantity']:0,
                'shipped_qty' => 0,
                'orderno' => isset($request['orderno'])? $request['orderno'] :'',
                'order_date' => isset($request['order_date'])? $request['order_date']:getcurentDate(),
                'total_gst' => isset($request['total_gst'])? $request['total_gst']:0.00,
                'sub_total' => isset($request['sub_total'])? $request['sub_total']:0.00,
                'grand_total' => isset($request['grand_total'])?  $request['grand_total']:0.00,
                'order_taking' => isset($request['order_taking'])?  $request['order_taking']:'MobileApp',
                'suc_del' => isset($request['suc_del'])?  $request['suc_del']:'',  
                'beatscheduleid' => isset($request['beatscheduleid']) ? $request['beatscheduleid'] :null,   
                'created_by' => isset($request['created_by']) ? $request['created_by'] :null,     
                'order_remark' => isset($request['order_remark']) ? $request['order_remark'] :null,     
                'created_at' => $created_at ,
                'updated_at' => $created_at
            ]) )
            {
                return $response = array('status' => 'success', 'message' => 'Sales Insert Successfully','order_id' => $order_id);
            }
            return $response = array('status' => 'error', 'message' => 'Error in Sales Store');
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

    public function sellers()
    {
        return $this->belongsTo('App\Models\Customers', 'seller_id', 'id')->select('id','name', 'first_name', 'last_name','mobile','email');
    }
    public function buyers()
    {
        return $this->belongsTo('App\Models\Customers', 'buyer_id', 'id')->select('id','name', 'first_name', 'last_name','mobile','email','customertype','executive_id');
    }

    public function customeraddress()
    {
        return $this->belongsTo('App\Models\Address', 'buyer_id', 'customer_id')->select('id','address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id','district_id' ,'city_id', 'pincode_id','zipcode');
    }

    public function orderdetails()
    {
        return $this->hasMany('App\Models\OrderDetails', 'order_id', 'id')->select('id','order_id', 'product_id', 'product_detail_id','quantity', 'shipped_qty', 'price', 'tax_amount', 'line_total', 'status_id');
    }

    public function address()
    {
       return $this->belongsTo('App\Models\Address', 'address_id', 'id')->select('id','address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id','district_id' ,'city_id', 'pincode_id');
    }

    public function statename()
    {
        return $this->belongsTo('App\Models\State', 'state_id', 'id')->select('id', 'state_name');
    }

    public function statusname()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name','display_name');
    }


    public function getuserdetails()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }



}
