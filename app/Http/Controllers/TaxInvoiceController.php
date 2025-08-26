<?php

namespace App\Http\Controllers;

use App\Models\CurrentTaxInvoiceNo;
use App\Models\Customers;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\PaymentTerm;
use App\Models\Product;
use App\Models\State;
use App\Models\TaxInvoiceTax;
use App\Models\TaxInvoiceTds;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Yajra\DataTables\Facades\DataTables;

class TaxInvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Invoice::with('customer');

            if($request->start_date && $request->end_date && !empty($request->start_date) && !empty($request->end_date)){
                $invoices = $invoices->whereBetween('invoice_date', [$request->start_date, $request->end_date]);                
            }
            if($request->searchInput && !empty($request->searchInput)){
                //Useing $query orwher using bracket 
                $invoices = $invoices->where(function ($query) use ($request) {
                    $query->where('invoice_no', 'like', '%' . $request->searchInput . '%')
                        ->orWhere('order_no', 'like', '%' . $request->searchInput . '%')
                        ->orWhereHas('customer', function ($subQuery) use ($request) {
                            $subQuery->where('name', 'like', '%' . $request->searchInput . '%')
                                ->orWhere('mobile', 'like', '%' . $request->searchInput . '%');
                        });
                });
            }
            $invoices = $invoices->latest();
            return DataTables::of($invoices)
                ->addIndexColumn()
                // ->editColumn('status', function ($data) {
                //     return '<span class="badge badge-paid">Paid</span>';
                // })
                ->editColumn('status', function ($data) {
                    $today = \Carbon\Carbon::today();
                    $dueDate = \Carbon\Carbon::parse($data->due_date);

                    if ($dueDate->isToday()) {
                        return '<span class="badge badge-warning">Due Today</span>';
                    } elseif ($dueDate->isPast()) {
                        $days = $dueDate->diffInDays($today);
                        return '<span class="badge badge-danger">Overdue by ' . $days . ' days</span>';
                    } else {
                        $days = $today->diffInDays($dueDate);
                        return '<span class="badge badge-info">Due in ' . $days . ' days</span>';
                    }
                })
                ->editColumn('invoice_no', function ($data) {
                    return '<a href="' . route('tax_invoice.show', $data->id) . '">' . $data->invoice_no . '</a>';
                })
                ->editColumn('invoice_date', function ($data) {
                    return date('d M Y', strtotime($data->invoice_date));
                })
                ->editColumn('due_date', function ($data) {
                    return date('d M Y', strtotime($data->due_date));
                })
                ->rawColumns(['status', 'invoice_no', 'invoice_date', 'due_date'])
                ->make(true);
        }
        return view('taxinvoice.index');
    }
    public function create(Request $request)
    {
        $payment_terms = PaymentTerm::all();
        $products = Product::where('active', 'Y')->get();
        $customers = Customers::where('active', 'Y')->select('id', 'name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $users = User::where('active', 'Y')->select('id', 'name')->get();

        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        if ($lastInvoice) {
            $parts = explode('/', $lastInvoice->invoice_no);
            $prefix = $parts[0];
            $lastNumberPart = end($parts);
            $digitLength = strlen($lastNumberPart);
            $nextNumber = intval($lastNumberPart) + 1;
            $formattedNumber = str_pad($nextNumber, $digitLength, '0', STR_PAD_LEFT);
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
        $all_tds = TaxInvoiceTds::all();

        return view('taxinvoice.create', compact(
            'payment_terms',
            'products',
            'customers',
            'states',
            'invoiceNumber',
            'prefixValue',
            'nextNumberValue',
            'users',
            'all_tax',
            'all_tds'
        ));
    }
    public function convert_to_tax_invoice(Request $request, Estimate $convert_estimate)
    {
        return view('work_in_progress');
        $payment_terms = PaymentTerm::all();
        $products = Product::where('active', 'Y')->get();
        $customers = Customers::where('active', 'Y')->select('id', 'name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $users = User::where('active', 'Y')->select('id', 'name')->get();

        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        if ($lastInvoice) {
            $parts = explode('/', $lastInvoice->invoice_no);
            $prefix = $parts[0];
            $lastNumberPart = end($parts);
            $digitLength = strlen($lastNumberPart);
            $nextNumber = intval($lastNumberPart) + 1;
            $formattedNumber = str_pad($nextNumber, $digitLength, '0', STR_PAD_LEFT);
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
        $all_tds = TaxInvoiceTds::all();

        return view('taxinvoice.convert', compact(
            'payment_terms',
            'products',
            'customers',
            'states',
            'invoiceNumber',
            'prefixValue',
            'nextNumberValue',
            'users',
            'all_tax',
            'all_tds',
            'convert_estimate'
        ));
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
    public function add_tds(Request $request)
    {
        $tds = new TaxInvoiceTds();
        $tds->tax_name = $request->tax_name;
        $tds->rate = $request->rate;
        $tds->section = $request->section;
        $tds->save();
        return response()->json(['status' => true, 'message' => 'TDS Added Successfully!', 'data' => $tds]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'invoice_no' => 'required|unique:invoices',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        // dd($request->all());
        $invoice = new Invoice();
        $invoice->customer_id          =   $request->customer_id;
        $invoice->place_of_supply      =   $request->place_of_supply;
        $invoice->invoice_no           =   $request->invoice_no;
        $invoice->order_no             =   $request->order_no;
        $invoice->invoice_date         =   $request->invoice_date;
        $invoice->payment_term         =   $request->payment_term;
        $invoice->due_date             =   $request->due_date;
        $invoice->user_id              =   $request->user_id;
        $invoice->sub_total            =   $request->sub_total;
        $invoice->discount_type        =   $request->discount_type;
        $invoice->discount             =   $request->discount;
        $invoice->discount_amount      =   $request->discount_amount;
        $invoice->tds                  =   $request->tds ?? 0.00;
        $invoice->tds_amount           =   $request->tds_amount ?? 0.00;
        $invoice->adjustment           =   $request->adjustment ?? 0.00;
        $invoice->grand_total          =   $request->grand_total;
        $invoice->customer_notes       =   $request->customer_notes;
        $invoice->t_c                  =   $request->t_c;
        $invoice->save();

        if(isset($request->convert_estimate_id) && !empty($request->convert_estimate_id)){
            Estimate::where('id', $request->convert_estimate_id)->update(['invoice_id' => $invoice->id, 'status' => 1]);
        }

        if ($request->hasFile('files') && count($request->file('files')) > 0) {
            foreach ($request->file('files') as $file) {
                $invoice->addMedia($file)->toMediaCollection('invoice_files');
            }
        }

        foreach ($request->product_id as $k => $product) {
            if(empty($request->product_id[$k])) continue;
            $invoice->details()->create([
                'product_id' => $request->product_id[$k],
                'product_dec' => $request->product_dec[$k],
                'hsn_sac' => $request->hsn_sac[$k],
                'quantity' => $request->quantity[$k],
                'mrp' => $request->mrp[$k],
                'tax' => $request->tax[$k] ?? 0.00,
                'tax_amount' => $request->tax_amount[$k],
                'amount' => $request->amount[$k]
            ]);
        }
        // return redirect()->route('tax_invoice.index');
        return redirect()->route('tax_invoice.show', $invoice->id);
    }

    public function show(Invoice $tax_invoice, Request $request)
    {
        $request = new Request(['customer_id' => $tax_invoice->customer_id]);
        $customer_address_class = new AjaxController();
        $customer_address = $customer_address_class->getCustomerAddress($request);
        $address = $customer_address->getData(true)['data'];

        $taxSummary = $tax_invoice->details
            ->groupBy('tax') // group by tax id
            ->map(function ($items, $taxId) {
                $taxName = optional($items->first()->tax_details)->tax_name.' ('.optional($items->first()->tax_details)->tax_percentage.'%)'; // get tax name from relation
                $totalAmount = $items->sum('tax_amount'); // sum tax_amount for this tax

                return [
                    'tax_id' => $taxId,
                    'tax_name' => $taxName,
                    'total_tax_amount' => $totalAmount,
                ];
            })
            ->values();
        return view('taxinvoice.show', compact('tax_invoice', 'address', 'taxSummary'));
    }
}
