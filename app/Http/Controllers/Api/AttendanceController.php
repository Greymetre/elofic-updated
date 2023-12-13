<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Validator;
use Gate;
use App\Models\Attendance;
use App\Models\TourProgramme;
use App\Models\BeatSchedule;
use App\Models\Beat;
use App\Models\TourDetail;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->attendances = new Attendance();
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
        $this->path = 'attendances';
    }

    public function getPunchin(Request $request)
    {
        try
        { 
            $user = $request->user();
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $query = $this->attendances->where(function ($query) use($user_id) {
                                        $query->where('user_id', '=', $user_id);
                                    })
                                    ->select('id','punchin_date', 'punchin_time', 'punchin_longitude', 'punchin_latitude', 'punchin_address', 'punchin_image', 'punchout_date', 'punchout_time', 'punchout_latitude', 'punchout_longitude', 'punchout_address', 'punchout_image')->orderBy('punchin_date', 'desc');
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if($db_data->isNotEmpty())
            {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'punchin_id' => !empty($value['id']) ? $value['id'] : 0,
                        'punchin_date' => !empty($value['punchin_date']) ? $value['punchin_date'] : '',
                        'punchin_time' => !empty($value['punchin_time']) ? $value['punchin_time'] : '',
                        'punchin_longitude' => !empty($value['punchin_longitude']) ? $value['punchin_longitude'] : '',
                        'punchin_latitude' => !empty($value['punchin_latitude']) ? $value['punchin_latitude'] : '',
                        'punchin_address' => !empty($value['punchin_address']) ? $value['punchin_address'] : '',
                        'punchin_image' => !empty($value['punchin_image']) ? $value['punchin_image'] : '',
                        'punchout_date' => !empty($value['punchout_date']) ? $value['punchout_date'] : '',
                        'punchout_time' => !empty($value['punchout_time']) ? $value['punchout_time'] : '',
                        'punchout_latitude' => !empty($value['punchout_latitude']) ? $value['punchout_latitude'] : '',
                        'punchout_longitude' => !empty($value['punchout_longitude']) ? $value['punchout_longitude'] : '',
                        'punchout_address' => !empty($value['punchout_address']) ? $value['punchout_address'] : '',
                        'punchout_image' => !empty($value['punchout_image']) ? $value['punchout_image'] : '',
                    ]);
                }
                return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);  
            
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }

    public function userPunchin(Request $request)
    {
        try
        { 
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'punchin_latitude' => 'required',
                'punchin_longitude' => 'required',
            ]); 
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }
            if($request->file('image')){
                $image = $request->file('image');
                // $filename = 'punchin_'.autoIncrementId('Attendance', 'id');
                $filename = 'punchin';
                $request['punchin_image'] = fileupload($image, $this->path, $filename);
            }
            $punchin_date = getcurentDate();
             // $request['punchin_address'] = getLatLongToAddress($request['punchin_latitude'],$request['punchin_longitude']);
             $request['punchin_address'] = getLatLongToAddress($request['punchin_longitude'],$request['punchin_latitude']);
             //$request['punchin_address'] = '';
            if($punchin = $this->attendances->updateOrCreate([
                'user_id' => $user->id, 'punchin_date' => $punchin_date],[
                'active' => 'Y',
                'user_id' => $user->id,
                'punchin_date' => $punchin_date,
                'punchin_time' => getcurentTime(),
                // 'punchin_longitude' => !empty($request['punchin_longitude']) ? $request['punchin_longitude'] :'',
                // 'punchin_latitude' => !empty($request['punchin_latitude']) ? $request['punchin_latitude'] :'',
                'punchin_longitude' => !empty($request['punchin_latitude']) ? $request['punchin_latitude'] :'',
                'punchin_latitude' => !empty($request['punchin_longitude']) ? $request['punchin_longitude'] :'',
                'punchin_address' => !empty($request['punchin_address']) ? $request['punchin_address'] :'',
                'punchin_image' => !empty($request['punchin_image']) ? $request['punchin_image'] :'',
                'punchin_summary' => !empty($request['punchin_summary']) ? $request['punchin_summary'] :'',
                'working_type' => !empty($request['type']) ? $request['type'] :'',
                'created_at' => getcurentDateTime(),
            ]))
            {
                $punchindata = $this->attendances->where('id',$punchin->id)->select('active','user_id','punchin_date','punchin_time','punchin_longitude','punchin_latitude','punchin_address','punchin_image')->first();
                // $useractivity = array(
                //         'userid' => $user->id, 
                //         'latitude' => $request['punchin_latitude'], 
                //         'longitude' => $request['punchin_longitude'], 
                //         'type' => 'Punchin',
                //         'description' => 'User Login',
                //     );
                // submitUserActivity($useractivity);
                if(!empty($request['beats']))
                {
                    $collection = array();
                    $beats = explode(',', $request['beats']);
                    if(!empty($beats))
                    {
                        foreach ($beats as $key => $beat) {            
                            array_push($collection,array(
                                "user_id" => $user->id, 
                                'beat_id' => $beat, 
                                'tourid' => $request['tourid'],
                                'beat_date' => date('Y-m-d'), 
                                'created_at' => date('Y-m-d H:i:s') ));
                        }
                        BeatSchedule::insert($collection);
                    }
                }
                if(!empty($request['tourid']))
                {
                    TourProgramme::where('id','=',$request['tourid'])->update([
                        'type' => !empty($request['type']) ? $request['type'] : ''
                    ]);
                    
                    $cityids = Beat::whereHas('beatschedules', function ($query) use($user){
                                $query->where('user_id', '=', $user->id);
                                $query->whereDate('beat_date', '=', date('Y-m-d'));
                            })
                            ->orderBy('city_id','asc')
                            ->pluck('city_id');
                    $cityids = $cityids->unique();
                    
                    foreach ($cityids as $key => $city) {
                        $updatecity = TourDetail::where('tourid','=',$request['tourid'])->whereNull('visited_cityid')->first();
                        if(!empty($updatecity))
                        {
                            $updatecity->update([
                                'visited_cityid' => $city
                            ]);
                        }
                        else
                        {
                            TourDetail::create([
                                'tourid' => $request['tourid'],
                                'city_id' => null, 
                                'visited_cityid' => $city
                            ]); 
                        }
                    }
                }
                // $zsmnotify = collect([
                //     'title' => 'Successfully punched in',
                //     'body' =>  $user->name.' has Punched in'
                // ]);
                // sendNotification($user->reportingid,$zsmnotify);
                // $asmnotify = collect([
                //     'title' => 'Successfully punched in',
                //     'body' =>  'You have successfully Punched in'
                // ]);
                // sendNotification($user->id,$asmnotify);
                return response()->json(['status' => 'success','message' => 'Punch In successfully','punchin_id' => $punchin->id,'punchin' => $punchindata ], $this->successStatus); 
            }
            return response()->json(['status' => 'error','message' => 'Error in Check In' ], $this->badrequest);
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }

    public function userPunchout(Request $request)
    {
        try
        { 

            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'punchin_id' => 'required|exists:attendances,id',
                'punchout_longitude' => 'required',
                'punchout_latitude' => 'required',
            ]); 
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'punchout_'.$request['punchin_id'];
                $request['punchout_image'] = fileupload($image, $this->path, $filename);
            }
            $request['punchout_address'] = getLatLongToAddress($request['punchout_latitude'], $request['punchout_longitude']);
            $punchout = Attendance::where('id',$request->punchin_id)->where('user_id',$user->id)->first();
            $punchout->punchout_date = getcurentDate() ; 
            $punchout->punchout_time = getcurentTime() ;
            $punchout->punchout_latitude = !empty($request['punchout_latitude']) ? $request['punchout_latitude'] :null; 
            $punchout->punchout_longitude = !empty($request['punchout_longitude']) ? $request['punchout_longitude'] :null ; 
            $punchout->punchout_address = !empty($request['punchout_address']) ? $request['punchout_address'] :'' ;
            $punchout->punchout_image = !empty($request['punchout_image']) ? $request['punchout_image'] :'' ;
            $punchout->punchout_summary = !empty($request['punchout_summary']) ? $request['punchout_summary'] :'';
            $punchout->worked_time = gmdate("H:i:s", strtotime(getcurentDateTime()) - strtotime($punchout->punchin_date.' '.$punchout->punchin_time) );
            if($punchout->save())
            {
                // $useractivity = array(
                //         'userid' => $user->id, 
                //         'latitude' => $request['punchout_latitude'], 
                //         'longitude' => $request['punchout_longitude'], 
                //         'type' => 'Punchout',
                //         'description' => 'User Logout',
                //     );
                // submitUserActivity($useractivity);
                // $zsmnotify = collect([
                //     'title' => 'Successfully punched out',
                //     'body' =>  $user->name.' has Punched out'
                // ]);
                // sendNotification($user->reportingid,$zsmnotify);
                // $asmnotify = collect([
                //     'title' => 'Successfully punched out',
                //     'body' =>  'You have successfully Punched out'
                // ]);
                // sendNotification($user->id,$asmnotify);
                return response()->json(['status' => 'success','message' => 'Punch Out successfully' ,'punchout' => $punchout], $this->successStatus);
            }
            return response()->json(['status' => 'error','message' => 'Error in Punch Out' ], $this->badrequest);
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }

}
