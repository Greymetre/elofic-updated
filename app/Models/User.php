<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
          'active','name', 'first_name', 'last_name', 'mobile', 'email', 'email_verified_at', 'password', 'notification_id', 'device_type', 'gender', 'profile_image', 'latitude', 'longitude', 'region_id', 'remember_token', 'deleted_at', 'created_at', 'updated_at','location' , 'reportingid','branch_id','designation_id','employee_codes','department_id','division_id' 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guard_name = 'users';

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function userinfo()
    {
        return $this->belongsTo('App\Models\UserDetails','id','user_id');
    }

    public function userbeats()
    {
        return $this->hasMany('App\Models\BeatUser','user_id','id')->select('beat_id','user_id');
    } 

    public function cities()
    {
        return $this->hasMany('App\Models\UserCityAssign','userid','id')->select('city_id','userid');
    }

    public function reportinginfo()
    {
        return $this->belongsTo('App\Models\User', 'reportingid', 'id')->select('id','name');
    }

    public function getbranch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    // public function getdepartment()
    // {
    //     return $this->belongsTo('App\Models\Division', 'department_id', 'id')->select('id','division_name');
    // }

    public function getdivision()
    {
        return $this->belongsTo('App\Models\Division', 'division_id', 'id')->select('id','division_name');
    }

    public function getdepartment()
    {
        return $this->belongsTo('App\Models\Department', 'department_id', 'id')->select('id','name');
    }


    public function getdesignation()
    {
        return $this->belongsTo('App\Models\Designation', 'designation_id', 'id')->select('id','designation_name'); 
    }

    public function geteducation()
    {
        return $this->hasMany(UserEducation::class);
    }

    public static function tree($user_id){
        $all_user = User::get();
        $root_users = User::where('id', $user_id)->get();
        self::formatTree($root_users, $all_user);

        return $root_users;
    }

    private static function formatTree($root_users, $all_user){
        foreach ($root_users as $root_user) {
            $root_user->children = $all_user->where('reportingid', $root_user->id)->values();
            if($root_user->children->isNotEmpty()){
                self::formatTree($root_user->children, $all_user);
            }
        }
    }

    
}
