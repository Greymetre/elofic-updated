<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Status;

class LeadController extends Controller
{
    public function getLeads(Request $request)
    {
        $user = $request->user();
        $pageSize = $request->input('pageSize', 10);
        $reporting_users = getUsersReportingToAuth($user->id);
        $leads = Lead::query();
        if (!$user->hasRole('superadmin')) {
            $leads->where(function ($query) use ($reporting_users) {
                $query->whereIn('created_by', $reporting_users)
                    ->orWhereIn('assign_to', $reporting_users);
            });
        }
        $leads = $leads->orderBy('created_at', 'desc')->paginate($pageSize);

        $lead_status = Status::where('module', 'LeadStatus')->get();
        $counts = [];
        $counts['total'] = $leads->count();
        $counts['pending'] = $leads->where('status', 0)->count();
        foreach ($lead_status as $status) {
            $counts[strtolower($status->display_name)] = $leads->where('status', $status->id)->count();
        }

        $leads = $leads->map(function ($lead) {
            return [
                'id' => $lead->id,
                'name' => $lead->company_name,
                'address' => $lead->address ? $lead->address->full_address : '',
                'status' => [
                    'id' => $lead->status_is ? $lead->status_is->id : 0,
                    'display_name' => $lead->status_is ? $lead->status_is->display_name : 'Pending',
                ],
                'contact' => [
                    'name' => $lead->contacts->first()->name ?? null,
                    'phone_number' => $lead->contacts->first()->phone_number ?? null,
                    'email' => $lead->contacts->first()->email ?? null,
                    'url' => $lead->contacts->first()->url ?? null,
                    'lead_source' => $lead->contacts->first()->lead_source ?? null,
                ],
                'note' => $lead->notes->first()->note ?? null,
                'created_at' => $lead->created_at->toDateTimeString(),
            ];
        });

        

        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $leads, 'counts' => $counts], 200);
    }
}