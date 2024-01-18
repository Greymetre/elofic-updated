<?php

namespace App\Http\Controllers;

use App\Models\SchemeHeader;
use Illuminate\Http\Request;
use App\Http\Requests\SchemeRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\Models\SchemeDetails;
use App\DataTables\SchemesDataTable;
use App\Imports\SchemeImport;
use App\Exports\SchemeExport;
use App\Exports\SchemeTepmlate;

class SchemeController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->schemes = new SchemeHeader();
        $this->path = 'schemes';
    }
    
    public function index(SchemesDataTable $dataTable)
    {
        abort_if(Gate::denies('scheme_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('schemes.index');
    }


    public function create()
    {
        abort_if(Gate::denies('scheme_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('schemes.create')->with('schemes',$this->schemes);
    }


    public function store(SchemeRequest $request)
    {
        try
        { 
            abort_if(Gate::denies('scheme_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
           $validator = Validator::make($request->all(), [
                'scheme_name' => 'required',
            ]); 
            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
            $request['scheme_image'] = '';
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'schemeheader'.autoIncrementId('SchemeHeader','id');
                unset($request['image']);
                $request['scheme_image'] = fileupload($image, $this->path, $filename);
            }
            if($id = SchemeHeader::insertGetId([
                'active' => 'Y',
                'scheme_name' => isset($request['scheme_name']) ? $request['scheme_name'] : '',
                'scheme_description' => isset($request['scheme_description']) ? $request['scheme_description'] : '',
                'start_date' => isset($request['start_date']) ? $request['start_date'] : '',
                'end_date' => isset($request['end_date']) ? $request['end_date'] : '',
                'scheme_image' => isset($request['scheme_image']) ? $request['scheme_image'] : '',
                'scheme_type' => isset($request['scheme_type']) ? $request['scheme_type'] : '',
                'point_value' => isset($request['point_value']) ? $request['point_value'] : '',
                'points_start_date' => isset($request['points_start_date']) ? $request['points_start_date'] : null,
                'points_end_date' => isset($request['points_end_date']) ? $request['points_end_date'] : null,
                'block_points' => isset($request['block_points']) ? $request['block_points'] : null,
                'block_percents' => isset($request['block_percents']) ? $request['block_percents'] : null,
                'created_at' => getcurentDateTime(),
            ]))
            {
                $schmedetils = collect([]);
                if($request['points'])
                {
                    foreach ($request['points'] as $key => $value) {
                        $schmedetils->push([
                            'active' => 'Y',
                            'scheme_id' => $id,
                            'product_id' => !empty($request['product_id']) ? $request['product_id'][$key] : null,
                            'category_id' => !empty($request['category_id']) ? $request['category_id'][$key] : null,
                            'subcategory_id' => !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null,
                            'minimum' => isset($request['minimum']) ? $request['minimum'][$key] : null,
                            'maximum' => isset($request['maximum']) ? $request['maximum'][$key] : null,
                            'points' => isset($request['points']) ? $request['points'][$key] : 0,
                        ]);
                    }
                    if($schmedetils->isNotEmpty())
                    {
                        SchemeDetails::insert($schmedetils->toArray());
                    } 
                }
              return Redirect::to('schemes')->with('message_success', 'Scheme Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Scheme Store')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function show(SchemeHeader $schemeheader)
    {
        abort_if(Gate::denies('scheme_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }


    public function edit($id)
    {
        abort_if(Gate::denies('scheme_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $schemes = SchemeHeader::find($id);
        return view('schemes.create')->with('schemes',$schemes);
    }


    public function update(SchemeRequest $request, $id)
    {
        try
        { 
            abort_if(Gate::denies('scheme_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
           $validator = Validator::make($request->all(), [
                'scheme_name' => 'required',
            ]); 
            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
            $id = decrypt($id);
            $scheme = SchemeHeader::find($id);
            $scheme->scheme_name = isset($request['scheme_name'])? $request['scheme_name'] :'';
            $scheme->scheme_description = isset($request['scheme_description'])? $request['scheme_description'] :'';
            $scheme->start_date = $request['start_date'];
            $scheme->end_date = $request['end_date'];
            $scheme->scheme_type = isset($request['scheme_type'])? $request['scheme_type'] :'';
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'scheme'.$id;
                unset($request['image']);
                $scheme->scheme_image = fileupload($image, $this->path, $filename);
            }
            if($scheme->save())
            {
                $existdetails = SchemeDetails::where('scheme_id',$id)->select('id','product_id','category_id','minimum','maximum','points')->get();
                $schmedetils = collect([]);
                foreach ($request['points'] as $key => $value) {
                    if(!empty($request['detail_id'][$key]))
                    {
                        $schmedetils = SchemeDetails::firstOrNew(array('id' => $request['detail_id'][$key]));
                    }
                    else
                    {
                        $schmedetils = new SchemeDetails();
                    }
                    $schmedetils->active = 'Y';
                    $schmedetils->scheme_id = $id;
                    $schmedetils->product_id = !empty($request['product_id']) ? $request['product_id'][$key] : null;
                    $schmedetils->category_id = !empty($request['category_id']) ? $request['category_id'][$key] : null;
                    $schmedetils->subcategory_id = !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null;
                    $schmedetils->minimum = !empty($request['minimum']) ? $request['minimum'][$key] : null;
                    $schmedetils->maximum = !empty($request['maximum']) ? $request['maximum'][$key] : null;
                    $schmedetils->points = !empty($request['points']) ? $request['points'][$key] : null;
                    $schmedetils->save();
                }


              return Redirect::to('schemes')->with('message_success', 'Scheme Update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Scheme Update')->withInput();
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

    }
    public function destroy($id)
    {
        abort_if(Gate::denies('scheme_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        SchemeDetails::where('scheme_id','=',$id)->delete();
        $scheme = SchemeHeader::find($id);
        if($scheme->delete())
        {
            return response()->json(['status' => 'success','message' => 'Scheme deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Scheme Delete!']);
    }
    
    public function active(Request $request)
    {
        if(SchemeHeader::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            SchemeDetails::where('scheme_id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']);
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Scheme '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
        abort_if(Gate::denies('scheme_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new SchemeImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('scheme_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SchemeExport, 'schemes.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('scheme_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SchemeTepmlate, 'schemes.xlsx');
    }
}
