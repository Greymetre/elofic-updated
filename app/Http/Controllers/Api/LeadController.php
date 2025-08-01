<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\LeadContact;
use App\Models\LeadNote;
use App\Models\LeadOpportunity;
use App\Models\LeadTask;
use App\Models\OpportunitieStatus;
use App\Models\Status;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth;

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
                'city' => $lead->address ? $lead->address?->cityname?->city_name : '',
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
            'company_name' => 'required',
            'contact_name' => 'required',
            'phone_number' => 'required',
            'status' => 'required|exists:statuses,id',
            'lead_source' => 'required|in:Google,Indiamart,Justdial,Instagram,Facebook,Self',
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
        if (isset($request->lead_id) && !empty($request->lead_id)) {
            $lead = Lead::find($request->lead_id);
            $lead->update([
                'company_name' => $request->company_name,
                'company_url' => $request->website,
                'status' => $request->status ?? 0,
                'lead_generation_date' => date('Y-m-d'),
                'lead_source' => $request->lead_source,
                'others' => $otherData,
            ]);
            Address::where('model_type', 'App\Models\Lead')->where('model_id', $lead->id)->update([
                'address1' => $request->address ?? 'N/A',
                'country_id' => 1,
                'pincode_id' => $request->pincode_id ?? null,
                'state_id' => $request->state_id ?? null,
                'city_id' => $request->city_id ?? null,
                'district_id' => $request->district_id ?? null,
            ]);
            LeadContact::where('lead_id', $lead->id)->update([
                'name' => $request->contact_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'url' => $request->url,
                'lead_source' => $request->lead_source,
            ]);
            LeadNote::where('lead_id', $lead->id)->update([
                'note' => $request->note,
            ]);
            return response()->json(['status' => 'success', 'message' => 'Lead updated successfully.']);
        } else {
            $lead = Lead::create([
                'company_name' => $request->company_name,
                'company_url' => $request->website,
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
                LeadContact::create([
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

    public function leadDetails(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        $lead = Lead::find($request->lead_id);
        if ($lead) {
            $data = [
                'id' => $lead->id,
                'company_name' => $lead->company_name,
                'contact_id' => $lead->contacts->first()->id ?? null,
                'contact_name' => $lead->contacts->first()->name ?? null,
                'website' => $lead->company_url,
                'phone_number' => $lead->contacts->first()->phone_number ?? null,
                'email' => $lead->contacts->first()->email ?? null,
                'address' => $lead->address?->full_address ?? null,
                'pincode' => $lead->address?->pincodename?->pincode ?? null,
                'pincode_id' => $lead->address?->pincodename?->id ?? null,
                'city' => $lead->address?->cityname?->city_name ?? null,
                'city_id' => $lead->address?->cityname?->id ?? null,
                'district' => $lead->address?->districtname?->district_name ?? null,
                'district_id' => $lead->address?->districtname?->id ?? null,
                'state' => $lead->address?->statename?->state_name ?? null,
                'state_id' => $lead->address?->statename?->id ?? null,
                'status' => $lead->status_is ? $lead->status_is->display_name : 'Pending',
                'status_id' => $lead->status_is ? $lead->status_is->id : '0',
                'lead_source' => $lead->lead_source,
                'note' => $lead->notes->first()->note ?? null,
                'lead_generation_date' => (
                    !empty($lead->lead_generation_date) && $lead->lead_generation_date != '0000-00-00'
                    ? date('d M Y', strtotime($lead->lead_generation_date))
                    : $lead->created_at->format('d M Y')
                ),
                'updated_at' => $lead->updated_at->format('d M Y'),
            ];
            $lead_notes = LeadNote::where(['lead_id' => $lead->id])->get();
            $lead_tasks = LeadTask::where(['lead_id' => $lead->id])->get();
            $lead_notes->each(function ($item) {
                $item->type = 'note';
                $item->created_at_formatted = $item->created_at->format('d M Y');
            });
            $lead_tasks->each(function ($item) {
                $item->type = 'task';
                $item->created_at_formatted = $item->created_at->format('d M Y');
            });

            $combined = $lead_notes->merge($lead_tasks)->sortByDesc('created_at')->values();
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'notes_tasks' => $combined], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data not found.']);
        }
    }

    public function addNote(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id' => 'required',
            'note' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        if($request->note_id && !empty($request->note_id)){
            $note = LeadNote::find($request->note_id);
            if ($note) {
                $note->update(['note' => $request->note]);
                return response()->json(['status' => 'success', 'message' => 'Note updated successfully.', 'data' => $note], 200);
            }else{
                return response()->json(['status' => 'error', 'message' => 'Note not found.']);
            }

        }else{
            $lead = Lead::find($request->lead_id);
            if ($lead) {
                $note = LeadNote::create([
                    'note' => $request->note,
                    'lead_id' => $lead->id,
                    'created_by' => $request->user()->id
                ]);
                return response()->json(['status' => 'success', 'message' => 'Note added successfully.', 'data' => $note], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Lead not found.']);
            }
        }
    }

    public function getTaskDropdowns(Request $request)
    {
        $priorities = [
            [
                "id" => "low",
                "name" => "Low"
            ],
            [
                "id" => "medium",
                "name" => "Medium"
            ],
            [
                "id" => "high",
                "name" => "High"
            ]
        ];
        $status = [
            [
                "id" => "open",
                "name" => "Open"
            ],
            [
                "id" => "in_progress",
                "name" => "In Progress"
            ],
            [
                "id" => "completed",
                "name" => "Completed"
            ]
        ];
        $user_ids = getUsersReportingToAuth($request->user()->id);
        $users = User::select('id', 'name');
        if ($request->user()->hasRole('superadmin')) {
            $users->where(function ($query) use ($user_ids) {
                $query->whereIn('id', $user_ids);
            });
        }
        $users = $users->get();
        $data = [
            'users' => $users,
            'priorities' => $priorities,
            'status' => $status,
        ];

        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
    }

    public function addleadTask(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id'=>'required',
            'assigned_to'=>'required',
            'description'=>'required',
            'date'=>'required',
            'priority'=>'required',
            'status'=>'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        $created_by = $request->user()->id; 
        $task_id = $request->task_id;
        $lead_task = LeadTask::where(['id'=>$task_id])->first();
        if(!$request->status && empty($request->status)){
            $request->status = 'open';
        }
        if($lead_task){
             $lead_task->update(['assigned_to'=>$request->assigned_to,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'description'=>$request->description,'date'=>$request->date,'time'=>$request->time, 'priority'=>$request->priority,'status'=>$request->status]);
             $new = false;
        }else{
            $lead_task = LeadTask::create(['assigned_to'=>$request->assigned_to,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'description'=>$request->description,'date'=>$request->date,'time'=>$request->time, 'priority'=>$request->priority,'status'=>$request->status]);
            $new = true;
        }
        if($request->status == 'open'){
            $lead_task->update(['open_date'=>date('Y-m-d')]);
        }
        if($request->status == 'completed'){
            $lead_task->update(['close_date'=>date('Y-m-d')]);
        }
        if($new){
            return response()->json(['status' => 'success', 'message' => 'Task added successfully.', 'data' => $lead_task], 200);
        }else{
            return response()->json(['status' => 'success', 'message' => 'Task updated successfully.', 'data' => $lead_task], 200);
        }
    }

    public function getLeadContacts(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        $lead = Lead::find($request->lead_id);
        $opportunity_statuses = OpportunitieStatus::select('id', 'status_name')->orderBy('ordering', 'asc')->get();
        if ($lead) {
            $contacts = $lead->contacts;
            $data = [
                'contacts' => $contacts,
                'opportunity_statuses' => $opportunity_statuses
            ];
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data not found.']);
        }
    }

    public function addLeadopportunity(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id'=>'required',
            'assigned_to'=>'required',
            'lead_contact_id'=>'required',
            'amount'=>'required',
            //'type'=>'required',
            'estimated_close_date'=>'required',
            'confidence'=>'required',
            'note'=>'required',
            'status'=>'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        $created_by = Auth::id(); 
        $opportunity_id = $request->opportunity_id;
        $lead_opportunity = LeadOpportunity::where(['id'=>$opportunity_id])->first();
        if($lead_opportunity){
            $lead_opportunity->update(['note'=>$request->note,'created_by'=>$created_by,'assigned_to'=>$request->assigned_to,'lead_contact_id'=>$request->lead_contact_id,'estimated_close_date'=>$request->estimated_close_date,'confidence'=>$request->confidence,'status'=>$request->status, 'amount'=>$request->amount]);
            $new = false;
        }else{
            $lead_opportunity = LeadOpportunity::create(['note'=>$request->note,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'assigned_to'=>$request->assigned_to,'lead_contact_id'=>$request->lead_contact_id,'amount'=>$request->amount,'type'=>$request->type,'estimated_close_date'=>$request->estimated_close_date,'confidence'=>$request->confidence,'status'=>$request->status]);
            $new = true;
        }

        if($new){
            return response()->json(['status' => 'success', 'message' => 'Opportunity added successfully.', 'data' => $lead_opportunity], 200);
        }else{
            return response()->json(['status' => 'success', 'message' => 'Opportunity updated successfully.', 'data' => $lead_opportunity], 200);
        }
    }

    public function getAllOpportunities(Request $request)
    {
        $opportunity_statuses = OpportunitieStatus::select('id', 'status_name')->orderBy('ordering', 'asc')->get();
        $data = [];

        foreach ($opportunity_statuses as $key => $opportunity_status) {
            $data[$key]['status_id'] = $opportunity_status->id;
            $data[$key]['status_name'] = $opportunity_status->status_name;
            $data[$key]['total_opportunities'] = LeadOpportunity::where('status', $opportunity_status->id)->count();
            $data[$key]['total_amount'] = LeadOpportunity::where('status', $opportunity_status->id)->sum('amount');
            $all_opportunities = LeadOpportunity::with('lead:id,company_name', 'assignUser:id,name');
            if(!$request->user()->hasRole('superadmin')) {
                $user_ids = getUsersReportingToAuth($request->user()->id);
                $lead_ids = Lead::where('assign_to', $user_ids)->pluck('id');
                $all_opportunities->where('assigned_to', $user_ids);
            }
            $all_opportunities = $all_opportunities->where('status', $opportunity_status->id)->get();
            $data[$key]['opportunities'] = $all_opportunities;
        }
        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
    }
}
