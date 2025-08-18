<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaxInvoiceController extends Controller
{
    public function index(Request $request)
    {
        if($request->dev){
            if($request->ajax()){
                $invoices = Invoice::with('customer')->latest();
                return DataTables::of($invoices)
                        ->addIndexColumn()
                        ->editColumn('status', function($data)
                        {
                            return '<span class="badge badge-paid">Paid</span>';
                        })
                        ->rawColumns(['status'])
                        ->make(true);
            }
            return view('taxinvoice.index');
        }else{
            return view('work_in_progress');
        }
    }

    public function create(Request $request)
    {
        if($request->dev){
            return view('taxinvoice.create');
        }else{
            return view('work_in_progress');
        }
    }
}