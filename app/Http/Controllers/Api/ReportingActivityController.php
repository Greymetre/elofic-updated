<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\CheckIn;
use App\Models\Customers;
use App\Models\Order;
use App\Models\User;
use Validator;

class ReportingActivityController extends Controller
{
    public function allReportingUsers(Request $request){
        $user = $request->user();
        $user_id = $user->id;
        $pageSize = $request->input('pageSize');
        $search_name = $request->input('search_name');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $validator = Validator::make($request->all(), [
            'end_date' => 'required_with:start_date',
        ]); 
        if ($validator->fails()) {
            return response()->json(['status' => 'error','message' =>  $validator->errors()], 400); 
        }

        $all_ids = getUsersReportingToAuth();
        $all_reporting_user_ids = User::query();
        if($search_name){
            $all_reporting_user_ids->where('name', 'LIKE', '%'.$search_name.'%');
        }
        $all_reporting_user_ids = $all_reporting_user_ids->whereIn('id', $all_ids)->pluck('id')->toArray();

        
        $date_checkIn = Attendance::select('punchin_date', 'user_id')
        ->with('users')
        ->whereIn('user_id', $all_reporting_user_ids);
        if($start_date && $start_date != '' && $start_date != null){
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date));
            $date_checkIn->whereBetween('punchin_date', [$start_date, $end_date]);
        }
        $date_checkIn->orderBy('punchin_date', 'desc');
        
        
        $date_checkIn = (!empty($pageSize)) ? $date_checkIn->paginate($pageSize) : $date_checkIn->paginate(100);
        
        $all_user_details = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $branches= array();
        $all_users= array();
        $all_branch= array();
        foreach ($all_user_details as $k => $val) {
            if(!in_array($val->getbranch->id, $all_branch)){
                array_push($all_branch, $val->getbranch->id);
                $branches[$k]['id'] = $val->getbranch->id;
                $branches[$k]['name'] = $val->getbranch->branch_name;
            }
            $all_users[$k]['id'] = $val->id;
            $all_users[$k]['name'] = $val->name;
        }
        
        $data = array();
        if(count($date_checkIn) > 0){
            foreach($date_checkIn as $key=>$checkIn){
                $data[$key]['user_id'] = $checkIn->users->id;
                $data[$key]['name'] = $checkIn->users->name;
                $data[$key]['date'] = date('d/m/Y', strtotime($checkIn->punchin_date));
            }
            return response()->json(['status' => 'success','message' => 'Data retrieved successfully.', 'users'=>$all_users, 'branches'=>$branches, 'page_count'=>$date_checkIn->lastPage(), 'data' => $data ], 200);
        }else{
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);
        }
    }


    public function userActivity(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required',
        ]); 
        if ($validator->fails()) {
            return response()->json(['status' => 'error','message' =>  $validator->errors()], 400); 
        }
        $date = date('Y-m-d', strtotime($request->input('date')));
        $user_id = $request->input('user_id');

        $punchInOut = Attendance::where('user_id', $user_id)->where('punchin_date', $date)->get();
        $checkInOut = CheckIn::with('visitreports')->with('customers')->where('user_id', $user_id)->where('checkin_date', $date)->get();
        $orders = Order::with('buyers')->where('created_by', $user_id)->whereRaw('DATE(created_at)="'.$date.'"')->get();
        $customer_add = Customers::with('customeraddress')->where('created_by', $user_id)->whereRaw('DATE(created_at)="'. $date.'"')->get();
        $customer_update = Customers::with('customeraddress')->where('created_by', $user_id)->whereColumn('updated_at','>','created_at')->whereRaw('DATE(updated_at)="'. $date.'"')->get();

        $punchInData = array();
        $punchOutData = array();
        $checkInData = array();
        $checkOutData = array();
        $orderData = array();
        $customerAddData = array();
        $customerUpdateData = array();

        foreach($punchInOut as $k=>$val){
            if($val->punchin_time != null){
                $punch_in_city = getLatLongToCity($val->punchin_latitude, $val->punchin_longitude);
                $punchInData[$k]['title'] = 'Punch In';
                $punchInData[$k]['time'] = $val->punchin_time;
                $punchInData[$k]['latitude'] = $val->punchin_latitude!=null?$val->punchin_latitude:'';
                $punchInData[$k]['longitude'] = $val->punchin_longitude!=null?$val->punchin_longitude:'';
                $punchInData[$k]['msg'] = $val->punchin_summary.' - '.$punch_in_city;
            }
            if($val->punchout_time != null){
                $punchOutData[$k]['title'] = 'Punch Out';
                $punchOutData[$k]['time'] = $val->punchout_time;
                $punchOutData[$k]['latitude'] = $val->punchout_latitude!=null?$val->punchout_latitude:'';
                $punchOutData[$k]['longitude'] = $val->punchout_longitude!=null?$val->punchout_longitude:'';
                $punchOutData[$k]['msg'] = $val->punchout_address;
            }
        }

        foreach($checkInOut as $k=>$val){
            if($val->checkin_time != null){
                $check_in_city = getLatLongToCity($val->checkin_latitude, $val->checkin_longitude);
                $checkInData[$k]['title'] = 'Check In';
                $checkInData[$k]['time'] = $val->checkin_time;
                $checkInData[$k]['latitude'] = $val->checkin_latitude!=null?$val->checkin_latitude:'';
                $checkInData[$k]['longitude'] = $val->checkin_longitude!=null?$val->checkin_longitude:'';
                $checkInData[$k]['msg'] = $val->customers->name.' - '.$check_in_city;
            }
            if($val->checkout_time != null){
                $check_out_city = getLatLongToCity($val->checkout_latitude, $val->checkout_longitude);
                $checkOutData[$k]['title'] = 'Check Out';
                $checkOutData[$k]['time'] = $val->checkout_time;
                $checkOutData[$k]['latitude'] = $val->checkout_latitude!=null?$val->checkout_latitude:'';
                $checkOutData[$k]['longitude'] = $val->checkout_longitude!=null?$val->checkout_longitude:'';
                $checkOutData[$k]['msg'] = $val->customers->name.' - '.$check_out_city.' Remark - '.$val->visitreports->description;
            }
        }

        foreach ($orders as $k => $val) {
            $orderData[$k]['title'] = 'Order Collect';
            $orderData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
            $orderData[$k]['latitude'] = '';
            $orderData[$k]['longitude'] = '';
            $orderData[$k]['msg'] = $val->buyers->name.' - '.$val->buyers->customeraddress->cityname->city_name.', Qty : '.$val->orderdetails->sum('quantity').', Total : '.$val->grand_total;
        }

        foreach ($customer_add as $k => $val) {
            $customerAddData[$k]['title'] = 'New Customer Registration';
            $customerAddData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
            $customerAddData[$k]['latitude'] = $val->latitude!=null?$val->latitude:'';
            $customerAddData[$k]['longitude'] = $val->longitude!=null?$val->longitude:'';
            $customerAddData[$k]['msg'] = $val->name.' - '. $val->customeraddress->cityname->city_name;
        }

        foreach ($customer_update as $k => $val) {
            $customerUpdateData[$k]['title'] = 'Customer Edit';
            $customerUpdateData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
            $customerUpdateData[$k]['latitude'] = $val->latitude!=null?$val->latitude:'';
            $customerUpdateData[$k]['longitude'] = $val->longitude!=null?$val->longitude:'';
            $customerUpdateData[$k]['msg'] = $val->name.' - '. $val->customeraddress->cityname->city_name;
        }

        $data = array_merge($punchInData, $punchOutData, $checkInData, $checkOutData, $orderData, $customerAddData, $customerUpdateData);

        
        usort($data, function ($a, $b) {
            return strtotime($a['time']) - strtotime($b['time']);
        });
        foreach($data as $k=>$val){
            $data[$k]['time'] = date('h:i A', strtotime($val['time']));
            $data[$k]['date'] = $request->input('date');
        }
    
        if(count($data) > 0){
            return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data ], 200);
        }else{
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);
        }
    }
}
