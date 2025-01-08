<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}
