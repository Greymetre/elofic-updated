<?php

namespace App\Http\Controllers\Api;

use App\Exports\OrderEmailExport;
use App\Http\Controllers\Controller;
use App\Mail\OrderMailWithAttachment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Validator;
use Gate;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Attachment;
use App\Models\Cart;
use App\Models\Customers;
use App\Models\User;
use Excel;
use Illuminate\Support\Facades\Mail;
use stdClass;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->orders = new Order();
        
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

    public function getOrderList(Request $request)
    {
        try
        { 
            $user = $request->user();
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $query = $this->orders->where(function ($query) use($user_id) {
                                        $query->where('created_by', '=', $user_id);
                                    })
                                    ->select('id','buyer_id','seller_id','total_qty','shipped_qty','orderno','order_date','completed_date','total_gst','sub_total','grand_total')->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if($db_data->isNotEmpty())
            {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'order_id' => isset($value['id']) ? $value['id'] : 0,
                        'seller_id' => isset($value['seller_id']) ? $value['seller_id'] : 0,
                        'seller_name' => isset($value['sellers']['name']) ? $value['sellers']['name'] : '',
                        'buyer_id' => isset($value['buyer_id']) ? $value['buyer_id'] : 0,
                        'buyer_name' => isset($value['buyers']['name']) ? $value['buyers']['name'] : '',
                        // 'total_qty' => isset($value['total_qty']) ? $value['total_qty'] : 0,
                        'total_qty' => $value->orderdetails->sum('quantity')?? 0,
                        'shipped_qty' => isset($value['shipped_qty']) ? $value['shipped_qty'] : 0,
                        'orderno' => isset($value['orderno']) ? $value['orderno'] : '',
                        'order_date' => isset($value['order_date']) ? $value['order_date'] : '',
                        'completed_date' => isset($value['completed_date']) ? $value['completed_date'] : '',
                        'grand_total' => isset($value['grand_total']) ? $value['grand_total'] : 0.00,
                        'sub_total' => isset($value['sub_total']) ? $value['sub_total'] : 0.00,
                    ]);
                }
                return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);  
            
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }

    public function getOrderDetails(Request $request)
    {
        try
        { 
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
            ]); 
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }
            $user = $request->user();
            $user_id = $user->id;
            $order_id = $request->input('order_id');
            $data = $this->orders->with('orderdetails','orderdetails.products','orderdetails.productdetails')->where('id', $order_id)->select('id','seller_id','total_qty','shipped_qty','orderno','order_date','completed_date','total_gst','sub_total','grand_total','buyer_id')->first();

            if(!empty($data['orderdetails']))
            {
                $orderdetails = collect([]);
                foreach ($data['orderdetails'] as $key => $value) {
                    $orderdetails->push([
                        'orderdetail_id' => isset($value['id']) ? $value['id'] : 0,
                        'product_id' =>  isset($value['product_id']) ? $value['product_id'] : 0,
                        'product_name' =>  isset($value['products']['product_name']) ? $value['products']['product_name'] : '',
                        'product_image' =>  isset($value['products']['product_image']) ? $value['products']['product_image'] : '',
                        'product_detail_id' =>  isset($value['product_detail_id']) ? $value['product_detail_id'] : $value['product_id'],
                        //'detail_title' =>  isset($value['productdetails']['detail_title']) ? $value['productdetails']['detail_title'] : '',
                        'detail_title' =>  isset($value['products']['product_no']) ? $value['products']['product_no'] : '',
                        'quantity' =>  isset($value['quantity']) ? $value['quantity'] : 0,
                        'shipped_qty'  =>  isset($value['shipped_qty']) ? $value['shipped_qty'] : 0,
                        'price'  =>  isset($value['price']) ? $value['price'] : 0.00,
                        'tax_amount'  =>  isset($value['tax_amount']) ? $value['tax_amount'] : 0.00,
                        'line_total'  =>  isset($value['line_total']) ? $value['line_total'] : 0.00,
                        'status_id'  =>  isset($value['status_id']) ? $value['status_id'] : 0,
                        'specification' => isset($value['products']['suc_del']) ? $value['products']['suc_del'] : '',
                        'part_no' => isset($value['products']['part_no']) ? $value['products']['part_no'] : '',
                        'product_no' => isset($value['products']['product_no']) ? $value['products']['product_no'] : '',
                        'hp' => isset($value['products']['specification']) ? $value['products']['specification'] : '',
                    ]);
                }
                unset($data['orderdetails']);
                $data['seller_name'] = isset($data['sellers']['name']) ? $data['sellers']['name'] : '';
                $data['buyer_name'] = isset($data['buyers']['name']) ? $data['buyers']['name'] : '';
                $data['orderdetails'] = $orderdetails;
                return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);  
            
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }  
    }

    public function insertOrder(Request $request)
    {
        try
        { 
            $user = $request->user();
            $request['created_by'] = $user->id;
            $validator = Validator::make($request->all(), $this->orders->insertrules(), $this->orders->message()); 
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' => $validator->messages()->all()],$this->badrequest);
            }
            $response =  $this->orders->save_data($request);
            if($response['status'] == 'success')
            {
                $orderdetail = collect([]);
                foreach ($request->orderdetail as $key => $rows) {
                    $orderdetail->push([
                        'active' => 'Y',
                        'order_id' => isset($response['order_id']) ? $response['order_id'] :null,
                        'product_id' => isset($rows['product_id']) ? $rows['product_id'] :null,
                        'product_detail_id' => isset($rows['product_detail_id']) ? $rows['product_detail_id'] :null,
                        'quantity' => isset($rows['quantity']) ? $rows['quantity'] :0,
                        'discount' => isset($rows['discount']) ? $rows['discount'] :0.00,
                        'discount_amount' => isset($rows['discount_amount']) ? $rows['discount_amount'] :0.00,
                        'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] :0,
                        'price' => isset($rows['price']) ? $rows['price'] :0.00,
                        'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] :0.00,
                        'line_total' => isset($rows['line_total']) ? $rows['line_total'] :0.00,
                        'created_at' => getcurentDateTime(),
                    ]);
                }

                if($orderdetail->isNotEmpty())
                {
                    OrderDetails::insert($orderdetail->toArray());
                    $exportData = new Request();
                    $exportData->merge([
                        'order_id' => $response['order_id'],
                    ]);
    
                    Excel::store(new OrderEmailExport($exportData), '/assets/orderDetails.xlsx', 'local');
    
                    if($user->userinfo->order_mails  && $user->userinfo->order_mails != null && $user->userinfo->order_mails != ''){
                        $mail_id_array = explode(',', $user->userinfo->order_mails);
                        $buyer = Customers::find($request['buyer_id']);
                        $seller = Customers::find($request['seller_id']);
                        $attachmentPath = base_path('storage/app/assets/orderDetails.xlsx');
                        foreach ($mail_id_array as $k => $val) {
                        Mail::to($val)->send(new OrderMailWithAttachment($attachmentPath, $orderdetail, Order::find($response['order_id'])));
                        }
                    }
                }
                // $useractivity = array(
                //     'userid' => $user->id, 
                //     'latitude' => $request['latitude'], 
                //     'longitude' => $request['longitude'], 
                //     'type' => 'Order',
                //     'description' => $user->name.' Order to Submited',
                // );
                // submitUserActivity($useractivity);
                 $customername = Customers::where('id','=',$request['buyer_id'])->pluck('name')->first();

                $adminnotify = collect([
                    'title' => 'Order collected',
                    'body' =>  $user->name.' has collected order at '.$customername
                ]);
                sendNotification(39,$adminnotify);

                $zsmnotify = collect([
                    'title' => 'Order collected',
                    'body' =>  $user->name.' has collected order at '.$customername
                ]);
                sendNotification($user->reportingid,$zsmnotify);
                $asmnotify = collect([
                    'title' => 'Order successfully placed',
                    'body' =>  'Your order is successfully placed at '.$customername
                ]);
                sendNotification($user->id,$asmnotify);
                return response()->json($response, $this->successStatus); 
            }
            return response()->json($response, $this->badrequest); 
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }

    public function addCartItems(Request $request)
    {
        try
        { 
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'product_detail_id' => 'required|exists:product_details,id',
            ]); 
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }

            $data =  Cart::create([
                'customer_id' => isset($request->customer_id) ? $request->customer_id : null,
                'product_id' => isset($request->product_id) ? $request->product_id : null,
                'product_detail_id' => isset($request->product_detail_id) ? $request->product_detail_id : null,
                'quantity' => isset($request->quantity) ? $request->quantity : 1,
                'price' => isset($request->price) ? $request->price : 0.00,
                'discount' => isset($request->discount) ? $request->discount : 0.00,
                'total' => isset($request->total) ? $request->total : 0.00,
                'user_id' => isset($request->user_id) ? $request->user_id : $user->id,
                'created_at' => getcurentDateTime(),
            ]);
            if($data)
            {
                return response(['status' => 'success', 'message' => 'Cart item added successfully.', 'data' => $data ],200); 
            } 
            return response(['status' => 'error', 'message' => 'Error in cart added.', 'data' => $data ],200); 
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }

    public function getCartItems(Request $request)
    {
        try
        { 
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
            ]); 
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }

            $data =  Cart::with(array('products' => function($query) {
                            $query->select('id','product_name','product_image');
                        }, 'productdetails' => function($query) {
                            $query->select('id','detail_title');
                        } ))->where('customer_id',$request->customer_id)->get();
            if($data)
            {
                $date = strtotime("+5 day");
                $expected_date = date('M d, Y', $date);
                return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data , 'expected_date' => $expected_date],200); 
            } 
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200); 
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }
}

