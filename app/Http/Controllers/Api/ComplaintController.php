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
use App\Models\WarrantyActivation;
use App\Models\TransactionHistory;
use App\Models\EndUser;
use App\Models\Wallet;
use App\Models\InvalidCoupons;
use App\Models\Pincode;
use App\Models\Product;
use App\Models\Services;
use App\Http\Controllers\SendNotifications;
use App\Models\ComplaintType;
use App\Models\Customers;

class ComplaintController extends Controller
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

    public function getComplaintType(Request $request)
    {
        try {
            
            $data = ComplaintType::all();
            if ($data) {
                return response()->json(['status' => 'success', 'data' => $data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'data not found', 'data' => null], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}