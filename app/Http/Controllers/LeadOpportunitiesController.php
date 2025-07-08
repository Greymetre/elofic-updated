<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

use DataTables;
use Auth;

use App\Models\Lead;
use App\Models\User;
use App\Models\LeadOpportunity;
use App\Models\LeadContact;

class LeadOpportunitiesController extends Controller
{
    

  
    public function index(Request $request)
    {
       // abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('name')->get();

        $lead_contacts = LeadContact::get();
        return view('leads-opportunities.index',compact('users','lead_contacts'));
    }


    public function getCardData(Request $request){

        $assigned_to = $request->assigned_to;
        $no_show_opportunities = LeadOpportunity::where(['status'=>'no_show']);
        if($assigned_to){
            $no_show_opportunities->where('assigned_to',$assigned_to);
        }
        $no_show_opportunities = $no_show_opportunities->get();

        $demo_book_opportunities = LeadOpportunity::where(['status'=>'demo_book']);
        if($assigned_to){
            $demo_book_opportunities->where('assigned_to',$assigned_to);
        }
        $demo_book_opportunities = $demo_book_opportunities->get();

        $demo_completed_opportunities = LeadOpportunity::where(['status'=>'demo_completed']);
        if($assigned_to){
            $demo_completed_opportunities->where('assigned_to',$assigned_to);
        }
        $demo_completed_opportunities = $demo_completed_opportunities->get();

        $negotiating_opportunities = LeadOpportunity::where(['status'=>'negotiating']);
        if($assigned_to){
            $negotiating_opportunities->where('assigned_to',$assigned_to);
        }
        $negotiating_opportunities = $negotiating_opportunities->get();

        $interested_opportunities = LeadOpportunity::where(['status'=>'interested']);
        if($assigned_to){
            $interested_opportunities->where('assigned_to',$assigned_to);
        }
        $interested_opportunities = $interested_opportunities->get();

        $not_interested_opportunities = LeadOpportunity::where(['status'=>'not_interested']);
        if($assigned_to){
            $not_interested_opportunities->where('assigned_to',$assigned_to);
        }
        $not_interested_opportunities = $not_interested_opportunities->get();


        $view = view('leads-opportunities.inc_card_data', compact(
            'no_show_opportunities',
            'demo_book_opportunities',
            'demo_completed_opportunities',
            'negotiating_opportunities',
            'interested_opportunities',
            'not_interested_opportunities'
        ))->render();

        $total_annualised_value = ($no_show_opportunities->sum('amount')+$demo_book_opportunities->sum('amount')+$demo_completed_opportunities->sum('amount')+$negotiating_opportunities->sum('amount')+$interested_opportunities->sum('amount')+$not_interested_opportunities->sum('amount'));
        return response()->json([
            'status' => true,
            'view' => $view,
            'total_annualised_value' => $total_annualised_value,
        ]);
        
    }

    public function updateCardStatus(Request $request){

       $card_id = $request->card_id;
       $new_status = $request->new_status;
       $lead_opportunity = LeadOpportunity::where(['id'=>$card_id])->first();
       if($lead_opportunity){
            $lead_opportunity->update(['status'=>$new_status]);
            return response()->json(['status'=>true,'message'=>'']);
       }else{
         return response()->json(['status'=>false,'message'=>'data not found.']);
       }

    }

    public function getsingleData(Request $request){
        $id = $request->id;
        $lead_opportunity = LeadOpportunity::where(['id'=>$id])->first();
        return response()->json(['status'=>true,'message'=>'','data'=>$lead_opportunity]);
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
            'assigned_to'=>'required',
            'lead_contact_id'=>'required',
            'amount'=>'required',
            //'type'=>'required',
            'estimated_close_date'=>'required',
            'confidence'=>'required',
            'note'=>'required',
            'status'=>'required',
            
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id(); 
        $opportunity_id = $request->opportunity_id;
        $lead_opportunity = LeadOpportunity::where(['id'=>$opportunity_id])->first();
        if($lead_opportunity){
            $lead_opportunity->update(['note'=>$request->note,'created_by'=>$created_by,'assigned_to'=>$request->assigned_to,'lead_contact_id'=>$request->lead_contact_id,'estimated_close_date'=>$request->estimated_close_date,'confidence'=>$request->confidence,'status'=>$request->status, 'amount'=>$request->amount]);
            $request->session()->flash('message_success',__('Lead Opportunity update successfully.'));

        }else{

            $lead_opportunity = LeadOpportunity::create(['note'=>$request->note,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'assigned_to'=>$request->assigned_to,'lead_contact_id'=>$request->lead_contact_id,'amount'=>$request->amount,'type'=>$request->type,'estimated_close_date'=>$request->estimated_close_date,'confidence'=>$request->confidence,'status'=>$request->status]);
            $request->session()->flash('message_success',__('Lead Opportunity successfully.'));
        }
       

        return redirect()->back();
    }

    


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, LeadOpportunity $leadOpportunity)
    // {
    //     $rules = [
    //         'lead_id'=>'required',
    //         'assigned_to'=>'required',
    //         'lead_contact_id'=>'required',
    //         'amount'=>'required',
    //         'type'=>'required',
    //         'estimated_close_date'=>'required',
    //         'confidence'=>'required',
    //         'note'=>'required',
            
    //     ];

    //     $request->validate($rules);
    //     $data = $request->all();
    //     $created_by = Auth::id(); 
    //     $lead_opportunity = LeadOpportunity::where(['id'=>$leadOpportunity->id])->first();
    //     if($lead_opportunity){
    //          $lead_opportunity->update(['note'=>$request->note,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'assigned_to'=>$request->assigned_to,'lead_contact_id'=>$request->lead_contact_id,'amount'=>$request->amount,'type'=>$request->type,'estimated_close_date'=>$request->estimated_close_date,'confidence'=>$request->confidence]);
    //          $request->session()->flash('message_success',__('Lead Opportunity Added successfully.'));
    //     }else{
    //          $request->session()->flash('message_info',__('something went wrong.'));

    //     }
       
    //     return redirect()->back();
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, LeadOpportunity $leadOpportunity)
    {
        $leadOpportunity->delete();
        $request->session()->flash('message_success',__('Lead Opportunity deleted successfully.'));
        return redirect()->back();
    }
}
