<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CallLogController extends Controller
{
    /**
     * Store a new call log.
     */
    public function store(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'lead_id'    => 'required|exists:leads,id',
                'started_at' => 'required|date',
                'duration'   => 'required|integer|min:0',
                'status'     => 'required|in:0,1', // 0 = No Response, 1 = Received
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422); // 422 Unprocessable Entity
            }

            $callLog = CallLog::create([
                'user_id'    => $request->user()->id,
                'lead_id'    => $request->lead_id,
                'number'     => $request->number,
                'started_at' => date('Y-m-d H:i:s', strtotime($request->started_at)),
                'duration'   => $request->duration ?? 0,
                'status'     => $request->status,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Call log created successfully',
                'data'    => $callLog,
            ], 201);
        } catch (\Exception $e) {
            Log::info($e);
            return response()->json([
                'success' => false,
                'message' => 'Call log created successfully',
                'data'    => [],
            ], 201);
        }
    }

    /**
     * Get call logs (optionally filter by user or lead).
     */
    public function index(Request $request)
    {
        $user_ids = getUsersReportingToAuth($request->user()->id);
        $users = User::select('id', 'name')->where('active', 'Y');
        $pageSize = $request->has('page_size') ? $request->page_size : 20;

        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $users = $users->whereIn('id', $user_ids);
        }
        $users = $users->get();

        // Base query
        $query = CallLog::with(['user:id,name', 'lead:id,company_name', 'lead.contacts:id,lead_id,name']);

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        if($request->has('date') && !empty($request->date)) {
            $query->whereDate('started_at', $request->date);
        }

        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $query->whereIn('user_id', $user_ids); // ✅ should be user_id not id
        }

        if ($request->has('lead_id')) {
            $query->where('lead_id', $request->lead_id);
        }

        // Clone query for totals (without pagination)
        $allLogs = (clone $query)->get();

        // Paginated logs
        $logs = $query->latest('started_at')->paginate($pageSize);

        // Totals for ALL logs
        $totalDuration = $allLogs->sum('duration');
        $callDialed    = $allLogs->count();
        $connected     = $allLogs->where('status', 1)->count();
        $noResponse    = $allLogs->where('status', 0)->count();

        foreach ($logs as $log) {
            $log->contact_name = $log->lead->contacts ? $log->lead->contacts->first()->name : '';
            $log->duration = gmdate('i:s', $log->duration);
        }

        // dd($totalDuration);

        return response()->json([
            'success' => true,
            'data'    => $logs->items(),
            'users'   => $users,
            'call_dialted'   => $callDialed,
            'connected'      => $connected,
            'no_response'    => $noResponse,
            'total_duration' => gmdate('H:i:s', $totalDuration),
        ]);
    }
}
