<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

use App\Exports\ExcelExport;
use App\Exports\LeadsTemplate;
use App\Imports\LeadsImport;
use Excel;

use DataTables;
use Auth;

use App\Models\Lead;
use App\Models\LeadContact;
use App\Models\LeadNote;
use App\Models\LeadTask;
use App\Models\User;
use App\Models\LeadOpportunity;
use App\Models\Pincode;
use App\Models\Country;
use App\Models\Address;
use App\Models\OpportunitieStatus;
use App\Models\Status;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::where('active', '=', 'Y')->whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->select('id', 'name')->orderBy('id')->get();
        $status = Status::where('module', 'LeadStatus')->where('active', 'Y')->get();
        return view('leads.index', compact('users', 'status'));
    }

    public function getLeads(Request $request)
    {
        $leads = Lead::with(['contacts', 'assign_user', 'status_is']);

        $datetime = $request->input('datetime');
        if ($datetime != "") {
            $datetimes = array_map('trim', explode('-', $datetime));
            $start_time = $datetimes[0] ?? '';
            $end_time = $datetimes[1] ?? '';

            if (isset($start_time) && $start_time != '') {
                $start_time = str_replace('/', '-', $start_time);
                $start_time = \Carbon\Carbon::parse($start_time)->format('Y-m-d');
            }

            if (isset($end_time) && $end_time != '') {
                $end_time = str_replace('/', '-', $end_time);
                $end_time = \Carbon\Carbon::parse($end_time)->format('Y-m-d');
            }

            if ($start_time != "" && $end_time != "") {
                $leads->whereBetween(\DB::raw('DATE(created_at)'), [$start_time, $end_time]);
            } else if ($start_time != "") {
                $leads->where(\DB::raw('DATE(created_at)'), '>=', $start_time);
            } else if ($end_time != "") {
                $leads->where(\DB::raw('DATE(created_at)'), '<=', $end_time);
            }
        }

        if ($request->input('search') != "") {
            $search = $request->input('search');
            $leads->where(function ($query) use ($search) {
                $query->where('company_name', 'like', "%{$search}%")
                    ->orWhereHas('contacts', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->input('status') != "") {
            $status = $request->input('status');
            $leads->where('status', $status);
        }
        // dd(auth()->user()->hasRole('superadmin'));
        if (!auth()->user()->hasRole('superadmin')) {
            $user_ids = getUsersReportingToAuth();
            $leads->where('assign_to', $user_ids);
        }

        $leads = $leads->orderBy('created_at', 'desc')->select(\DB::raw(with(new Lead)->getTable() . '.*'))->groupBy('id');
        return DataTables::of($leads)
            ->editColumn('company_name', function ($lead) {
                $url = route('leads.show', $lead);
                return '<a href="' . $url . '">' . ucwords(strtolower($lead->company_name)) . '</a>';
            })
            ->editColumn('assign_to', function ($lead) {
                return $lead->assign_user ? $lead->assign_user->name : '-';
            })
            ->editColumn('contacts', function ($lead) {
                if (count($lead->contacts) > 0) {
                    if (count($lead->contacts) > 1) {
                        $contacts_name = $lead->contacts[0]->name ?? '';
                        return $contacts_name . " +" . count($lead->contacts) - 1;
                    } else {
                        return $contacts_name = $lead->contacts[0]->name ?? '';
                    }
                    return "";
                }
            })
            ->editColumn('phone', function ($lead) {

                if (count($lead->contacts) > 0) {
                    return $contacts_name = $lead->contacts[0]->phone_number ?? '';
                }
                return "";
            })
            ->editColumn('email', function ($lead) {
                if (count($lead->contacts) > 0) {
                    return $contacts_name = $lead->contacts[0]->email ?? '';
                }
                return "";
            })
            ->editColumn('created_at', function ($lead) {
                return \Carbon\Carbon::parse($lead->created_at)->format('M j, Y \a\t g:i a');
            })

            ->addColumn('checkbox', function ($lead) {
                $lead_id = "'" . $lead->id . "'";

                return '<input type="checkbox" class="lead-checkbox checkbox_cls" value="' . $lead->id . '" name="lead_ids[]">';
            })

            ->editColumn('status', function ($lead) {
                if ($lead->status == '0') {
                    return "<span class='badge badge-warning'>Pending</span>";
                } else {
                    if ($lead->status_is) {
                        return "<span class='badge badge-success'>" . $lead->status_is->status_name . "</span>";
                    } else {
                        return "-";
                    }
                }
            })
            ->editColumn('others', function ($lead) {
                $jsonData = '';
                if(!empty($lead->others) && count($lead->others) > 0){
                    foreach ($lead->others as $key => $value) {
                        $jsonData .= "<strong>" . ucwords(str_replace('_', ' ', $key)) . ":</strong> " . $value . "<br>";
                    }
                }
                return $jsonData;
            })
            ->rawColumns(['action', 'company_name', 'checkbox', 'status', 'others'])
            ->make(true);
    }




    function exportLeads(Request $request)
    {
        $filename = 'leads.xlsx';

        $results_per_page = 8000;
        $page_number = intval($request->input('page_number'));
        $page_result = ($page_number - 1) * $results_per_page;

        $leads = Lead::with(['contacts', 'opportunities']);
        if (!auth()->user()->hasRole('superadmin')) {
            $user_ids = getUsersReportingToAuth();
            $leads->where('assign_to', $user_ids);
        }

        $datetime = $request->input('datetime');
        if ($datetime != "") {
            $datetimes = array_map('trim', explode('-', $datetime));
            $start_time = $datetimes[0] ?? '';
            $end_time = $datetimes[1] ?? '';

            if (isset($start_time) && $start_time != '') {
                $start_time = str_replace('/', '-', $start_time);
                $start_time = \Carbon\Carbon::parse($start_time)->format('Y-m-d');
            }

            if (isset($end_time) && $end_time != '') {
                $end_time = str_replace('/', '-', $end_time);
                $end_time = \Carbon\Carbon::parse($end_time)->format('Y-m-d');
            }

            if ($start_time != "" && $end_time != "") {
                $leads->whereBetween(\DB::raw('DATE(created_at)'), [$start_time, $end_time]);
            } else if ($start_time != "") {
                $leads->where(\DB::raw('DATE(created_at)'), '>=', $start_time);
            } else if ($end_time != "") {
                $leads->where(\DB::raw('DATE(created_at)'), '<=', $end_time);
            }
        }

        $leads = $leads->orderBy('created_at', 'desc')->groupBy('id')->get();

        $allOtherKeys = [];

        $data = $leads->map(function ($item) use (&$allOtherKeys) {
            $contact = $item->contacts->first();

            $contacts_name = $contact?->name ?? '';
            $contacts_number = $contact?->phone_number ?? '';
            $contacts_email = $contact?->email ?? '';
            $contacts_lead_source = $contact?->lead_source ?? '';

            $address = $item->address;

            // ✅ Safely decode 'others' field
            $othersData = is_array($item->others)
                ? $item->others
                : (is_string($item->others) ? json_decode($item->others, true) : []);

            $othersData = is_array($othersData) ? $othersData : [];

            // ✅ Collect all unique keys
            $allOtherKeys = array_unique(array_merge($allOtherKeys, array_keys($othersData)));

            // ✅ Base export row
            $baseRow = [
                $item->id,
                $item->lead_generation_date,
                $item->company_name,
                $contacts_name,
                $contacts_number,
                $contacts_email,
                $contacts_lead_source,
                $address->pincodename?->pincode ?? '',
                $address->cityname?->city_name ?? '',
                $address->districtname?->district_name ?? '',
                $item->status_is?->status_name ?? 'Pending',
                $address->address1 ?? '',
                $item->assign_user?->name ?? '',
                '', // Lead Status (custom field?)
                $item->close_duration ?? '',
                $item->createdby->name ?? '',
                '',
                $item->opportunities->sum('amount') ?? '0',
            ];

            // ✅ Add 'others' values in consistent order
            $orderedOthers = collect($allOtherKeys)->mapWithKeys(function ($key) use ($othersData) {
                return [$key => $othersData[$key] ?? ''];
            })->toArray();

            return array_merge($baseRow, $orderedOthers);
        })->toArray();

        // ✅ Header
        $baseHeaders = [
            'ID',
            'Lead Generation Date',
            'Firm Name',
            'Customer Name',
            'Customer Number',
            'Email',
            'Lead Source',
            'Pincode',
            'City',
            'District',
            'Lead Type',
            'Address',
            'Assignee',
            'Lead Status',
            'Close Duration',
            'Created By',
            'Lead Time',
            'Sales Value',
        ];

        $finalHeaders = array_merge($baseHeaders, $allOtherKeys);

        // ✅ Export
        $export = new ExcelExport($finalHeaders, $data);
        return Excel::download($export, $filename);
    }


    public function uploadleadFiles(Request $request)
    {

        $rules = [
            'lead_id' => 'required',
            'lead_file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,pdf,xls,xlsx',
                'max:' . (config('media-library.max_file_size') / 1024), // max in KB
            ],
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id();
        $lead_id = $request->lead_id;
        $lead = Lead::where(['id' => $lead_id])->first();
        if ($lead) {

            if ($request->hasFile('lead_file')) {
                $file = $request->file('lead_file');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $lead->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('lead_file');
            }

            $request->session()->flash('message_success', __('Lead file upload successfully.'));
            return redirect()->route('leads.show', $lead);
        } else {
            //$request->session()->flash('message_success',__('Lead file upload successfully.'));
            return redirect()->route('leads.show', $lead);
        }
    }

    public function deleteMedia(Request $request)
    {

        $rules = [
            //'lead_id'=>'required',
            'media_id' => 'required',
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id();
        $lead_id = $request->lead_id;
        $media_id = $request->media_id;
        $media = Media::find($media_id);

        if ($media && $media->model_type === Lead::class) {
            $media->delete();
            $request->session()->flash('message_success', __('Lead file upload successfully.'));
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }




    public function searchExistsLead(Request $request)
    {
        $company_name = $request->company_name ?? '-';
        $contact_name = $request->contact_name ?? '-';
        $user_id = Auth::id() ?? '';
        $leads = Lead::where(['created_by' => $user_id])->where(function ($query) use ($company_name, $contact_name) {
            $query->where('company_name', 'like', "%{$company_name}%")
                ->orWhereHas('contacts', function ($subQuery) use ($contact_name) {
                    $subQuery->where('name', 'like', "%{$contact_name}%");
                });
        })->get();

        if ($leads->count() > 0) {
            $text = '<p class="text-muted">We\'ve found similar Leads that already exist:</p>
                <div class="table-responsive">
                  <table class="table align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Created</th>
                      </tr>
                    </thead>
                    <tbody>';

            foreach ($leads as $lead) {
                $firstContact = $lead->contacts->first();
                $contactName = $firstContact ? $firstContact->name : '—';
                $contactCount = $lead->contacts->count();
                if ($contactCount < 2) {
                    $contactname_html  = htmlspecialchars($contactName);
                } else {
                    $contactname_html = htmlspecialchars($contactName) . ' +' . ($contactCount - 1);
                }

                $text .= '<tr>
                    <td>
                      <a href="' . route('leads.show', $lead->id) . '" class="text-primary text-decoration-underline">'
                    . htmlspecialchars($lead->company_name) . '</a><br>
                      <small class="text-muted">' . $contactname_html . '</small>
                    </td>
                    <td>' . htmlspecialchars($lead->status ?? '—') . '</td>
                    <td>' . $lead->created_at->diffForHumans() . '</td>
                  </tr>';
            }

            $text .= '</tbody></table></div>';

            return $text;
        }

        return '';
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'company_name' => 'required',
            'contact_name' => 'required',
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id();
        $lead = Lead::create(['company_name' => $request->company_name, 'created_by' => $created_by]);
        $category = LeadContact::create(['name' => $request->contact_name, 'lead_id' => $lead->id, 'created_by' => $created_by]);
        $request->session()->flash('message_success', __('Lead Added successfully.'));
        return redirect()->route('leads.show', $lead);
    }

    public function storeAddress(Request $request)
    {
        $rules = [
            'lead_id' => 'required',
            'address1' => 'required',
            'address2' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            //'district_id'=>'required',
            'city_id' => 'required',
            'pincode_id' => 'required',

        ];

        $address_id = $request->address_id;
        $address = Address::where(['id' => $address_id])->first();
        if ($address) {
            $address->update(['model_type' => 'App\Models\Lead', 'model_id' => $request->lead_id, 'address1' => $request->address1, 'address2' => $request->address2, 'country_id' => $request->country_id, 'state_id' => $request->state_id, 'district_id' => $request->district_id, 'city_id' => $request->city_id, 'pincode_id' => $request->pincode_id]);
            $request->session()->flash('message_success', __('Lead Address Update successfully.'));
        } else {
            Address::create(['model_type' => 'App\Models\Lead', 'model_id' => $request->lead_id, 'address1' => $request->address1, 'address2' => $request->address2, 'country_id' => $request->country_id, 'state_id' => $request->state_id, 'district_id' => $request->district_id, 'city_id' => $request->city_id, 'pincode_id' => $request->pincode_id]);
            $request->session()->flash('message_success', __('Lead Address Added successfully.'));
        }

        return redirect()->back();
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Lead $lead)
    {
        $userids = getUsersReportingToAuth();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('name')->get();

        $lead_contacts = LeadContact::where(['lead_id' => $lead->id])->get();
        $lead_notes = LeadNote::where(['lead_id' => $lead->id])->get();
        $lead_tasks = LeadTask::where(['lead_id' => $lead->id])->get();
        $lead_opportunities = LeadOpportunity::where(['lead_id' => $lead->id])->get();

        $pincodes = Pincode::where('active', '=', 'Y')
            ->whereHas('assigncitiesusers', function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('userid', $userids);
                }
            })
            ->select('id', 'pincode')->orderBy('id', 'desc')->get();
        $countries = Country::where('active', '=', 'Y')
            ->whereHas('countrystates', function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereHas('statecities', function ($query) use ($userids) {
                        $query->whereHas('assignusers', function ($q) use ($userids) {
                            $q->whereIn('userid', $userids);
                        });
                    });
                }
            })
            ->select('id', 'country_name')->orderBy('id', 'desc')->get();
        $address = Address::where(['model_type' => 'App\Models\Lead', 'model_id' => $lead->id])->first();
        if (isset($address)) {
            $address1 = $address->address1;
            $address2 = $address->address2;
            $city_name = $address->cityname->city_name ?? '';
            $state_name = $address->statename->state_name ?? '';
            $pincodename = $address->pincodename->pincode ?? '';
            $address_data = $address1 . "," . $address2 . "," . $city_name . "," . $state_name . "," . $pincodename;
        } else {
            $address_data = "";
        }

        $lead_notes->each(function ($item) {
            $item->type = 'note';
        });
        $lead_tasks->each(function ($item) {
            $item->type = 'task';
        });

        $combined = $lead_notes->merge($lead_tasks)->sortByDesc('created_at')->values();

        $media_items = $lead->getMedia('lead_file');
        $status = Status::where('module', 'LeadStatus')->where('active', 'Y')->get();
        $opportunity_status = OpportunitieStatus::orderBy('ordering', 'asc')->pluck('status_name', 'id')->toArray();
        return view('leads.show', compact('lead', 'lead_contacts', 'lead_notes', 'users', 'lead_tasks', 'lead_opportunities', 'countries', 'pincodes', 'address', 'address_data', 'media_items', 'combined', 'status', 'opportunity_status'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Lead $lead) {}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead)
    {
        $rules = [
            'company_name' => 'required',
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id();
        $lead->update(['company_name' => $request->company_name]);
        $request->session()->flash('message_success', __('Lead Updated successfully.'));
        return redirect()->route('leads.show', $lead);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        //
    }

    public function assignLead(Request $request)
    {
        $update = Lead::whereIn('id', $request->lead_id)->update(['assign_to' => $request->user_id]);
        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'Lead assigned successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    //Multiple delete
    public function deleteLead(Request $request)
    {
        $lead = Lead::whereIn('id', $request->lead_id)->delete();
        if ($lead) {
            return response()->json(['status' => 'success', 'message' => 'Lead deleted successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function changeStatus(Request $request)
    {
        $update = Lead::where('id', $request->lead_id)->update(['status' => $request->status]);
        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'Status updated successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('lead_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new LeadsImport, request()->file('import_file'));
        return back();
    }

    public function template()
    {
        abort_if(Gate::denies('lead_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new LeadsTemplate, 'LeadTemplate.xlsx');
    }
}
