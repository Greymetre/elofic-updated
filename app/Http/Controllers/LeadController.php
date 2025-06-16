<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

use DataTables;
use Auth;

use App\Models\Lead;
use App\Models\LeadContact;
use App\Models\LeadNote;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       // abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        return view('leads.index');
    }

    public function getLeads(Request $request){
        $leads = Lead::with(['contacts']); 
        
        $datetime = $request->input('datetime');
        if($datetime!=""){
            $datetimes = array_map('trim', explode('-', $datetime));
            $start_time = $datetimes[0]??'';
            $end_time = $datetimes[1]??'';

            if (isset($start_time) && $start_time!=''){
                $start_time = str_replace('/', '-', $start_time);
                $start_time = \Carbon\Carbon::parse($start_time)->format('Y-m-d');
            }

            if (isset($end_time) && $end_time!=''){
                $end_time = str_replace('/', '-', $end_time);
                $end_time = \Carbon\Carbon::parse($end_time)->format('Y-m-d');
            }

            if($start_time!="" && $end_time!=""){
                $leads->whereBetween(\DB::raw('DATE(created_at)'), [$start_time, $end_time]);
            }else if($start_time!=""){
                $leads->where(\DB::raw('DATE(created_at)'), '>=', $start_time);
            }else if($end_time!=""){
                $leads->where(\DB::raw('DATE(created_at)'), '<=', $end_time);
            }
        }

        $leads = $leads->select(\DB::raw(with(new Lead)->getTable().'.*'))->groupBy('id');
        return DataTables::of($leads)
            ->editColumn('company_name', function ($lead) {
                $url = route('leads.show',$lead);
                return '<a href="'.$url.'">'.$lead->company_name.'</a>';
            })
            ->editColumn('status', function ($lead) {
                    return $lead->status;
            })
            ->editColumn('contacts', function ($lead) {
                if(count($lead->contacts)>0){
                    if(count($lead->contacts)>1){
                        $contacts_name = $lead->contacts[0]->name??'';
                        return $contacts_name." +".count($lead->contacts)-1;
                    }else{
                       return $contacts_name = $lead->contacts[0]->name??'';
                    }
                    return "";
                  
                }
            })
            ->editColumn('phone', function ($lead) {
                
                    if(count($lead->contacts)>0){
                     return $contacts_name = $lead->contacts[0]->phone_number??'';
                    }  
                    return "";
                  
                
            })
            ->editColumn('email', function ($lead) {
                    if(count($lead->contacts)>0){
                     return $contacts_name = $lead->contacts[0]->email??'';
                    }  
                    return "";
            })
            ->editColumn('created_at', function ($lead) {
                     return \Carbon\Carbon::parse($lead->created_at)->format('M j, Y \a\t g:i a');
            })

            ->addColumn('checkbox', function ($lead) {
                return '<input type="checkbox" class="lead-checkbox" value="'.$lead->id.'" name="lead_ids[]">';
            })

            ->addColumn('action', function ($lead) {
                return "action";
            })
            ->rawColumns(['action','company_name','checkbox'])
            ->make(true);
    }

    public function searchExistsLead(Request $request)
    {
        $company_name = $request->company_name??'-';
        $contact_name = $request->contact_name??'-';
        $user_id =Auth::id()??'';
        $leads = Lead::where(['created_by'=>$user_id])->where(function ($query) use ($company_name,$contact_name) {
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
                if($contactCount<2){
                    $contactname_html  = htmlspecialchars($contactName);
                }else{
                   $contactname_html = htmlspecialchars($contactName) . ' +' . ($contactCount - 1);
                }

                $text .= '<tr>
                    <td>
                      <a href="' . route('leads.show', $lead->id) . '" class="text-primary text-decoration-underline">'
                        . htmlspecialchars($lead->company_name) . '</a><br>
                      <small class="text-muted">'.$contactname_html.'</small>
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
    public function create(Request $request)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $rules = [
            'company_name'=>'required',
            'contact_name'=>'required',
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id(); 
        $lead = Lead::create(['company_name'=>$request->company_name,'created_by'=>$created_by]);
        $category = LeadContact::create(['name'=>$request->contact_name,'lead_id'=>$lead->id,'created_by'=>$created_by]);
        $request->session()->flash('message_success',__('Lead Added successfully.'));
        return redirect()->route('leads.show',$lead);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Lead $lead)
    {   
        $lead_contacts = LeadContact::where(['lead_id'=>$lead->id])->get();
        $lead_notes = LeadNote::where(['lead_id'=>$lead->id])->get();
        return view('leads.show',compact('lead','lead_contacts','lead_notes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,Lead $lead)
    {   

    }

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
            'company_name'=>'required',
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id(); 
        $lead->update(['company_name'=>$request->company_name]);
        $request->session()->flash('message_success',__('Lead Updated successfully.'));
        return redirect()->route('leads.show',$lead);
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
}
