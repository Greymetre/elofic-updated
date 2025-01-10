<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrimarySales;
use App\Models\Product;
use App\Models\SapStock;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class SapStockController extends Controller
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

    public function insertSapStock(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'itm_code' => 'required',
                'itm_desc' => 'required',
                'itm_grp_code' => 'required',
                'itm_grp_name' => 'required',
                'warehouse_code' => 'required',
                'warehouse_name' => 'required',
                'instock_qty' => 'required',
                'value' => 'required',
                'itm_remarks' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $product = Product::where('sap_code', $request['itm_code'])->first();

            // if (!$product) {
            //     Log::channel('sapstock')->error("Product with itm_code '{$request['itm_code']}' is not available in our system.");

            //     return response()->json([
            //         'status' => 'error',
            //         'message' => "Product with itm_code '{$request['itm_code']}' is not available in our system."
            //     ], $this->notFound);
            // } else {
            //     $wareHouse = WareHouse::where('warehouse_code', $request['warehouse_code'])->first();

            //     if (!$wareHouse) {
            //         Log::channel('sapstock')->error("WareHouse with warehouse_code '{$request['warehouse_code']}' is not available in our system.");

            //         return response()->json([
            //             'status' => 'error',
            //             'message' => "WareHouse with warehouse_code '{$request['warehouse_code']}' is not available in our system."
            //         ], $this->notFound);
            //     } else {
                    SapStock::updateorCreate([
                        'product_sap_code' => $request['itm_code'],
                        'warehouse_code' => $request['warehouse_code'],
                    ], [
                        'product_description' => $request['itm_desc'],
                        'product_category_sap_code' => $request['itm_grp_code'],
                        'product_category_name' => $request['itm_grp_name'],
                        'warehouse_name' => $request['warehouse_name'],
                        'instock_qty' => $request['instock_qty'],
                        'value' => $request['value'],
                        'itm_remarks' => $request['itm_remarks'],
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => "Data updated successfully."
                    ], $this->successStatus);
                // }
            // }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function insertSapSell(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'branch_code' => 'required',
                'sinv_branch' => 'required',
                'document_status' => 'required',
                'canceled' => 'required',
                'sinv_no' => 'required',
                'sinv_dt' => 'required',
                'bp_code' => 'required',
                'bp_name' => 'required',
                'billto_city' => 'required',
                'billto_state' => 'required',
                'item_no' => 'required',
                'item_desc' => 'required',
                'group_code' => 'required',
                'itm_group_name' => 'required',
                'sinv_total_qty' => 'required',
                'list_price' => 'required',
                'sinv_unit_price' => 'required',
                'sinv_price' => 'required',
                'tax_code' => 'required',
                'sinv_gst_amt' => 'required',
                'emp_code' => 'required',
                'sinv_sales_emp' => 'required',
                'sinv_remarks' => 'required',
                'serial_no' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $invoice_date = date('Y-m-d', strtotime($request['sinv_dt']));
            $month = date('M', strtotime($request['sinv_dt']));
            $month = strtoupper(substr($month, 0, 3));
            $itm_group_name_array = explode(' ', $request['itm_group_name']);
            $division = $itm_group_name_array[0];

            PrimarySales::create([
                'branch_id' => $request['branch_code'],
                'final_branch' => $request['sinv_branch'],
                'document_status' => $request['document_status'],
                'canceled' => $request['canceled'],
                'invoiceno' => $request['sinv_no'],
                'invoice_date' => $invoice_date,
                'month' => $month,
                'division' => $division,
                'customer_id' => $request['bp_code'],
                'dealer' => $request['bp_name'],
                'city' => $request['billto_city'],
                'state' => $request['billto_state'],
                'item_no' => $request['item_no'],
                'product_name' => $request['item_desc'],
                'group_code' => $request['group_code'],
                'itm_group_name' => $request['itm_group_name'],
                'quantity' => $request['sinv_total_qty'],
                'lp' => $request['list_price'],
                'rate' => $request['sinv_unit_price'],
                'net_amount' => $request['sinv_price'],
                'tax_code' => $request['tax_code'], 
                'sinv_gst_amt' => $request['sinv_gst_amt'],
                'emp_code' => $request['emp_code'],
                'sales_person' => $request['sinv_sales_emp'],
                'remarks' => $request['sinv_remarks'],
                'serial_no' => $request['serial_no']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Data updated successfully."
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
