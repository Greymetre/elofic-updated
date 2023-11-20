<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\UserRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\DataTables\UsersDataTable;
use App\DataTables\UserCityDataTable;
use App\Imports\UserImport;
use App\Exports\UserExport;
use App\Exports\UserTemplate;
use App\Models\UserDetails;
use App\Models\City;
use App\Models\UserCityAssign;
use App\Imports\UserCityImport;
use App\Exports\UserCityMapedExport;

class UsersController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->user = new User();
        $this->path = 'users';
        
    }

    public function index(UsersDataTable $dataTable)
    {
       //abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return $dataTable->render('users.index');
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles = Role::where('name','!=','super-admin')->pluck('name', 'id');
        $cities = City::where('active','=','Y')->pluck('city_name','id');
        $reportings = User::where('active','=','Y')->select('id','name')->get();
        return view('users.create', compact('roles','cities','reportings'))->with('user',$this->user);
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'active'   =>  isset($request['active']) ? $request['active'] :'Y',
            'name'   =>  isset($request['name']) ? $request['name'] :$request['first_name'].' '.$request['last_name'],
            'first_name'   =>  isset($request['first_name']) ? $request['first_name'] :'',
            'last_name'   =>  isset($request['last_name']) ? $request['last_name'] :'',
            'mobile'   =>  isset($request['mobile']) ? $request['mobile'] :null,
            'email'   =>  isset($request['email']) ? $request['email'] :'',
            'password'   =>  isset($request['password']) ? Hash::make($request['password']) :'',
            'notification_id'   =>  isset($request['notification_id']) ? $request['notification_id'] :'',
            'device_type'   =>  isset($request['device_type']) ? $request['device_type'] :'',
            'gender'   =>  isset($request['gender']) ? $request['gender'] :'',
            'profile_image'   =>  isset($request['profile_image']) ? $request['profile_image'] :'',
            'latitude'   =>  isset($request['latitude']) ? $request['latitude'] :'',
            'longitude' => isset($request['longitude']) ? $request['longitude'] :'',
            'location' => !empty($request['location']) ? $request['location'] :'',
        ]);
        $user->roles()->sync($request->input('roles', []));
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $user->givePermissionTo($permissions);

        if($request->file('image')){
            $image = $request->file('image');
            $filename = 'profile_'.$user['id'];
            $profile = fileupload($image, $this->path, $filename);
            User::where('id',$user['id'])->update([ 'profile_image' => $profile]);
        }
        if(!empty($request['cities']))
            {
                foreach ($request['cities'] as $key => $city) {
                    UserCityAssign::updateOrCreate(['userid' => $user['id'], 'city_id' => $city],
                    ['userid' => $user['id'], 'city_id' => $city,  'reportingid' => $request['reportingid']]);
                }
            }
        UserDetails::insert([
            'user_id' => $user['id'], 
            'date_of_birth'   =>  isset($request['date_of_birth']) ? $request['date_of_birth'] :null,
            'date_of_joining'   =>  isset($request['date_of_joining']) ? $request['date_of_joining'] :null,
            'marital_status'   =>  isset($request['marital_status']) ? $request['marital_status'] :null,
        ]);
        return redirect()->route('users.index');

    }

    public function edit($id)
    {
        //abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $user = User::with('userinfo')->where('id',$id)->first();
        $roles = Role::where('name','!=','super-admin')->pluck('name', 'id');
        $user->load('roles');
        $user['cities'] = UserCityAssign::where('userid','=',$id)->pluck('city_id');
        //$user['reportingid'] = UserCityAssign::where('userid','=',$id)->pluck('reportingid')->first();
        $cities = City::where('active','=','Y')->pluck('city_name','id');
        $reportings = User::where('active','=','Y')->select('id','name')->get();

        return view('users.edit', compact('roles', 'user','cities','reportings'));
    }

    //public function update(Request $request, User $user)
    public function update(Request $request, $id)
    {
        if($request->file('image')){
            $image = $request->file('image');
            $filename = 'profile_'.$id;
            $request['profile_image'] = fileupload($image, $this->path, $filename);
        }

        UserDetails::updateOrCreate(['user_id' => $id],[
            'user_id' => $id,
            'date_of_birth'   =>  isset($request['date_of_birth']) ? $request['date_of_birth'] :null,
            'date_of_joining'   =>  isset($request['date_of_joining']) ? $request['date_of_joining'] :null,
            'marital_status'   =>  isset($request['marital_status']) ? $request['marital_status'] :'',
        ]);   
        $user = User::where('id',$id)->first();
        $user->name = isset($request['name']) ? $request['first_name'].' '.$request['last_name'] :'';
        $user->first_name = isset($request['first_name']) ? $request['first_name'] :'';
        $user->last_name = isset($request['last_name']) ? $request['last_name'] :'';
        $user->mobile = isset($request['mobile']) ? $request['mobile'] :'';
        $user->email = isset($request['email']) ? $request['email'] :'';
        if($request['password'])
        {
            $user->password = isset($request['password']) ? Hash::make($request['password']) :'';
        }
        if($request['profile_image'])
        {
            $user->profile_image = isset($request['profile_image']) ? $request['profile_image'] :'';
        }
        $user->location = !empty($request['location']) ? $request['location'] : '';
        $user->gender = isset($request['gender']) ? $request['gender'] :'';
        $user->reportingid = isset($request['reportingid']) ? $request['reportingid'] : null;
        $user->region_id = isset($request['region_id']) ? $request['region_id'] :null;
        if($user->save())
        {
            $user->roles()->sync($request->input('roles', []));
            $permissions = $user->getPermissionsViaRoles()->pluck('name');
            $user->givePermissionTo($permissions);
            if(!empty($request['cities']))
            {
                foreach ($request['cities'] as $key => $city) {
                    UserCityAssign::updateOrCreate(['userid' => $id, 'city_id' => $city],
                    ['userid' => $id, 'city_id' => $city,  'reportingid' => $request['reportingid']]);
                }
            }
            UserCityAssign::whereNotIn('city_id',$request['cities'])->where('userid' ,$id)->delete();
        }
        return redirect()->route('users.index');

    }

    public function show($id)
    {
        //abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $user = User::find($id);
        $user->load('roles');
        return view('users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return back();

    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }

    public function upload(Request $request) 
    {
        abort_if(Gate::denies('user_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new UserImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('user_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UserExport, 'users.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('user_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UserTemplate, 'users.xlsx');
    }

    public function active(Request $request)
    {
        if(User::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'User '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function userCity(UserCityDataTable $dataTable)
    {
       return $dataTable->render('users.usercity');
    }

    public function userCityUpload(Request $request) 
    {
        abort_if(Gate::denies('user_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new UserCityImport,request()->file('import_file'));
        return back();
    }
    public function userCitydownload()
    {
        abort_if(Gate::denies('user_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UserCityMapedExport, 'users.xlsx');
    }
}
