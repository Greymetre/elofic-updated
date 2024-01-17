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

class UserTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return User::select('id','name', 'first_name', 'last_name', 'mobile', 'email', 'password','gender', 'profile_image')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['id','name', 'first_name', 'last_name', 'mobile', 'email', 'password','gender','profile_image','role','ctc','last_year_increments','last_promotion'];
    }

}
