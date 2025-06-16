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

class LeadContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        return view('lead-contacts.index');
    }

    public function getLeadContacts(Request $request){
        $lead_contacts = LeadContact::with(['lead']); 
        $lead_contacts = $lead_contacts->select(\DB::raw(with(new LeadContact)->getTable().'.*'))->groupBy('id');
        return DataTables::of($lead_contacts)
            ->editColumn('lead.company_name', function ($lead_contact) {
                    return $lead_contact->lead->company_name??'';
            })
            ->editColumn('name', function ($lead_contact) {
                $url = route('leads.show',$lead_contact->lead_id);
                return '<a href="'.$url.'">'.$lead_contact->name.'</a>';
            })
           
            ->addColumn('action', function ($lead_contact) {
                return "action";
            })
            ->rawColumns(['action','name'])
            ->make(true);
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
            'lead_id'=>'required',
            'name'=>'required',
            'title'=>'required',
            'phone_number'=>'required',
            'contact_email'=>'required',
            'url'=>'required',
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id(); 
        $LeadContact = LeadContact::create(['name'=>$request->name,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'title'=>$request->title,'phone_number'=>$request->phone_number,'email'=>$request->contact_email,'url'=>$request->url]);
        $request->session()->flash('message_success',__('Lead contact Added successfully.'));

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
        $lead_contacts = LeadContact::where(['lead_id'=>$lead->id])->get();
        return view('leads.edit',compact('lead','lead_contacts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,Lead $lead)
    {
        //return view('leads.edit',compact('lead'));
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
        //
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
