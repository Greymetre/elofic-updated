<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketingActivity;
use App\Models\MspActivity;
use Carbon\Carbon;
use App\Models\MspActivityCity;
use App\Models\MspActivityCustomer;

use Validator;

class MspActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->marketingActivity = new MarketingActivity();


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

    public function index()
    {
        try {
            $activities = $this->marketingActivity->select('id', 'type')->get(); // Use `get()` instead of `all()`
            
            return response()->json([
                'status' => true,
                'message' => 'Marketing activities retrieved successfully',
                'data' => $activities
            ], $this->successStatus);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], $this->internalError);
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'activity_date'  => 'required|date',
                'activity_type'  => 'required|integer',
                'msp_count'      => 'required|integer',
                'customers'      => 'required|array',
                'cities'         => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()
                ], $this->noContent);
            }

            // Format activity date
            $activityDate = Carbon::parse($request->activity_date);
            $month = $activityDate->format('m'); 
            $year  = $activityDate->format('Y'); 
            $nextYear = substr($year + 1, -2);
            $formattedYear = "$year-$nextYear";

            // Create Marketing Activity
            $activity = MspActivity::create([
                'emp_code'      => $request->user()->employee_codes ?? '',
                'fyear'         => $formattedYear,
                'month'         => $month,
                'msp_count'     => $request->msp_count,
                'activity_type' => $request->activity_type,
            ]);

            // Insert cities in bulk
            if (!empty($request->cities)) {
                $citiesData = collect($request->cities)->map(function ($city) use ($activity) {
                    return ['msp_activity_id' => $activity->id, 'city_id' => $city];
                })->toArray();

                MspActivityCity::insert($citiesData);
            }

            // Insert customers in bulk
            if (!empty($request->customers)) {
                $customersData = collect($request->customers)->map(function ($customer) use ($activity) {
                    return ['msp_activity_id' => $activity->id, 'customer_id' => $customer];
                })->toArray();

                MspActivityCustomer::insert($customersData);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Marketing activity added successfully',
                'data'    => $activity
            ], $this->successStatus);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage()
            ], $this->internalError);
        }
    }
}
