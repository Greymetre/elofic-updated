<?php

namespace App\Http\Controllers;

use App\Models\OpportunitieStatus;
use Illuminate\Http\Request;
use DataTables;

class LeadOpportunitiesStatusController extends Controller
{
    public function index(Request $request)
    {
        if(request()->ajax()) {
            $data = OpportunitieStatus::with('createbyname')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($data){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';
                    $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';
                    return $btn;
                })
                ->editColumn('created_at', function($data){
                    //Conver Date Format in DD-MM-YYY H:i AM/PM
                    return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                })
                ->rawColumns(['action'])
                ->make(true);
            
        }
        return view('lead-opportunities-status.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by'] = auth()->user()->id;
        $lead_opportunities_status = OpportunitieStatus::create($data);
        return redirect()->back();
    }
}