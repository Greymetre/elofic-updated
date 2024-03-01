<?php

namespace App\Http\Controllers;

use Gate;
use Excel;
use App\Exports\TransactionHistoryExport;
use Validator;
use App\Models\Branch;
use App\Models\Services;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\DataTables\TransactionHistoryDataTable;
use App\Models\SchemeHeader;

class TransactionHistoryController extends Controller
{

    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->transaction_history = new TransactionHistory();
        $this->path = 'transaction_history';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionHistoryDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('transaction_history_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $parent_customers = Customers::where('active', 'Y')->whereIn('customertype', ['1','3'])->select('id', 'name')->get();
        $scheme_names = SchemeHeader::where('active', 'Y')->select('id', 'scheme_name')->get();
        return $dataTable->render('transaction_history.index', compact('branches', 'parent_customers', 'scheme_names'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('transaction_history_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customers = Customers::where('customertype', '2')->select('id', 'name', 'mobile')->get();
        return view('transaction_history.create', compact('customers'))->with('transaction_history',$this->transaction_history);;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        { 
            abort_if(Gate::denies('transaction_history_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'coupen_code.*' => 'required',
            ]);
            $validator->setAttributeNames([
                'coupen_code.*' => 'coupon code',
            ]);
            
            $validator->setCustomMessages([
                'coupen_code.*.required' => 'All coupon code fields are required.',
            ]); 
            if ($validator->fails()) {
                return redirect()->back()
                ->withErrors($validator)
                ->withInput();
            }
            $nonNullCoupenCodes = array_filter($request->coupen_code, function($value) {
                return !is_null($value);
            });
            foreach($nonNullCoupenCodes as $nonNullCoupenCode){
                $exists = TransactionHistory::where('coupen_code', $nonNullCoupenCode)->exists();
                $notexists = Services::where('serial_no', $nonNullCoupenCode)->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'coupen_code' => "The coupon code '$nonNullCoupenCode' already Scanned.",
                    ]);
                }
                if (!$notexists) {
                    throw ValidationException::withMessages([
                        'coupen_code' => "The coupon code '$nonNullCoupenCode' is Invalid.",
                    ]);
                }
                TransactionHistory::create([
                    'customer_id' => $request->customer_id,
                    'coupen_code' => $nonNullCoupenCode,
                    'created_by' => auth()->user()->id,
                ]);
            }
            return Redirect::to('transaction_history')->with('message_success', 'Transaction History Store Successfully');
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransactionHistory  $transactionHistory
     * @return \Illuminate\Http\Response
     */
    public function show(TransactionHistory $transactionHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransactionHistory  $transactionHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(TransactionHistory $transactionHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransactionHistory  $transactionHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransactionHistory $transactionHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransactionHistory  $transactionHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransactionHistory $transactionHistory)
    {
        if($transactionHistory->delete())
        {
            return response()->json(['status' => 'success','message' => 'Transaction History deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Transaction History Delete!']);
    }

    public function download(Request $request)
    {
        abort_if(Gate::denies('transaction_history_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TransactionHistoryExport($request), 'TransactionHistory.xlsx');
    }
}
