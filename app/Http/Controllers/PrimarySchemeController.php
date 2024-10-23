<?php

namespace App\Http\Controllers;

use App\Imports\PrimarySchemeImport;
use App\Models\Branch;
use App\Models\Customers;
use App\Models\CustomerType;
use App\Models\PrimarySales;
use App\Models\PrimaryScheme;
use App\Models\PrimarySchemeDetail;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Gate;
use Excel;
use DataTables;

class PrimarySchemeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->schemes = new PrimaryScheme();
        $this->path = 'primary_schemes';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $order_schemes = PrimaryScheme::with(['primaryscheme_details'])->orderBy('id', 'desc');
            $order_schemes = $order_schemes->select(\DB::raw(with(new PrimaryScheme)->getTable() . '.*'))->groupBy('id');
            return Datatables::of($order_schemes)
                ->addIndexColumn()
                ->editColumn('id', function ($query) {
                    return $query->id ?? '';
                })
                ->editColumn('scheme_name', function ($query) {
                    return $query->scheme_name ?? '';
                })
                ->editColumn('scheme_description', function ($query) {
                    return $query->scheme_description ?? '';
                })
                ->editColumn('start_date', function ($query) {
                    return $query->start_date ?? '';
                })
                ->editColumn('end_date', function ($query) {
                    return $query->end_date ?? '';
                })
                ->editColumn('scheme_type', function ($query) {
                    return $query->scheme_type ?? '';
                })
                ->editColumn('created_at', function ($query) {
                    return  date("Y-m-d", strtotime($query->created_at));
                })

                ->addColumn('action', function ($query) {
                    $btn = '';
                    $activebtn = '';

                    $btn = $btn . '<a href="' . route("orderschemes.edit", ["orderscheme" => $query->id]) . '" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' ' . trans('panel.orderschemes.title_singular') . '">
                               <i class="material-icons">edit</i>
                                </a>';

                    $btn = $btn . ' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.orderschemes.title_singular') . '">
                                            <i class="material-icons">clear</i>
                                          </a>';

                    $active = ($query->active == 'Y') ? 'checked="" value="' . $query->active . '"' : 'value="' . $query->active . '"';
                    $activebtn = '<div class="togglebutton">
                                        <label>
                                          <input type="checkbox"' . $active . ' id="' . $query->id . '" class="orderschemeActive">
                                          <span class="toggle"></span>
                                        </label>
                                    </div>';


                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('primary_schemes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $primary_customers = PrimarySales::groupBy('customer_id')->pluck('customer_id');
        $primary_branchs = PrimarySales::groupBy('branch_id')->pluck('branch_id');
        $customer_types = CustomerType::where('active', 'Y')->select('id', 'customertype_name')->get();
        $branchs = Branch::whereIn('id', $primary_branchs)->where('active', 'Y')->select('id', 'branch_name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $customers = Customers::whereIn('id', $primary_customers)->where('active', 'Y')->select('id', 'name')->get();

        return view('primary_schemes.form', compact('customer_types', 'branchs', 'states', 'customers'))->with('schemes', $this->schemes);
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
            $rule = [
                'scheme_name' => 'required',
            ];
            if($request['repetition'] == '1'){
                $rule['week'] = 'required';
            }elseif($request['repetition'] == '2'){
                $rule['week_repeat'] = 'required';
            }else{
                $rule['start_date'] = 'required';
                $rule['end_date'] = 'required';
            }
            $message = [
                'week.required' => 'Please select at least one day.'
            ];
            $validator = Validator::make($request->all(), $rule, $message);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            if ($id = PrimaryScheme::insertGetId([
                'active' => 'Y',
                'scheme_name' => isset($request['scheme_name']) ? $request['scheme_name'] : '',
                'scheme_description' => isset($request['scheme_description']) ? $request['scheme_description'] : '',
                'start_date' => isset($request['start_date']) ? $request['start_date'] : '',
                'end_date' => isset($request['end_date']) ? $request['end_date'] : '',
                'repetition' => isset($request['repetition']) ? $request['repetition'] : '',
                'day_repeat' => isset($request['week']) ? implode(',',$request['week']) : NULL,
                'week_repeat' => isset($request['week_repeat']) ? $request['week_repeat'] : NULL,
                'quarter' => isset($request['quarter']) ? $request['quarter'] : NULL,
                'scheme_type' => isset($request['scheme_type']) ? $request['scheme_type'] : '',
                'scheme_basedon' => isset($request['scheme_basedon']) ? $request['scheme_basedon'] : '',
                'assign_to' => isset($request['assign_to']) ? $request['assign_to'] : '',
                'branch' => (isset($request['branch']) && count($request['branch']) > 0) ? implode(',', $request['branch']) : '',
                'state' => (isset($request['state']) && count($request['state']) > 0) ? implode(',', $request['state']) : '',
                'customer' => (isset($request['customer']) && count($request['customer']) > 0) ? implode(',', $request['customer']) : '',
                'customer_type' => (isset($request['customer_type']) && count($request['customer_type']) > 0) ? implode(',', $request['customer_type']) : '',
                'minimum' => isset($request['minimum']) ? $request['minimum'] : null,
                'maximum' => isset($request['maximum']) ? $request['maximum'] : null,
                'created_at' => getcurentDateTime(),
            ])) {
                if ($request->import_file) {
                    if (ob_get_contents()) ob_end_clean();
                    ob_start();
                    Excel::import(new PrimarySchemeImport(encrypt($id)), $request['import_file']);
                } else {

                    $primarychemedetils = collect([]);
                    if ($request['points']) {
                        foreach ($request['points'] as $key => $value) {
                            $primarychemedetils->push([
                                'active' => 'Y',
                                'primary_scheme_id' => $id,
                                'product_id' => !empty($request['product_id']) ? $request['product_id'][$key] : null,
                                'category_id' => !empty($request['category_id']) ? $request['category_id'][$key] : null,
                                'subcategory_id' => !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null,
                                //'minimum' => isset($request['minimum']) ? $request['minimum'][$key] : null,
                                //'maximum' => isset($request['maximum']) ? $request['maximum'][$key] : null,
                                'points' => isset($request['points']) ? $request['points'][$key] : 0,
                            ]);
                        }
                        if ($primarychemedetils->isNotEmpty()) {
                            PrimarySchemeDetail::insert($primarychemedetils->toArray());
                        }
                    }
                }
                return Redirect::to('primary_scheme')->with('message_success', 'Primary Scheme Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Primary Scheme Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PrimaryScheme  $primaryScheme
     * @return \Illuminate\Http\Response
     */
    public function show(PrimaryScheme $primaryScheme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PrimaryScheme  $primaryScheme
     * @return \Illuminate\Http\Response
     */
    public function edit(PrimaryScheme $primaryScheme)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PrimaryScheme  $primaryScheme
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PrimaryScheme $primaryScheme)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PrimaryScheme  $primaryScheme
     * @return \Illuminate\Http\Response
     */
    public function destroy(PrimaryScheme $primaryScheme)
    {
        //
    }
}
