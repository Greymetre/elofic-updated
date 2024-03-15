<?php

namespace App\Http\Controllers\Api;

use Gate;
use Excel;
use App\Exports\TransactionHistoryExport;
use Validator;
use App\Models\Branch;
use App\Models\Services;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\DataTables\TransactionHistoryDataTable;
use App\Models\SchemeDetails;
use App\Models\SchemeHeader;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Redemption;

class TransactionHistoryController extends Controller
{

    public function __construct()
    {
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

    public function getcouponhistory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $pageSize = $request->input('pageSize');
            $query = TransactionHistory::with('scheme_details')->where(function ($query) use ($request) {
                $query->where('customer_id', $request->id);
                if ($request->search && $request->search != '' && $request->search != null) {
                    $query->where('product_name', 'LIKE', "%{$request->search}%")->orWhere('display_name', 'LIKE', "%{$request->search}%");
                }
                if ($request->category_id && $request->category_id != '' && $request->category_id != null) {
                    $query->where('category_id', $request->category_id);
                }
                if ($request->subcategory_id && $request->subcategory_id != '' && $request->subcategory_id != null) {
                    $query->where('subcategory_id', $request->subcategory_id);
                }
                if ($request->min_range && $request->min_range != '' && $request->min_range != null && $request->max_range && $request->max_range != '' && $request->max_range != null) {
                    $query->whereBetween('points', [$request->min_range, $request->max_range]);
                }
            })->orderBy('created_at', 'desc');
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $total_points = TransactionHistory::where('customer_id', $request->id)->sum('point') ?? 0;
            $active_points = TransactionHistory::where('customer_id', $request->id)->where('status', '1')->sum('point') ?? 0;
            $provision_points = TransactionHistory::where('customer_id', $request->id)->where('status', '0')->sum('point') ?? 0;

            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'scheme_name' => isset($value['scheme_details']) ? $value['scheme_details']['scheme_name'] : '',
                        'coupon_code' => isset($value['coupen_code']) ? $value['coupen_code'] : '',
                        'status' => isset($value['status']) ? $value['status'] : '',
                        'point' => isset($value['point']) ? $value['point'] : '',
                        'date' => isset($value['created_at']) ? date('d M Y', strtotime($value['created_at'])) : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'total_points' => $total_points, 'active_points' => $active_points, 'provision_points' => $provision_points, 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'total_points' => $total_points, 'active_points' => $active_points, 'provision_points' => $provision_points, 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getredemptionhistory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $pageSize = $request->input('pageSize');
            $query = Redemption::with('product')->with('neft_details')->where(function ($query) use ($request) {
                $query->where('customer_id', $request->id);
                if ($request->search && $request->search != '' && $request->search != null) {
                    $query->where('product_name', 'LIKE', "%{$request->search}%")->orWhere('display_name', 'LIKE', "%{$request->search}%");
                }
                if ($request->category_id && $request->category_id != '' && $request->category_id != null) {
                    $query->where('category_id', $request->category_id);
                }
                if ($request->subcategory_id && $request->subcategory_id != '' && $request->subcategory_id != null) {
                    $query->where('subcategory_id', $request->subcategory_id);
                }
                if ($request->min_range && $request->min_range != '' && $request->min_range != null && $request->max_range && $request->max_range != '' && $request->max_range != null) {
                    $query->whereBetween('points', [$request->min_range, $request->max_range]);
                }
            })->orderBy('created_at', 'desc');
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $total_redemption = Redemption::where('customer_id', $request->id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
            $active_points = TransactionHistory::where('customer_id', $request->id)->where('status', '1')->sum('point') ?? 0;
            $total_rejected = Redemption::where('customer_id', $request->id)->where('status', '2')->sum('redeem_amount') ?? 0;
            $total_balance = (int)$active_points - (int)$total_redemption;

            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'redeem_mode' => isset($value['redeem_mode']) ? $value['redeem_mode'] : '',
                        'coupon_code' => isset($value['coupen_code']) ? $value['coupen_code'] : '',
                        'status' => isset($value['status']) ? $value['status'] : '',
                        'point' => isset($value['redeem_amount']) ? $value['redeem_amount'] : '',
                        'date' => isset($value['updated_at']) ? date('d M Y', strtotime($value['updated_at'])) : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'total_redemption' => $total_redemption, 'total_rejected' => $total_rejected, 'total_balance' => $total_balance, 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'success', 'total_redemption' => $total_redemption, 'total_rejected' => $total_rejected, 'total_balance' => $total_balance, 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getBankDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $customer = Customers::with('customerdocuments')->with('customerdetails')->find($request->id);

            // $data = collect([]);
            if ($customer) {
                $data['bank_details']['account_holder'] = $customer->customerdetails ? $customer->customerdetails->account_holder : '';
                $data['bank_details']['account_number'] = $customer->customerdetails ? $customer->customerdetails->account_number : '';
                $data['bank_details']['bank_name'] = $customer->customerdetails ? $customer->customerdetails->bank_name : '';
                $data['bank_details']['ifsc_code'] = $customer->customerdetails ? $customer->customerdetails->ifsc_code : '';
                $data['bank_details']['passbook_image'] = !empty($customer['customerdocuments']->where('document_name', 'bankpass')->pluck('file_path')->first()) ? asset('uploads/' . $customer['customerdocuments']->where('document_name', 'bankpass')->pluck('file_path')->first()) : url('/') . '/' . asset('assets/img/placeholder.jpg');
                $data['bank_details']['status'] = $customer->customerdetails ? $customer->customerdetails->bank_status : 0;
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'success', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addNeftRedemption(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'redeem_amount' => 'required|numeric',
                'account_holder' => 'required',
                'account_number' => 'required',
                'bank_name' => 'required',
                'ifsc_code' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $data = Redemption::create([
                'customer_id' => $request->customer_id,
                'redeem_mode' => '2',
                'account_holder' => $request->account_holder,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'ifsc_code' => $request->ifsc_code,
                'redeem_amount' => $request->redeem_amount,
            ]);
            return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
          
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addSerialNumber(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'coupen_code.*' => 'required',
            ]);
            $validator->setAttributeNames([
                'coupen_code.*' => 'coupon code',
            ]);

            $validator->setCustomMessages([
                'coupen_code.*.required' => 'All coupon code fields are required.',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $nonNullCoupenCodes = array_filter($request->coupen_code, function ($value) {
                return !is_null($value);
            });
            $expire_schemes = array();
            $no_schemes = array();
            foreach ($nonNullCoupenCodes as $nonNullCoupenCode) {
                $exists = TransactionHistory::where('coupen_code', $nonNullCoupenCode)->exists();
                $notexists = Services::where('serial_no', $nonNullCoupenCode)->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'coupen_code' => "The coupon code '$nonNullCoupenCode' already Scanned.",
                    ]);
                }
                if (!$notexists) {
                    throw ValidationException::withMessages([
                        'coupen_code' => "The coupon code '$nonNullCoupenCode' is Invalid.",
                    ]);
                }
                $scheme = Services::where('serial_no', $nonNullCoupenCode)->first();
                $scheme_details = SchemeDetails::where('product_id', $scheme->product->id)->first();
                $point = 0;
                if($scheme_details){
                    $scheme_id = $scheme_details->scheme_id;
                    $start_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->start_date);
                    $end_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->end_date);
                    $current_date = Carbon::today();
                    if ($current_date->isSameDay($start_date) || ($current_date->gte($start_date) && $current_date->lte($end_date))) {
                        $point = ($scheme_details) ? $scheme_details->points : NULL;
                    } else {
                        array_push($expire_schemes, $nonNullCoupenCode);
                        $point = '0';
                    }
                }else{
                    array_push($no_schemes, $nonNullCoupenCode);
                    $scheme_id = null;
                }
                $tHistory = TransactionHistory::create([
                    'customer_id' => $request->customer_id,
                    'coupen_code' => $nonNullCoupenCode,
                    'scheme_id' => $scheme_id,
                    'point' => $point,
                ]);
            }
            if (count($expire_schemes) > 0) {
                return response(['status' => 'success', 'message' => 'Transaction History Store Successfully but coupon code (' . implode(',', $expire_schemes) . ') scheme has either expired or has not started yet so you earned 0 point.'], 200);
            } elseif(!$scheme_details) {
                return response(['status' => 'success', 'message' => 'Transaction History Store Successfully but no any scheme on coupon code (' . implode(',', $no_schemes) . ') so you earned 0 point.'], 200);
            }else{
                return response(['status' => 'success', 'message' => 'Transaction History Store Successfully'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
