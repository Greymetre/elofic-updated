<?php

namespace App\Http\Controllers;

use App\Models\{Customers,UserLogin,CustomerType,FirmType,Regions,Pincode,Country,CustomerDetails,Address,Attachment, SurveyData, Field, State, City, Beat, DealIn};
use App\Models\User; 
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\{CustomersDataTable,DistributorDataTable,CustomerLoginDataTable, SurveyDataTable};
use App\Imports\CustomersImport;
use App\Exports\CustomersExport;
use App\Exports\DistributorExport;
use App\Exports\SurveyExport;
use App\Exports\CustomersTemplate;
use App\Http\Requests\CustomersRequest;

use App\Models\EmployeeDetail;
use App\Models\ParentDetail;

class CustomerController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->customers = new Customers();
        $this->customerdetails = new CustomerDetails();
        $this->address = new Address();
        $this->path = 'customers';
    }

    public function index(Request $request)
    {
        ////abort_if(Gate::denies('customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $beats = Beat::where('active','=','Y')->whereHas('beatusers',function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('user_id',$userids);
                                }
                            })->select('id','beat_name')->get();
        $users= User::where('active','=','Y')->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name')->get();
        $states = State::where('active','=','Y')
                                ->whereHas('statecities',function($query) use($userids){
                                    if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                    {
                                        $query->whereHas('assignusers', function($q) use($userids){
                                            $q->whereIn('userid', $userids);
                                        });
                                    }
                                })
                                ->select('id','state_name')->get();
        $cities = City::where('active','=','Y')->whereHas('assignusers',function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('userid', $userids);
                                }
                            })->select('id','city_name')->get();
        $customertype = CustomerType::select('id','customertype_name')->orderBy('id','desc')->get();



       
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_branches = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $branches = array();
        $all_branch = array();
        $bkey = 0;
        foreach ($all_user_branches as $k => $val) {
            if (!in_array($val->getbranch->id, $all_branch)) {
                array_push($all_branch, $val->getbranch->id);
                $branches[$bkey]['id'] = $val->getbranch->id;
                $branches[$bkey]['name'] = $val->getbranch->branch_name;
                $bkey++;
            }
        }




        if ($request->ajax()) {
            $data = Customers::with('customertypes','firmtypes','createdbyname')
                        ->where(function ($query) use ($request , $userids) {
                            if(!empty($request['executive_id']))
                            {
                                $query->where('executive_id', $request['executive_id']);
                            }
                            if(!empty($request['customertype']))
                            {
                                $query->where('customertype', $request['customertype']);
                            }
                            // if(!empty($request['beat_id']))
                            // {
                            //     $query->whereHas('beatdetails',function($q) use($request){
                            //         $q->where('beat_id', $request['beat_id']);
                            //     });
                            // }

                            if(!empty($request['branch_id']))
                            {
                               $branch_user_id = User::whereIn('branch_id',$request['branch_id'])->pluck('id');
                                if(!empty($branch_user_id)){
                                   $query->whereIn('executive_id', $branch_user_id);  
                                }

                            }


                            if(!empty($request['state_id']))
                            {
                                $query->whereHas('customeraddress',function($q) use($request){
                                    $q->where('state_id', $request['state_id']);
                                });
                            }
                            if(!empty($request['city_id']))
                            {
                                $query->whereHas('customeraddress',function($q) use($request){
                                    $q->where('city_id', $request['city_id']);
                                });
                            }
                            if(!empty($request['search']) && is_array($request['search']) == false){
                                $search = $request['search'] ;
                                $query->where(function($query) use($search) {
                                    $query->where('name', 'like', "%{$search}%")
                                    ->Orwhere('first_name', 'like', "%{$search}%")
                                    ->Orwhere('last_name', 'like', "%{$search}%")
                                    ->Orwhere('email', 'like', "%{$search}%")
                                    ->Orwhere('mobile', 'like', "%{$search}%");
                                });
                            }
                            if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                            {
                                $query->whereIn('executive_id',$userids);
                            }
                            // $query->whereIn('customertype', ['2','3','4','5','6']);
                        })
                        ->latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('checkbox', function ($item) {
                        return '<input type="checkbox" id="manual_entry_'.$item->id.'" class="manual_entry_cb" value="'.$item->id.'" />';
                        })
                        ->editColumn('created_at', function($data)
                        {
                            return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                        })
                        ->addColumn('action', function ($query) {
                              $btn = '';
                              $activebtn ='';
                              if(auth()->user()->can(['customer_edit']))
                              {
                                $btn = $btn.'<a href="'.url("customers/".encrypt($query->id).'/edit') .'" class="btn btn-info btn-just-icon btn-sm" title="'.trans('panel.global.edit').' '.trans('panel.customers.title_singular').'">
                                                <i class="material-icons">edit</i>
                                            </a>';
                              }
                              if(auth()->user()->can(['customer_show']))
                              {
                                $btn = $btn.'<a href="'.url("customers/".encrypt($query->id)).'" class="btn btn-theme btn-just-icon btn-sm" title="'.trans('panel.global.show').' '.trans('panel.customers.title_singular').'">
                                                <i class="material-icons">visibility</i>
                                            </a>';
                              }
                              if(auth()->user()->can(['customer_delete']))
                              {
                                $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.customers.title_singular').'">
                                            <i class="material-icons">clear</i>
                                          </a>';
                              }
                              if(auth()->user()->can(['customer_active']))
                              {
                                $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                                $activebtn = '<div class="togglebutton">
                                            <label>
                                              <input type="checkbox"'.$active.' id="'.$query->id.'" class="customerActive">
                                              <span class="toggle"></span>
                                            </label>
                                          </div>';
                              }
                              return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            '.$btn.'
                                        </div>'.$activebtn;
                        })
                        ->addColumn('image', function ($query) {
                            $profileimage = !empty($query->profile_image) ? env('IMAGE_UPLOADS').$query->profile_image : asset('assets/img/placeholder.jpg') ;
                                return '<img src="'.$profileimage.'" border="0" width="70" class="rounded-circle imageDisplayModel" align="center" />';
                            })
                        ->rawColumns(['action','image','checkbox'])
                    ->make(true);
        }
        return view('customers.index', compact('beats','users','states','cities','customertype','branches'));
    }

    public function distributors(DistributorDataTable $dataTable)
    {
        ////abort_if(Gate::denies('distributor_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('customers.distributor');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        ////abort_if(Gate::denies('customer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //$status = DB::table('status')->whereIn('id',[1,2,5,7])->select('id', 'name')->orderBy('id','desc')->get();
        $userids = getUsersReportingToAuth();
        $pincodes = Pincode::where('active','=','Y')
                                ->whereHas('assigncitiesusers',function($query) use($userids){
                                    if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                    {
                                        $query->whereIn('userid', $userids);
                                    }
                                })
                                ->select('id','pincode')->orderBy('id','desc')->get();
        $countries = Country::where('active','=','Y')
                            ->whereHas('countrystates',function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereHas('statecities', function($query) use($userids){
                                        $query->whereHas('assignusers', function($q) use($userids){
                                                $q->whereIn('userid', $userids);
                                            });
                                    });
                                }
                            })
                            ->select('id','country_name')->orderBy('id','desc')->get();
        $customertype = CustomerType::select('id','customertype_name')->orderBy('id','desc')->get();
        $firmtype = FirmType::select('id','firmtype_name')->orderBy('id','desc')->get();
        $fields = Field::with('fieldsData')->whereIn('module',$customertype->pluck('id'))->where('active','=','Y')->get();
        $users = User::where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name')->orderBy('id','desc')->get();
        $deals = array();

        $parentcustomers = Customers::where('active','=','Y')->where('customertype', '!=', '2')->select('id','name')->orderBy('id','desc')->get();

        return view('customers.create',compact('pincodes','customertype','firmtype','pincodes','countries','fields', 'users','deals','parentcustomers'))->with('customers',$this->customers);
    }

    public function createDistributor()
    {
        ////abort_if(Gate::denies('distributor_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pincodes = Pincode::where('active','=','Y')->select('id','pincode')->orderBy('id','desc')->get();
        $countries = Country::where('active','=','Y')->select('id','country_name')->orderBy('id','desc')->get();
        $customertype = CustomerType::where('type_name', '=', 'distributor')->select('id','customertype_name')->orderBy('id','desc')->get();
        $firmtype = FirmType::select('id','firmtype_name')->orderBy('id','desc')->get();
        $fields = Field::with('fieldsData')->whereIn('module',$customertype->pluck('id'))->where('active','=','Y')->get();
        return view('customers.distributor_add',compact('pincodes','customertype','firmtype','pincodes','countries','fields'))->with('customers',$this->customers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomersRequest $request)
    {

        try
        { 

            ////abort_if(Gate::denies('customer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['active'] = 'Y';
            $request['created_by'] = Auth::user()->id;
            $docimages = collect([]);
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'profile';
                unset($request['image']);
                $request['profile_image'] = fileupload($image, $this->path, $filename) ;
                
            }
            if($request->file('imggstin')){
                $image = $request->file('imggstin');
                $filename = 'gstin';
                unset($request['imggstin']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'gstin', 
                ]);
            }
            if($request->file('imgpan')){
                $image = $request->file('imgpan');
                $filename = 'pan';
                unset($request['imgpan']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'pan', 
                ]);
            }
            if($request->file('imgaadhar')){
                $image = $request->file('imgaadhar');
                $filename = 'aadhar';
                unset($request['image']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'aadhar', 
                ]);
            }
            if($request->file('imgother')){
                $image = $request->file('imgother');
                $filename = 'other';
                unset($request['imgother']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'other', 
                ]);
            }
            // echo '<pre>';
            // print_r($request->all());
            //  die('correct');
            $response = $this->customers->save_data($request);

            if($response['status'] == 'success')
            {
                $request['customer_id'] = $response['customer_id'];
                $this->customerdetails->save_data($request);
                $this->address->save_data($request);
                $attachments = $docimages->map(function ($item, $key) use($request) {
                                $item['customer_id'] = $request['customer_id'] ;
                                return $item;
                            });
                if($attachments->isNotEmpty())
                {
                    Attachment::insert($attachments->toArray());
                }
                if(!empty($request['survey']))
                {
                    foreach ($request['survey'] as $key => $rows) {

                        if(!empty($rows['value']))
                        {
                            $value = (is_array($rows['value'])) ? implode(', ', $rows['value']) : $rows['value'] ;
                            SurveyData::updateOrCreate(['customer_id' => $request['customer_id'], 'field_id' => $rows['field_id']], [ 
                                'customer_id' => $request['customer_id'],
                                'field_id' => $rows['field_id'],
                                'value' => $value,
                                'created_by'  => Auth::user()->id,
                            ]);
                        }
                    }
                }

                if($request['dealing'])
                {
                    $dealings = $request['dealing'];
                    foreach ($dealings as $key => $deal) {
                        $types = !empty($deal['types']) ? $deal['types'] : '';
                        DealIn::updateOrCreate([
                            'customer_id' => $request['id'],
                            'types' => $types
                            ],[
                            'customer_id'   => !empty($request['id'])? $request['id']:null,
                            'types' => $types,
                            'hcv' => isset($deal['hcv']) ? $deal['hcv'] : false, 
                            'mav' => isset($deal['mav']) ? $deal['mav'] : false, 
                            'lmv' => isset($deal['lmv']) ? $deal['lmv'] : false, 
                            'lcv' => isset($deal['lcv']) ? $deal['lcv'] : false, 
                            'other' => isset($deal['other']) ? $deal['other'] : false, 
                            'tractor' => isset($deal['tractor']) ? $deal['tractor'] : false, 
                        ]);
                    }
                }



             //employee start

             if(!empty($request['executive_id']))
                {
                    foreach ($request['executive_id'] as $key => $rows) {
                $employeeDetail = EmployeeDetail::create(
                  [ 
                    'customer_id' => $request['customer_id'],
                    'user_id' => $rows,
                    'created_by' => Auth::user()->id,
                  ]
                 );
      
                }
                }

               // employee end


               //parent start

                if(!empty($request['parent_id']))
                {
                    foreach ($request['parent_id'] as $key => $rows) {
                $parentDetail = ParentDetail::create(
                  [ 
                    'customer_id' => $request['customer_id'],
                    'parent_id' => $rows,
                    'created_by' => Auth::user()->id,
                  ]
                 );
                }
                }


               // parent end










                return Redirect::to('customers')->with('message_success', $response['message']); 
            }
             return redirect()->back()->with('message_danger', $response['message'])->withInput();
             
        }         
        catch(\Exception $e)
        {

          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        ////abort_if(Gate::denies('customer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
         $id = decrypt($id);
        $customers = Customers::find($id);
        $customers['due_amount'] = totalDueAmount($id);
        return view('customers.show')->with('customers',$customers);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        ////abort_if(Gate::denies('customer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $id = decrypt($id);
        $customers = Customers::with('surveys')->find($id);
        $deals = DealIn::where('customer_id','=',$id)->get();
        $pincodes = Pincode::where('active','=','Y')->whereHas('assigncitiesusers',function($query) use($userids){
                                    if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                    {
                                        $query->whereIn('userid', $userids);
                                    }
                                })->select('id','pincode')->orderBy('id','desc')->get();
        $countries = Country::where('active','=','Y')->whereHas('countrystates',function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereHas('statecities', function($query) use($userids){
                                        $query->whereHas('assignusers', function($q) use($userids){
                                                $q->whereIn('userid', $userids);
                                            });
                                    });
                                }
                            })->select('id','country_name')->orderBy('id','desc')->get();
        $customertype = CustomerType::select('id','customertype_name')->orderBy('id','desc')->get();
        $firmtype = FirmType::select('id','firmtype_name')->orderBy('id','desc')->get();
        $customers['gstin_image'] = $customers['customerdocuments']->where('document_name','gstin')->pluck('file_path')->first();
        $customers['pan_image'] = $customers['customerdocuments']->where('document_name','pan')->pluck('file_path')->first();
        $customers['aadhar_image'] = $customers['customerdocuments']->where('document_name','aadhar')->pluck('file_path')->first();
        $customers['other_image'] = $customers['customerdocuments']->where('document_name','other')->pluck('file_path')->first();
        $fields = Field::with('fieldsData')->whereIn('module',[$customers->customertype])->where('active','=','Y')->get();
        $users = User::where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name')->orderBy('id','desc')->get();

        $parentcustomers = Customers::where('active','=','Y')->where('customertype', '!=', '2')->select('id','name')->orderBy('id','desc')->get();
        return view('customers.create',compact('pincodes','customertype','firmtype','pincodes','countries','fields','users','deals','parentcustomers'))->with('customers',$customers);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try
        { 
            ////abort_if(Gate::denies('customer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['updated_by'] = Auth::user()->id;
            $docimages = collect([]);
            if($request->file('image')){
                $path = 'customers/';
                $image = $request->file('image');
                $filename = 'profile_'.$id;
                unset($request['image']);
                $request['profile_image'] = fileupload($image, $this->path, $filename) ;
            }
            if($request->file('imggstin')){
                $path = 'customers/';
                $image = $request->file('imggstin');
                $filename = 'gstin_'.$id;
                unset($request['imggstin']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'gstin', 
                ]);
            }
            if($request->file('imgpan')){
                $path = 'customers/';
                $image = $request->file('imgpan');
                $filename = 'pan_'.$id;
                unset($request['imgpan']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'pan', 
                ]);
            }
            if($request->file('imgaadhar')){
                $path = 'customers/';
                $image = $request->file('imgaadhar');
                $filename = 'aadhar_'.$id;
                unset($request['image']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'aadhar', 
                ]);
            }
            if($request->file('imgother')){
                $path = 'customers/';
                $image = $request->file('imgother');
                $filename = 'other_'.$id;
                unset($request['imgother']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'other', 
                ]);
            }
            $request['customer_id'] = $id;
            $response = $this->customers->update_data($request);
            if($response['status'] == 'success')
            {
                $this->customerdetails->save_data($request);
                $this->address->save_data($request);
                $attachments = $docimages->map(function ($item, $key) use($request) {
                                $item['customer_id'] = $request['customer_id'] ;
                                return $item;
                            });


                if($attachments->isNotEmpty())
                {
                    Attachment::insert($attachments->toArray());
                }

                if($request['survey'])
                {
                    foreach ($request['survey'] as $key => $rows) {

                        if(!empty($rows['value']))
                        {
                            $value = (is_array($rows['value'])) ? implode(', ', $rows['value']) : $rows['value'] ;
                            SurveyData::updateOrCreate(['customer_id' => $id, 'field_id' => $rows['field_id']], [ 
                                'customer_id' => $id,
                                'field_id' => $rows['field_id'],
                                'value' => $value,
                                'created_by'  => Auth::user()->id,
                            ]);
                        }
                    }
                }

                if($request['dealing'])
                {
                    $dealings = $request['dealing'];
                    foreach ($dealings as $key => $deal) {
                        $types = !empty($deal['types']) ? $deal['types'] : '';
                        DealIn::updateOrCreate([
                            'customer_id' => $request['id'],
                            'types' => $types
                            ],[
                            'customer_id'   => !empty($request['id'])? $request['id']:null,
                            'types' => $types,
                            'hcv' => isset($deal['hcv']) ? $deal['hcv'] : false, 
                            'mav' => isset($deal['mav']) ? $deal['mav'] : false, 
                            'lmv' => isset($deal['lmv']) ? $deal['lmv'] : false, 
                            'lcv' => isset($deal['lcv']) ? $deal['lcv'] : false, 
                            'other' => isset($deal['other']) ? $deal['other'] : false, 
                            'tractor' => isset($deal['tractor']) ? $deal['tractor'] : false, 
                        ]);
                    }
                }



             //employee start

             if(!empty($request['executive_id']))
                {

                EmployeeDetail::where('customer_id',$request['id'])->delete(); 
                foreach($request['executive_id'] as $key => $rows) {
                $employeeDetail = EmployeeDetail::updateOrCreate(
                  //['customer_id' => $request['id']],

                  [ 
                    'customer_id' => $request['id'],
                    'user_id' => $rows,
                    'created_by' => Auth::user()->id,
                  ]
                 );
                }
                }

            //employee end

            //parent start

                if(!empty($request['parent_id']))
                {
                    ParentDetail::where('customer_id',$request['id'])->delete(); 
                    foreach ($request['parent_id'] as $key => $rows) {
                $parentDetail = ParentDetail::updateOrCreate(
                 // ['customer_id' => $request['id']],  
                  [ 
                    'customer_id' => $request['id'],
                    'parent_id' => $rows,
                    'created_by' => Auth::user()->id,
                  ]
                 );
                }
                }

               // parent end

                return Redirect::to('customers')->with('message_success', $response['message']); 
            }
             return redirect()->back()->with('message_danger', $response['message'])->withInput();
             
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ////abort_if(Gate::denies('customer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try
        { 
            $walletids = DB::table('wallets')->where('customer_id', '=', $id)->select('id')->get()->pluck('id')->toArray();
            DB::table('wallet_details')->whereIn('wallet_id', $walletids)->delete();
            DB::table('wallets')->where('customer_id', '=', $id)->delete();
            $orderids = DB::table('orders')->where('buyer_id', '=', $id)
                            ->orWhere('seller_id', '=', $id)
                            ->select('id')->get()->pluck('id')->toArray();
            DB::table('order_details')->whereIn('order_id', $orderids)->delete();
            DB::table('orders')->where('buyer_id', '=', $id)->orWhere('seller_id', '=', $id)->delete();
            $saleids = DB::table('sales')->where('buyer_id', '=', $id)
                            ->orWhere('seller_id', '=', $id)
                            ->select('id')->get()->pluck('id')->toArray();
            DB::table('sales_details')->whereIn('sales_id', $saleids)->delete();
            DB::table('sales')->where('buyer_id', '=', $id)->orWhere('seller_id', '=', $id)->delete();
            DB::table('attachments')->where('customer_id', '=', $id)->delete();
            DB::table('beat_customers')->where('customer_id', '=', $id)->delete();
            DB::table('check_in')->where('customer_id', '=', $id)->delete();
            DB::table('notifications')->where('customer_id', '=', $id)->delete();
            DB::table('supports')->where('customer_id', '=', $id)->delete();
            DB::table('survey_data')->where('customer_id', '=', $id)->delete();
            DB::table('tasks')->where('customer_id', '=', $id)->delete();
            DB::table('visit_reports')->where('customer_id', '=', $id)->delete();
            DB::table('user_activities')->where('customerid', '=', $id)->delete();
            CustomerDetails::where('customer_id',$id)->delete();
            Address::where('customer_id',$id)->delete();

            EmployeeDetail::where('customer_id',$id)->delete();
            ParentDetail::where('customer_id',$id)->delete();

            $customer = Customers::find($id);
            if($customer->delete())
            {
                return response()->json(['status' => 'success','message' => 'Customer deleted successfully!']);
            }
            return response()->json(['status' => 'error','message' => 'Error in Customer Delete!']);
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
    
    public function active(Request $request)
    {
        if(Customers::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Customer '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function customersLogin(CustomerLoginDataTable $dataTable)
    {
        ////abort_if(Gate::denies('customer_login'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('customers.login');
    }

    public function upload(Request $request) 
    {
        ////abort_if(Gate::denies('customer_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new CustomersImport,request()->file('import_file'));
        return back();
    }
    public function download(Request $request)
    {     
        ////abort_if(Gate::denies('customer_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CustomersExport($request), 'customers.xlsx');
    }
    public function distributordownload()
    {
        ////abort_if(Gate::denies('customer_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new DistributorExport, 'distributor.xlsx');
    }


    public function template()
    {
        ////abort_if(Gate::denies('customer_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CustomersTemplate, 'customers.xlsx');
    }

    public function survey(Request $request)
    {
        if ($request->ajax()) {
            $data = SurveyData::with('customers')->select('customer_id', DB::raw('count(value) as total_ans'))
                ->groupBy('customer_id')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('survey', function($row) {
                        $survey = '';
                        $questions = SurveyData::where('customer_id',$row['customer_id'])->select('field_id', 'value')->get();
                        if(!empty($questions))
                        {
                            foreach ($questions as $key => $value) {
                                $survey = $survey.'<p>'.$value['fields']['label_name'].'</p>'.'<b> Ans. '.$value['value'].'</b>';
                            }
                        }
                        return $survey;
                    })
                    ->rawColumns(['survey'])
                    ->make(true);
        }
        $customertype = CustomerType::select('id','customertype_name')->orderBy('id','desc')->get();
        return view('customers.survey',compact('customertype'));
    }

    public function surveyDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SurveyExport($request), 'survey.xlsx');
    }
}
