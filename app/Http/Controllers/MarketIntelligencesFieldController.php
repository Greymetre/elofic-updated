<?php

namespace App\Http\Controllers;

use App\DataTables\MarketIntelligencesFieldsDataTable;
use App\DataTables\MarketIntelligencesDataTable;
use App\Exports\ExcelExport;
use App\Models\CustomerType;
use App\Models\Division;
use App\Models\MarketIntelligenceServey;
use App\Models\MarketIntelligencesField;
use App\Models\MarketIntelligencesFielddata;
use Illuminate\Http\Request;
use Excel;
use Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class MarketIntelligencesFieldController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->fields = new MarketIntelligencesField();
    }
    public function index(MarketIntelligencesFieldsDataTable $dataTable, Request $request)
    {
        //abort_if(Gate::denies('fields_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('market_intelligences.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customertype = CustomerType::select('id', 'customertype_name')->orderBy('id', 'desc')->get();
        $divisions = Division::select('id', 'division_name')->orderBy('id', 'desc')->get();
        return view('market_intelligences.create', compact('customertype', 'divisions'))->with('fields', $this->fields);
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
            //$useraccess = !empty($request['id']) ? 'fields_edit' : 'fields_create' ;
            //abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['created_by'] = Auth::user()->id;
            $request['is_required'] = ($request['is_required'] == 'on') ? true : false;
            $request['is_multiple'] = ($request['is_multiple'] == 'on') ? true : false;
            $request['active'] = 'Y';
            $nextId = MarketIntelligencesField::max('id') + 1;
            $request['key'] = $request['field_name'] ? str_replace(' ', '_', strtolower($request['field_name'])).'_'.$nextId : '';
            if ($fields = MarketIntelligencesField::create($request->except(['_token']))) {
                if ($request['details']) {
                    foreach ($request['details'] as $key => $value) {
                        if (!empty($value['value'])) {
                            MarketIntelligencesFielddata::create([
                                'field_id' => $fields['id'],
                                'value' => $value['value'],
                            ]);
                        }
                    }
                }
                return Redirect::to('market_intelligences')->with('message_success', 'Field Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //abort_if(Gate::denies('fields_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $fields = MarketIntelligencesField::find($id);
        $customertype = CustomerType::select('id', 'customertype_name')->orderBy('id', 'desc')->get();
        $divisions = Division::select('id', 'division_name')->orderBy('id', 'desc')->get();
        return view('market_intelligences.create', compact('customertype', 'divisions'))->with('fields', $fields);
    }

    public function update(Request $request, $id)
    {
        try {
            $request['key'] = $request['field_name'] ? str_replace(' ', '_', strtolower($request['field_name'])).'_'.$id : '';
            if (MarketIntelligencesField::where('id', $id)->update($request->except(['_token', '_method', 'image', 'details']))) {
                if ($request['details']) {
                    $fieldValue = array();
                    foreach ($request['details'] as $key => $value) {
                        if (!empty($value['value'])) {
                            MarketIntelligencesFielddata::updateOrCreate(
                                ['field_id' => $id, 'value' => $value['value']],
                                [
                                    'field_id' => $id,
                                    'value' => $value['value']
                                ]
                            );
                            array_push($fieldValue, $value['value']);
                        }
                    }
                    MarketIntelligencesFielddata::where('field_id', $id)->whereNotIn('value', $fieldValue)->delete();
                }

                return Redirect::to('market_intelligences')->with('message_success', 'Data Update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        // abort_if(Gate::denies('fields_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        MarketIntelligencesFielddata::where('field_id', $id)->delete();
        $field = MarketIntelligencesField::find($id);
        // if(EnquireFields::where('field_id',$field['field_name'])->count() >= 1)
        // {
        //     return response()->json(['status' => 'error','message' => 'This field already in use!']);
        // }
        if ($field->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Field deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in User Delete!']);
    }

    public function active(Request $request)
    {
        if (Field::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y'])) {
            $message = ($request['active'] == 'Y') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'Field ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }


    public function download(Request $request)
    {

        if ($request->ip() != '111.118.252.250') {
            return view('work_in_progress');
        }
        $fields = MarketIntelligenceServey::with('createdbyname', 'state')->get();
        $data = array();
        $heading = [
            'Date',
            'User name',
            'EMP',
            'State',
            'Division',
            'Category',
            'Brand Name',
            'Product name',
            'Cooling Arrangement',
            'Type of Construction',
            'HP',
            'Stage',
            'Phase',
            'Head Range MTR',
            'Discharge Range-LPM',
            'Suc x Del (MM)',
            'Speed (RPM)',
            'MRP',
            'List Price',
            'Landed to dealers w/o Tax',
            'Remarks',
            'Upload Image'
        ];

        foreach ($fields as $key => $value) {
            $data[$key]['date'] = date('d M Y', strtotime($value->created_at));
            $data[$key]['user_name'] = $value->createdbyname ? $value->createdbyname->name : '-';
            $data[$key]['emp'] = $value->createdbyname ? $value->createdbyname->employee_codes : '-';
            $data[$key]['state'] = $value->state ? $value->state->state_name : '-';
            $data[$key]['division'] = $value->division_id ?? '-';
            $data[$key]['category'] = $value->category_id ?? '-';
            $data[$key]['brand'] = $value->brand_id ?? '-';
            $data[$key]['product'] = $value->product_name ?? '-';
            $data[$key]['cooling'] = $value->cooling_arrangement_id ?? '-';
            $data[$key]['type'] = $value->type_of_construction_id ?? '-';
            $data[$key]['hp'] = $value->hp_id ?? '-';
            $data[$key]['stage'] = $value->stage ?? '-';
            $data[$key]['phase'] = $value->phase_id ?? '-';
            $data[$key]['head_range'] = $value->head_range_mtr ?? '-';
            $data[$key]['discharge_range'] = $value->discharge_range_lpm ?? '-';
            $data[$key]['suc_x_del'] = $value->sucx_del ?? '-';
            $data[$key]['speed'] = $value->speed ?? '-';
            $data[$key]['mrp'] = $value->mrp ?? '-';
            $data[$key]['list_price'] = $value->list_price ?? '-';
            $data[$key]['landed'] = $value->landed_to_dealers ?? '-';
            $data[$key]['remark'] = $value->remark ?? '-';
            $data[$key]['image'] = $value->getMedia('servey_image')->count() > 0 
                ? $value->getMedia('servey_image')[0]->getFullUrl() 
                : 'No';
        }
        return Excel::download(new ExcelExport($heading, $data), 'MarketIntelligencesFields.xlsx');
    }

    public function marketIntelligence(MarketIntelligencesDataTable $dataTable, Request $request)
    {
        //abort_if(Gate::denies('fields_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('reports.customer_makert_intelligence');
    }
}
