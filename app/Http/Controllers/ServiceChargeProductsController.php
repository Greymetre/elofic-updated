<?php

namespace App\Http\Controllers;

use App\DataTables\ServiceProductCategoryDataTable;
use App\DataTables\ServiceProductDivisionDataTable;
use App\Exports\ServiceProductCategoryExport;
use App\Imports\ServiceProductCategoryImport;
use App\Models\ServiceChargeCategories;
use App\Models\ServiceChargeDivision;
use App\Models\ServiceChargeProducts;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Excel;


class ServiceChargeProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function divisionindex(ServiceProductDivisionDataTable $dataTable)
    {
        abort_if(Gate::denies('services_product_division'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('service_dividion.index');
    }

    public function divisionstore(Request $request)
    {
        try {
            if (!empty($request['id'])) {
                $status = ServiceChargeDivision::where('id', $request['id'])->update($request->except(['_token', 'id', 'image']));
                $msg = 'Division Update Successfully';
            } else {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;

                $request['division_name'] = $request->input('division_name', '');
                $status = ServiceChargeDivision::create($request->except(['_token', 'image']));
                $msg = 'Division Store Successfully';
            }

            if ($status) {
                return Redirect::to('service-charge/dividsions')->with('message_success', $msg);
            }

            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function divisionedit($id)
    {
        $division = ServiceChargeDivision::find($id);

        if (!$division) {
            return response()->json(['status' => 'error', 'message' => 'Division not found']);
        }

        return response()->json($division);
    }

    public function divisionactive(Request $request, $id)
    {
        $divsion = ServiceChargeDivision::find($id);

        if (!$divsion) {
            return response()->json(['status' => 'error', 'message' => 'Divsion not found']);
        }
        $divsion->update(['active' => $divsion->active === 'Y' ? 'N' : 'Y']);
        return response()->json(['status' => 'success', 'message' => 'Divsion status changed successfully']);
    }

    public function divisiondelete($id)
    {
        $user = ServiceChargeDivision::find($id);
        if ($user->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Division deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Division Delete!']);
    }

    public function categoryindex(ServiceProductCategoryDataTable $dataTable)
    {
        return '<h1>Comeing Soon...</h1><p>We are working on it.</p>';
        abort_if(Gate::denies('services_product_category'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = ServiceChargeDivision::all();
        return $dataTable->render('service_category.index', compact('categories'));
    }

    public function categorystore(Request $request)
    {
      try
        { 
            // $permission = !empty($request['id']) ? 'subcategory_edit' : 'subcategory_create' ;
            // abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $status = '';
            // dd($request->all());
            if(!empty($request['id']))
            {
                $status = ServiceChargeCategories::where('id',$request['id'])->update($request->except(['_token','id','image']));
            }
            else
            {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = ServiceChargeCategories::create($request->except(['_token','image']));
            } 
            if($status)
            {
              return Redirect::to('service-charge/categories')->with('message_success', 'Category Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function categoryedit($id)
    {
        $division = ServiceChargeCategories::find($id);

        if (!$division) {
            return response()->json(['status' => 'error', 'message' => 'Category not found']);
        }

        return response()->json($division);
    }

    public function categoryactive(Request $request, $id)
    {
        $divsion = ServiceChargeCategories::find($id);

        if (!$divsion) {
            return response()->json(['status' => 'error', 'message' => 'Category not found']);
        }
        $divsion->update(['active' => $divsion->active === 'Y' ? 'N' : 'Y']);
        return response()->json(['status' => 'success', 'message' => 'Category status changed successfully']);
    }

    public function categorydownload()
    {
      abort_if(Gate::denies('services_product_category_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ServiceProductCategoryExport, 'service_product_categorycategories.xlsx');
    }

    public function categoryupload(Request $request) 
    {
      abort_if(Gate::denies('services_product_category_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new ServiceProductCategoryImport,request()->file('import_file'));
        return back();
    }
}
