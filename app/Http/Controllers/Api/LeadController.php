<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\LeadContact;
use App\Models\LeadNote;
use App\Models\Status;
use DB;

class LeadController extends Controller
{
    public function getLeads(Request $request)
    {
        $user = $request->user();
        $pageSize = (int) $request->input('pageSize', 10);

        // -----------------------
        // DATA (filtered)
        // -----------------------
        $listQuery = Lead::query()
            ->with(['address', 'status_is', 'contacts', 'notes']);

        if (!$user->hasRole('superadmin')) {
            $reporting_users = getUsersReportingToAuth($user->id);
            $listQuery->where(function ($q) use ($reporting_users) {
                $q->whereIn('created_by', $reporting_users)
                    ->orWhereIn('assign_to', $reporting_users);
            });
        }

        if ($request->filled('search')) {
            $listQuery->where('company_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $listQuery->where('status', (int) $request->status);
        }

        $leads = $listQuery->latest()->paginate($pageSize);

        // Shape items
        $leads = $leads->map(function ($lead) {
            return [
                'id' => $lead->id,
                'name' => $lead->company_name,
                'address' => $lead->address ? $lead->address->full_address : '',
                'city' => $lead->address ? $lead->address->cityname->city_name : '',
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

        // -----------------------
        // COUNTS (UNFILTERED)
        // -----------------------
        $leadStatus = Status::where('module', 'LeadStatus')->select('id', 'display_name')->get();

        $grouped = Lead::select('status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status'); // [status_id => count]

        $counts = [
            ['id' => -1, 'display_name' => 'total',   'count' => Lead::count()],
            ['id' => 0,  'display_name' => 'pending', 'count' => $grouped[0] ?? 0],
        ];

        foreach ($leadStatus as $s) {
            $counts[] = [
                'id' => $s->id,
                'display_name' => strtolower($s->display_name),
                'count' => $grouped[$s->id] ?? 0,
            ];
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data retrieved successfully.',
            'data'    => $leads,
            'counts'  => $counts,
        ], 200);
    }

    public function leadStatusSource(Request $request)
    {
        $status = Status::where('module', 'LeadStatus')->select('id', 'display_name')->get();
        $source = [
            [
                'key' => 'Google',
                'value' => 'Google'
            ],
            [
                'key' => 'Facebook',
                'value' => 'Facebook'
            ],
            [
                'key' => 'Instagram',
                'value' => 'Instagram'
            ],
            [
                'key' => 'Indiamart',
                'value' => 'Indiamart'
            ],
            [
                'key' => 'Justdial',
                'value' => 'Justdial'
            ],
            [
                'key' => 'Self',
                'value' => 'Self'
            ],
        ];
        $data = [
            'status' => $status,
            'source' => $source,
        ];
        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
    }

    public function leadCreate(Request $request)
    {
        $validate = validator($request->all(), [
            'status' => 'required',
            'lead_source' => 'required',
            'company_name' => 'required',
            'contact_name' => 'required',
            'phone_number' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        if ($request->other) {
            $otherData = [
                'others' => $request->other,
            ];
            $otherData = json_encode($otherData, JSON_UNESCAPED_UNICODE);
        } else {
            $otherData = null;
        }
        $user = $request->user();
        $lead = Lead::create([
            'company_name' => $request->company_name,
            'status' => $request->status ?? 0,
            'created_by' => $user->id,
            'lead_generation_date' => date('Y-m-d'),
            'lead_source' => $request->lead_source,
            'assign_to' => $user->id,
            'others' => $otherData,
        ]);
        if ($lead->id) {
            Address::create([
                'model_type' => 'App\Models\Lead',
                'model_id' => $lead->id,
                'address1' => $request->address ?? 'N/A',
                'country_id' => 1,
                'pincode_id' => $request->pincode_id ?? null,
                'state_id' => $request->state_id ?? null,
                'city_id' => $request->city_id ?? null,
                'district_id' => $request->district_id ?? null,
                'created_by' => $user->id,
            ]);
            $category = LeadContact::create([
                'name' => $request->contact_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'lead_source' => $request->lead_source,
                'lead_id' => $lead->id,
                'created_by' => $user->id
            ]);
            if (isset($request->note) && !empty($request->note)) {
                $note = LeadNote::create([
                    'note' => $request->note,
                    'lead_id' => $lead->id,
                    'created_by' => $user->id
                ]);
            }
            return response()->json(['status' => 'success', 'message' => 'Lead created successfully.', 'data' => $lead], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }
}
