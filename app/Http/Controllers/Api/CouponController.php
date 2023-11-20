<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Validator;
use Gate;
use App\Models\Coupons;
use App\Models\Wallet;
use App\Models\InvalidCoupons;

class CouponController extends Controller
{
     public function __construct()
    {
        $this->coupons = new Coupons();
        
        
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }

    public function couponScans(Request $request)
    {
        try
        { 
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'coupons.*' => 'required',
            ]); 
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }
            $data = collect($request['coupons']);
            $data = $data->map(function ($item) use($user) {
                    return collect([
                        'customer_id'  =>  $user['id'],
                        'coupon_code'  =>   $item,   
                    ]);
                });
            $response = couponScans($data);
            return response()->json($response, $this->successStatus); 
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }

    public function getScanedCoupons(Request $request)
    {
        try
        { 
            $user = $request->user();
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $query = Wallet::where('customer_id', $user_id)
                            ->whereNotNull('coupon_code')
                            ->select('id','coupon_code','transaction_at','points')->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if($db_data->isNotEmpty())
            {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'wallet_id' => isset($value['id']) ? $value['id'] : 0,
                        'coupon_code' => isset($value['coupon_code']) ? $value['coupon_code'] : '',
                        'points' => isset($value['points']) ? $value['points'] : 0,
                        'transaction_at' => isset($value['transaction_at']) ? showdatetimeformat($value['transaction_at']) : '',
                    ]);
                }
            }

            $errorquery = InvalidCoupons::where('customer_id', $user_id)
                            ->whereNotNull('coupon_code')
                            ->select('id','coupon_code','created_at','status_id')->latest();
            $invalid_data = (!empty($pageSize)) ? $errorquery->paginate($pageSize) : $errorquery->get();
            $invalidData = collect([]);
            if($invalid_data->isNotEmpty())
            {
                foreach ($invalid_data as $key => $rows) {
                    $invalidData->push([
                        'transaction_id' => isset($rows['id']) ? $rows['id'] : 0,
                        'coupon_code' => isset($rows['coupon_code']) ? $rows['coupon_code'] : '',
                        'status' => isset($rows['status']['status_name']) ? $rows['status']['status_name'] : '',
                        'transaction_at' => isset($rows['created_at']) ? showdatetimeformat($rows['created_at']) : '',
                    ]);
                }
            }
            if(!empty($data) || !empty($invalidData))
            {
                return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data , 'invalid' => $invalidData ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);  
            
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }
}
