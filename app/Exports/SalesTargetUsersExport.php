<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesTargetUsers;
use App\Models\User;
use DB;

class SalesTargetUsersExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{

    public function __construct($request)
    {    
        $this->user_id = $request->input('user_id');
        $this->month = $request->input('month');
        $this->year = $request->input('year');
        $this->target = $request->input('target');  
      
    }
    public function collection()
    {
        $data = SalesTargetUsers::select(
            'user_id',
            'month',
            'year',
            'target',
            'achievement',
            'achievement_percent'
        );
        if($this->user_id && $this->user_id != null && $this->user_id != ''){
            $user_name = User::where('id', $this->user_id)->value('name');
            $data['user'] = $user_name;
        }
      
        $data = $data->groupBy('user_id', 'month','year','target','achievement',
            'achievement_percent')->get();
        return $data;
    }

    public function headings(): array
    {
        return ['Employee Code','User Name','Branch Name','Month','Year','Target Amount','Achievement','achievement Percent'];
    }

    public function map($data): array
    {
        return [
            $data['user']['employee_codes'],
            $data['user']['name'],
            $data['user']['getbranch']['branch_name'],
            $data['month'],
            $data['year'],
            $data['target'],
            $data['achievement'],
            $data['achievement']
        ];
    }

}
