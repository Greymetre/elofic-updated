<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class UserExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return User::with('reportinginfo')->where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('id', $this->userids);
                                }
                            })->select('id','name', 'first_name', 'last_name', 'mobile', 'email', 'gender', 'profile_image','reportingid')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','name', 'first_name', 'last_name', 'mobile', 'email', 'gender', 'profile_image', 'role', 'Reporting'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['name'],
            $data['first_name'],
            $data['last_name'],
            $data['mobile'],
            $data['email'],
            $data['gender'],
            $data['profile_image'],
            isset($data['roles']['name']) ? $data['roles']['name'] :'',
            isset($data['reportinginfo']['name']) ? $data['reportinginfo']['name'] :''
        ];
    }
}
