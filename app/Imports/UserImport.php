<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\UserDetails;
use App\Models\Address;
use App\Models\Pincode;
use Spatie\Permission\Models\Role;

class UserImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new User([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        $userdetails = collect([]);
        $addressdetails = collect([]);
        foreach ($rows as $row) {
            $name = trim($row['name']);
            $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $first_name = trim( preg_replace('#'.preg_quote($last_name,'#').'#', '', $name ) );
            if( $user = User::create([
                'active' => 'Y',
                'name' => !empty($name)? ucfirst(strtolower($name)):'',
                'first_name' => !empty($first_name)? ucfirst(strtolower($first_name)):'',
                'last_name' => !empty($last_name)? ucfirst(strtolower($last_name)):'',
                'mobile' => $row['mobile'],
                'email' => !empty($row['email'])? $row['email']:null,
                'password' => !empty($row['password'])? Hash::make($row['password']) :'',
                'gender' => !empty($row['gender'])? $row['gender']:'',
                'profile_image' => !empty($row['profile_image'])? $row['profile_image']:'',
                'user_code' => !empty($row['user_code'])? $row['user_code']:'',
                'location' => !empty($row['Location'])? $row['Location']:'',
                'created_at' => getcurentDateTime() ,
                'updated_at' => getcurentDateTime()
            ]) )
            {
                $roles = Role::where('name','=',$row['role'])->pluck('id')->toArray();
                $user->roles()->sync($roles);
                $permissions = $user->getPermissionsViaRoles()->pluck('name');
                $user->givePermissionTo($permissions);
                $userdetails->push([
                    'user_id' => $user['id'],
                    'created_at' => getcurentDateTime() ,
                    'updated_at' => getcurentDateTime()
                ]);

               //$pincode = Pincode::where('pincode','=',$row['pincode_id'])->select('id','city_id')->first();
               $addressdetails->push([
                    'active' => 'Y',
                    'user_id' => $user['id'],
                    'address1' => !empty($row['address1'])? $row['address1']:'',
                    'address2' => !empty($row['address2'])? $row['address2']:'',
                    'landmark' => !empty($row['landmark'])? $row['landmark']:'',
                    'locality' => !empty($row['locality'])? $row['locality']:'',
                    'country_id' => !empty($row['country_id'])? $row['country_id']:null,
                    'state_id' => !empty($row['state_id'])? $row['state_id']:null,
                    // 'district_id' => !empty($city['district_id'])? $city['district_id']:null,
                    // 'city_id' => !empty($pincode['city_id'])? $pincode['city_id']:null,
                    // 'pincode_id' => !empty($pincode['id'])? $pincode['id']:null,
                    'created_by' => Auth::user()->id,
                    'created_at' => getcurentDateTime() ,
                    'updated_at' => getcurentDateTime()
                ]);
           }
        }
        if($userdetails->isNotEmpty())
        {
            UserDetails::insert($userdetails->toArray());
        }
        if($addressdetails->isNotEmpty())
        {
            Address::insert($addressdetails->toArray());
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function onFailure(Failure ...$failures)
    {
        Log::stack(['import-failure-logs'])->info(json_encode($failures));
    }
}
