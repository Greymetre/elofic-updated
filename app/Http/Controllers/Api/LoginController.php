<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Validator;
use Gate;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->users = new User();
        
        $this->usersLogin = new UserLogin();
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 402;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
        $this->path = 'users';
    }

    public function login(Request $request)
    {
        try
        { 
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]); 
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->noContent); 
            }
            $username = $request->input('username');
            if (! $user = $this->users->where('mobile', $username)->orWhere('email', $username)->first()) {
                return response()->json(['status' => 'error','message' => 'User not found' ], $this->notFound);
            }
            $password = $request->input('password');
            if (Hash::check($password,$user['password']) ) {
                $token = $user->createToken('gSQ01LKOg1JV0O9eMsDiAN0TqkQlOpulK7vWemPF')->accessToken;
                $user->update([
                    'notification_id' => !empty($request['device_token']) ? $request['device_token'] : '',
                    'device_type' => isset($request['device_type']) ? $request['device_type'] : ''
                ]);
                $nestedData['id'] = isset($user['id']) ? $user['id'] :0;
                $nestedData['name'] = isset($user['name']) ? $user['name'] :'';
                $nestedData['first_name'] = isset($user['first_name']) ? $user['first_name'] :'';
                $nestedData['last_name'] = isset($user['last_name']) ? $user['last_name'] :'';
                $nestedData['email'] = isset($user['email']) ? $user['email'] :'';
                $nestedData['mobile'] = isset($user['mobile']) ? $user['mobile'] :'';
                $nestedData['profile_image'] = isset($user['profile_image']) ? $user['profile_image'] :'';
                $nestedData['gender'] = isset($user['gender']) ? $user['gender'] :'';
                $nestedData['payroll_id'] = isset($user['payroll']) ? $user['payroll'] :'';
                $nestedData['access_token'] = $token;
                $user['provider'] = 'users';
                $user['entry_from'] = 'app';
                $this->usersLogin->save_data($user);

                return response()->json(['status' => 'success','userinfo' => $nestedData ], $this->successStatus); 
            }
            else
            {
                return response()->json(['status' => 'error','message' => 'Password not match' ], $this->unauthorized);
            }
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }

    public function getProfile(Request $request)
    {
        try
        { 
            $user = $request->user();
            $user_id = $user->id;
            $user = $this->users->where('id', $user_id)->first();
            $nestedData['company_name'] = isset($user['companies']['company_name']) ? $user['companies']['company_name'] :'';
            $nestedData['name'] = isset($user['name']) ? $user['name'] :'';
            $nestedData['first_name'] = isset($user['first_name']) ? $user['first_name'] :'';
            $nestedData['last_name'] = isset($user['last_name']) ? $user['last_name'] :'';
            $nestedData['email'] = isset($user['email']) ? $user['email'] :'';
            $nestedData['mobile'] = isset($user['mobile']) ? $user['mobile'] :'';
            $nestedData['profile_image'] = isset($user['profile_image']) ? $user['profile_image'] :'';
            $nestedData['gender'] = isset($user['gender']) ? $user['gender'] :'';
            $nestedData['region_id'] = isset($user['region_id']) ? $user['region_id'] :'';
            return response()->json(['status' => 'success','userinfo' => $nestedData ], $this->successStatus); 
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }   
    }
    public function updateProfile(Request $request)
    {
        try
        { 
            $user = $request->user();
            $request['user_id'] = $user->id;
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'user_'.$request['user_id'];
                $request['profile_image'] = fileupload($image, $this->path, $filename);
            }
            $users =  $this->users->where('id',$request['user_id'])->first();
            if($request['profile_image'])
            {
                $users->profile_image = $request['profile_image'];
            }
            if($users->save())
            {
                $response['profile_image'] = $this->users->where('id',$request['user_id'])->pluck('profile_image')->first();
                return response()->json($response, $this->successStatus); 
            }
            return response()->json($response, $this->badrequest); 
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }
    }

    public function logout(Request $request)
    {
        try
        { 
            $user = $request->user();
            if($request->user()->token()->revoke())
            {
                $this->users->where('id',$user->id)->update([
                    'notification_id' => ""
                ]);
                $user['provider'] = 'users';
                $this->usersLogin->logout($user);
                return response()->json(['status' => 'success','message' => 'Logout Successfully' ], $this->successStatus);  
            }
            return response()->json(['status' => 'error','message' => 'Error in Logout' ], $this->badrequest);
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }
    }

}
