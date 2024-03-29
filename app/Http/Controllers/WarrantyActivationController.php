<?php

namespace App\Http\Controllers;

use App\DataTables\WarrantyActivationDataTable;
use App\Models\Branch;
use App\Models\Customers;
use App\Models\EndUser;
use App\Models\Pincode;
use App\Models\SchemeHeader;
use App\Models\TransactionHistory;
use App\Models\WarrantyActivation;
use Illuminate\Http\Request;
use Gate;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class WarrantyActivationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->warranty_activation = new WarrantyActivation();
        $this->path = 'warranty_activation';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(WarrantyActivationDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('warranty_activation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $parent_customers = [];
        $scheme_names = SchemeHeader::where('active', 'Y')->select('id', 'scheme_name')->get();
        return $dataTable->render('warranty_activation.index', compact('branches', 'parent_customers', 'scheme_names'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('warranty_activation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $customers = Customers::where('customertype', '2')->select('id', 'name', 'mobile')->get();
        $customers_dealer = Customers::where('customertype', ['1', '3'])->select('id', 'name', 'mobile')->get();
        $pincodes = Pincode::all();
        return view('warranty_activation.create', compact('customers', 'pincodes', 'branches'))->with('warranty_activation', $this->warranty_activation);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            abort_if(Gate::denies('warranty_activation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (!$request->end_user_id || $request->end_user_id == NULL || $request->end_user_id == '') {
                $end_user = EndUser::updateOrCreate(['customer_number' => $request->customer_number ?? ''], [
                    'customer_name' => $request->customer_name ?? '',
                    'customer_number' => $request->customer_number ?? '',
                    'customer_email' => $request->customer_email ?? '',
                    'customer_address' => $request->customer_address ?? '',
                    'customer_place' => $request->customer_place ?? '',
                    'customer_pindcode' => $request->customer_pindcode ?? '',
                    'customer_country' => $request->customer_country ?? '',
                    'customer_state' => $request->customer_state ?? '',
                    'customer_district' => $request->customer_district ?? '',
                    'customer_city' => $request->customer_city ?? ''
                ]);
                $request->end_user_id = $end_user->id;
            }
            WarrantyActivation::create([
                'product_serail_number' => $request->product_serail_number ?? NULL,
                'product_id' => $request->product_id ?? NULL,
                'end_user_id' => $request->end_user_id ?? NULL,
                'branch_id' => $request->branch_id ?? NULL,
                'customer_id' => $request->customer_id ?? NULL,
                'status' => $request->status ?? 1,
                'sale_bill_no' => $request->sale_bill_no ?? NULL,
                'sale_bill_date' => $request->sale_bill_date ?? NULL,
                'warranty_date' => $request->warranty_date ?? NULL,
                'created_by' => auth()->user()->id
            ]);
            TransactionHistory::where('coupon_code', $request->product_serail_number)->update(['status' => '1']);

            return Redirect::to('warranty_activation')->with('message_success', 'Warranty Activation Store Successfully.');
        } catch (\Exception $e) {
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
        if ($transactionHistory->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Transaction History deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Transaction History Delete!']);
    }

    public function download(Request $request)
    {
        abort_if(Gate::denies('transaction_history_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TransactionHistoryExport($request), 'TransactionHistory.xlsx');
    }
}
