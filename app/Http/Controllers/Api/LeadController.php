<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class LeadController extends Controller
{
    public function getLeads(Request $request)
    {
        $user = $request->user();
        $pageSize = $request->input('pageSize', 10);
        $reporting_users = getUsersReportingToAuth($user->id);
        $leads = Lead::with(['contacts', 'assign_user', 'notes', 'tasks', 'opportunities', 'status_is', 'address', 'media', 'createdby']);

        if (!$user->hasRole('superadmin')) {
            $leads->where(function ($query) use ($reporting_users) {
                $query->whereIn('created_by', $reporting_users)
                    ->orWhereIn('assign_to', $reporting_users);
            });
        }

        $leads = $leads->orderBy('created_at', 'desc')->paginate($pageSize);

        return response()->json($leads);
    }
}