<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\PromotionalActivity;
use App\Models\ActivityAttendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PromotionalActivitiesController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'activity_approved_by' => 'required|exists:users,id',
            'activity_date' => 'required|date',
            'target_market' => 'required',
            'product_category' => 'required',
            'activity_type' => 'required',

            'customer_type' => 'required',

            'discussion_Points' => 'nullable|array',
            'material_and_Samples' => 'nullable|array',
            'feedback' => 'nullable|array',

            'attendees' => 'nullable|array',
            'attendees.*.person_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            $activity = PromotionalActivity::create([

                'activity_approved_by' => $request->activity_approved_by,
                'activity_date' => $request->activity_date,
                'target_market' => $request->target_market,
                'product_category' => $request->product_category,
                'activity_type' => $request->activity_type,
                'total_order_qty' => $request->total_order_qty,
                'total_order_amount' => $request->total_order_amount,
                'order_confirm_by' => $request->order_confirm_by,
                'customer_type' => $request->customer_type,
                'total_participants' => $request->total_participants,
                'customer_count' => $request->customer_count,
                'retailer_id' => $request->retailer_id,
                'distributor_id' => $request->distributor_id,

                // Convert array to comma separated string
                'discussion_Points' => $request->discussion_Points
                    ? json_encode($request->discussion_Points)
                    : null,
                
                'material_and_Samples' => $request->material_and_Samples
                    ? json_encode($request->material_and_Samples)
                    : null,
                
                'feedback' => $request->feedback
                    ? json_encode($request->feedback)
                    : null,

                'managers_remarks' => $request->managers_remarks,

                'created_by' => auth()->id()
            ]);

            if ($request->has('attendees')) {

                foreach ($request->attendees as $attendee) {

                    ActivityAttendee::create([
                        'activity_id' => $activity->id,
                        'person_name' => $attendee['person_name'] ?? null,
                        'contact_no' => $attendee['contact_no'] ?? null,
                        'address' => $attendee['address'] ?? null,
                        'remarks' => $attendee['remarks'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Promotional Activity Created Successfully',
                'data' => $activity->load('attendees')
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
    
            'activity_approved_by' => 'required|exists:users,id',
            'activity_date' => 'required|date',
            'target_market' => 'required',
            'product_category' => 'required',
            'activity_type' => 'required',
            'customer_type' => 'required',
    
            'discussion_Points' => 'nullable|array',
            'material_and_Samples' => 'nullable|array',
            'feedback' => 'nullable|array',
    
            'attendees' => 'nullable|array',
            'attendees.*.person_name' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }
    
        DB::beginTransaction();
    
        try {
    
            $activity = PromotionalActivity::find($id);
    
            if (!$activity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Promotional Activity not found'
                ], 404);
            }
    
            $activity->update([
    
                'activity_approved_by' => $request->activity_approved_by,
                'activity_date' => $request->activity_date,
                'target_market' => $request->target_market,
                'product_category' => $request->product_category,
                'activity_type' => $request->activity_type,
                'total_order_qty' => $request->total_order_qty,
                'total_order_amount' => $request->total_order_amount,
                'order_confirm_by' => $request->order_confirm_by,
                'customer_type' => $request->customer_type,
                'total_participants' => $request->total_participants,
                'customer_count' => $request->customer_count,
                'retailer_id' => $request->retailer_id,
                'distributor_id' => $request->distributor_id,
    
                'discussion_Points' => $request->discussion_Points,
                'material_and_Samples' => $request->material_and_Samples,
                'feedback' => $request->feedback,
    
                'managers_remarks' => $request->managers_remarks,
            ]);
    
            /*
            |--------------------------------------------------------------------------
            | Update Attendees
            |--------------------------------------------------------------------------
            */
    
            if ($request->has('attendees')) {
    
                ActivityAttendee::where('activity_id', $activity->id)->delete();
    
                foreach ($request->attendees as $attendee) {
    
                    ActivityAttendee::create([
                        'activity_id' => $activity->id,
                        'person_name' => $attendee['person_name'] ?? null,
                        'contact_no' => $attendee['contact_no'] ?? null,
                        'address' => $attendee['address'] ?? null,
                        'remarks' => $attendee['remarks'] ?? null,
                    ]);
                }
            }
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Promotional Activity Updated Successfully',
                'data' => $activity->fresh()->load('attendees')
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
   public function index(Request $request)
{
    $query = PromotionalActivity::query();

    if ($request->filled('search')) {

        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where('target_market', 'like', "%{$search}%")
                ->orWhere('product_category', 'like', "%{$search}%")
                ->orWhere('activity_type', 'like', "%{$search}%")
                ->orWhere('customer_type', 'like', "%{$search}%")
                ->orWhere('managers_remarks', 'like', "%{$search}%");
        });
    }

    if ($request->filled('activity_type')) {
        $query->where('activity_type', $request->activity_type);
    }

    if ($request->filled('start_date')) {
        $query->whereDate('activity_date', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('activity_date', '<=', $request->end_date);
    }

    /*
    |--------------------------------------------------------------------------
    | Activity Counts
    |--------------------------------------------------------------------------
    */
    
    $activityCounts = PromotionalActivity::selectRaw('activity_type, COUNT(*) as total')
    ->groupBy('activity_type')
    ->pluck('total', 'activity_type')
    ->toArray();
    
    
    // $activityCounts = (clone $query)
    //     ->selectRaw('activity_type, COUNT(*) as total')
    //     ->groupBy('activity_type')
    //     ->pluck('total', 'activity_type')
    //     ->toArray();

    $activities = $query
        ->select([
            'id',
            'target_market',
            'activity_type',
            'activity_date',
            'total_order_qty',
            'total_order_amount',
            'total_participants',
            'activity_approved_by'
        ])
        ->with([
            'approvedBy:id,name'
        ])
        ->latest()
        ->paginate($request->per_page ?? 10);

    return response()->json([
        'status' => true,
        'message' => 'Promotional Activities List',

        'activity_counts' => [
            'Tent Meet'     => $activityCounts['Tent Meet'] ?? 0,
            'Van Activity'  => $activityCounts['Van Activity'] ?? 0,
            'Mechanic Meet' => $activityCounts['Mechanic Meet'] ?? 0,
            'Retailer Meet' => $activityCounts['Retailer Meet'] ?? 0,
        ],

        'data' => $activities
    ]);
}
    
    public function show($id)
    {
        $activity = PromotionalActivity::with([
            'approvedBy:id,name',
            'createdBy:id,name',
            'retailer:id,shop_name',
            'distributor:id,trade_name',
            'attendees'
        ])->find($id);
    
        if (!$activity) {
            return response()->json([
                'status' => false,
                'message' => 'Promotional Activity not found'
            ], 404);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Promotional Activity Details',
            'data' => [
                'id' => $activity->id,
        
                'activity_approved_by' => $activity->activity_approved_by,
                'activity_approved_by_name' => optional($activity->approvedBy)->name,
        
                'created_by' => $activity->created_by,
                'created_by_name' => optional($activity->createdBy)->name,
        
                'retailer_id' => $activity->retailer_id,
                'retailer_name' => optional($activity->retailer)->shop_name,
        
                'distributor_id' => $activity->distributor_id,
                'distributor_name' => optional($activity->distributor)->trade_name,
        
                'activity_date' => $activity->activity_date,
                'target_market' => $activity->target_market,
                'product_category' => $activity->product_category,
                'activity_type' => $activity->activity_type,
                'total_order_qty' => $activity->total_order_qty,
                'total_order_amount' => $activity->total_order_amount,
                'order_confirm_by' => $activity->order_confirm_by,
                'customer_type' => $activity->customer_type,
                'total_participants' => $activity->total_participants,
                'customer_count' => $activity->customer_count,
                'discussion_Points' => $activity->discussion_Points,
                'material_and_Samples' => $activity->material_and_Samples,
                'feedback' => $activity->feedback,
                'managers_remarks' => $activity->managers_remarks,
                'created_at' => $activity->created_at,
                'updated_at' => $activity->updated_at,
        
                'attendees' => $activity->attendees
            ]
        ]);
    }
}
