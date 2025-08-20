<?php

namespace App\Http\Controllers;

use App\Models\CurrentTaxInvoiceNo;
use App\Models\Customers;
use App\Models\Invoice;
use App\Models\PaymentTerm;
use App\Models\Product;
use App\Models\State;
use App\Models\TaxInvoiceTax;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaxInvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->dev) {
            if ($request->ajax()) {
                $invoices = Invoice::with('customer')->latest();
                return DataTables::of($invoices)
                    ->addIndexColumn()
                    ->editColumn('status', function ($data) {
                        return '<span class="badge badge-paid">Paid</span>';
                    })
                    ->rawColumns(['status'])
                    ->make(true);
            }
            return view('taxinvoice.index');
        } else {
            return view('work_in_progress');
        }
    }
    public function create(Request $request)
    {
        if ($request->dev) {
            $payment_terms = PaymentTerm::all();
            $products = Product::where('active', 'Y')->get();
            $customers = Customers::where('active', 'Y')->select('id', 'name')->get();
            $states = State::where('active', 'Y')->select('id', 'state_name')->get();
            $users = User::where('active', 'Y')->select('id', 'name')->get();

            $lastInvoice = Invoice::orderBy('id', 'desc')->first();
            if ($lastInvoice) {
                $parts = explode('/', $lastInvoice->invoice_no);
                $prefix = $parts[0];
                $lastNumber = intval(end($parts));
                $nextNumber = $lastNumber + 1;
                $formattedNumber = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
            } else {
                $currentYear = date('y'); // e.g. 25
                if (date('m') >= 4) {
                    $startYear = $currentYear;
                    $endYear   = $currentYear + 1;
                } else {
                    $startYear = $currentYear - 1;
                    $endYear   = $currentYear;
                }
                $prefix = 'INV-' . str_pad($startYear, 2, '0', STR_PAD_LEFT) . '-' . str_pad($endYear, 2, '0', STR_PAD_LEFT);
                $formattedNumber = '01';
            }
            $invoiceNumber = $prefix . '/' . $formattedNumber;
            if (strpos($invoiceNumber, '/') !== false) {
                [$prefixValue, $nextNumberValue] = explode('/', $invoiceNumber);
            } else {
                $prefixValue = $invoiceNumber;
                $nextNumberValue = '';
            }

            $all_tax = TaxInvoiceTax::all();

            return view('taxinvoice.create', compact(
                'payment_terms',
                'products',
                'customers',
                'states',
                'invoiceNumber',
                'prefixValue',
                'nextNumberValue',
                'users',
                'all_tax'
            ));
        } else {
            return view('work_in_progress');
        }
    }
    public function add_payment_term(Request $request)
    {
        $payment_term = new PaymentTerm();
        $payment_term->term_name = $request->term_name;
        $payment_term->number_of_days = $request->number_of_days;
        $payment_term->save();
        return response()->json(['status' => true, 'message' => 'Payment Term Added Successfully!', 'data' => $payment_term]);
    }
    public function add_tax(Request $request)
    {
        $tax = new TaxInvoiceTax();
        $tax->tax_name = $request->tax_name;
        $tax->tax_percentage = $request->tax_percentage;
        $tax->save();
        return response()->json(['status' => true, 'message' => 'Tax Added Successfully!', 'data' => $tax]);
    }
    public function store(Request $request)
    {
        dd($request->all());
    }
}
