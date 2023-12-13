<?php

namespace App\Http\Controllers;

use App\Models\TourProgramme;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\TourProgrammeDataTable;
use App\Models\TourDetail;
use App\Models\User; 
use App\Models\City;
use App\Imports\TourImport;
use App\Exports\TourExport;

class TourController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->tours = new TourProgramme();
        
    }
    
    public function index(TourProgrammeDataTable $dataTable)
    {
        //abort_if(Gate::denies('tour_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $users = User::where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name')->orderBy('id','desc')->get();
        return $dataTable->render('tours.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userids = getUsersReportingToAuth();
        $users = User::where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name')->orderBy('id','desc')->get();
        return view('tours.create',compact('users'))->with('tours',$this->tours);
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
            $permission = !empty($request['id']) ? 'tour_edit' : 'tour_create' ;
            //abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if(!empty($request['detail']))
            {
                foreach ($request['detail'] as $key => $value) {
                    $tours = TourProgramme::create([
                        'date' => isset($value['date']) ? $value['date'] : null, 
                        'userid' => isset($value['userid']) ? $value['userid'] : null,
                        'town' => isset($value['town']) ? $value['town'] : '',
                        'objectives' => isset($value['objectives']) ? $value['objectives'] : '',
                    ]);
                    $towns = explode(',', $value['town']);
                    foreach ($towns as $key => $town) {
                        $cityid = City::where('city_name','=',$town)->pluck('id')->first();

                        $visited = TourDetail::whereHas('tourinfo',function($query) use($value){
                                                $query->where('userid','=',$value['userid']);
                                            })
                                            ->where('visited_cityid','=',$cityid)
                                            ->whereNotNull('visited_date')
                                            ->select('visited_date')
                                            ->latest()
                                            ->first();                
                        $lastvisited = (isset($cityid) && !empty($visited)) ? $visited['visited_date'] : null;
                        TourDetail::create([
                            'tourid' => isset($tours->id) ? $tours->id : null,
                            'city_id' => isset($cityid) ? $cityid : null, 
                            'last_visited' => isset($lastvisited) ? $lastvisited : null,
                        ]); 
                    }
                }
                return Redirect::to('tours')->with('message_success', 'TourProgramme Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
        }        
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function update(Request $request)
    {
        abort_if(Gate::denies('tasks_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            TourProgramme::where('id',$request['id'])->update([
                'date' => isset($request['date']) ? $request['date'] : null, 
                'userid' => isset($request['userid']) ? $request['userid'] : null,
                'town' => isset($request['town']) ? $request['town'] : '',
                'objectives' => isset($request['objectives']) ? $request['objectives'] : '',
            ]);
            $towns = explode(',', $request['town']);
            foreach ($towns as $key => $town) {
                $cityid = City::where('city_name','=',$town)->pluck('id')->first();
                $visited = TourDetail::whereHas('tourinfo',function($query) use($request){
                                        $query->where('userid','=',$request['userid']);
                                    })
                                    ->where('visited_cityid','=',$cityid)
                                    ->whereNotNull('visited_date')
                                    ->select('visited_date')
                                    ->latest()
                                    ->first();                
                $lastvisited = (isset($cityid) && !empty($visited)) ? $visited['visited_date'] : null;
                TourDetail::updateOrCreate(['tourid' => $request['id'], 'city_id' => $cityid],[
                    'tourid' => isset($request['id']) ? $request['id'] : null,
                    'city_id' => isset($cityid) ? $cityid : null, 
                    'last_visited' => isset($lastvisited) ? $lastvisited : null,
                ]); 
            }
        return redirect()->back()->with('message_danger', 'Error in Tour Update')->withInput(); 
    }

    public function show($id)
    {
        $id = decrypt($id);
        $tours = TourProgramme::find($id);
        return response()->json($tours);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //abort_if(Gate::denies('tour_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $tours = TourProgramme::find($id);
        return response()->json($tours);
    }

    public function destroy($id)
    {
        //abort_if(Gate::denies('tour_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $user = TourProgramme::find($id);
        // if($user->delete())
        // {
        //     return response()->json(['status' => 'success','message' => 'TourProgramme deleted successfully!']);
        // }
        // return response()->json(['status' => 'error','message' => 'Error in TourProgramme Delete!']);

        $user = TourProgramme::find($id);
      if(!empty($user)){
        TourDetail::where('tourid',$id)->delete();
         $user->delete();
         return response()->json(['status' => 'success','message' => 'TourProgramme deleted successfully!']);
       }

        return response()->json(['status' => 'error','message' => 'Error in TourProgramme Delete!']);
    }

    public function upload(Request $request) 
    {
      //abort_if(Gate::denies('tour_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new TourImport,request()->file('import_file'));
        return back();
    }
    public function download(Request $request)
    {
      //abort_if(Gate::denies('tour_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TourExport($request), 'tours.xlsx');
    }
    public function template()
    {
      //abort_if(Gate::denies('tour_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TourExport, 'tours.xlsx');
    }
}
