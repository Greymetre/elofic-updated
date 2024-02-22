<?php

namespace App\Http\Controllers;

use App\Exports\SerialNumberTransactionExport;
use App\Imports\SerialNumberTransactionImport;
use App\Models\Services;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use DataTables;
use Excel;
use DB;
use Carbon\Carbon;

class ServicesController extends Controller
{
    public function serial_number_transaction(Request $request)
    {
        $branches = Branch::latest()->get();
        $products = Product::latest()->get();
        abort_if(Gate::denies('serial_number_transaction'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('services.serial_number_transaction', compact('branches','products'));
    }

    public function serial_number_transaction_upload(Request $request)
    {
        abort_if(Gate::denies('serial_number_transaction_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new SerialNumberTransactionImport, request()->file('import_file'));
        return back()->with('success', 'Serial Number Transaction Import successfully !!');
    }

    public function serial_number_transaction_download(Request $request)
    {
        abort_if(Gate::denies('serial_number_transaction_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SerialNumberTransactionExport($request), 'SerialNumberTransaction.xlsx');
    }

    public function serial_number_transaction_list(Request $request)
    {
        $data = Services::select(
            'product_code',
            'invoice_no',
            'invoice_date',
            'branch_code',
            'party_name',
            'product_name',
            'qty',
            'group'
        );
        if($request->branch_id && $request->branch_id != null && $request->branch_id != ''){
            $branche_code = Branch::where('id', $request->branch_id)->value('branch_code');
            $data = $data->where('branch_code', $branche_code);
        }
        if($request->product_id && $request->product_id != null && $request->product_id != ''){
            $product_code = Product::where('id', $request->product_id)->value('product_code');
            $data = $data->where('product_code', $product_code);
        }
        if($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != ''){
            $data = $data->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
        }
        $data = $data->groupBy('product_code', 'invoice_no','invoice_date' ,'branch_code','party_name','product_name','group','qty')->with('createdbyname');
        return Datatables::of($data)
            ->addIndexColumn()

            ->rawColumns(['action', 'image', 'checkbox'])
            ->make(true);
    }

    public function serial_number_history(Request $request)
    {
        // return true;
        abort_if(Gate::denies('serial_number_history'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('services.serial_number_history');
    }

    public function serial_number_history_list(Request $request)
    {
        return false;
        $data = Services::orderBy('invoice_date', 'asc')->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('expiry_date', function ($data) {
                $product = Product::where('product_code', $data->product_code)->first();
                if($product->expiry_interval && $product->expiry_interval != null && $product->expiry_interval != '' && $product->expiry_interval_preiod && $product->expiry_interval_preiod > 0 && $product->expiry_interval_preiod != null){
                    $initialDate = Carbon::parse($data->invoice_date);

                    $expiryDate = $initialDate->add($product->expiry_interval_preiod, strtolower($product->expiry_interval));

                    return $expiryDate->toDateString();
                }else{
                    return 'No Expiry Date';
                }
            })

            ->rawColumns(['expiry_date',])
            ->make(true);
    }
}
