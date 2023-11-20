<?php

namespace App\Exports;

use App\Models\TourProgramme;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class TourExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return TourProgramme::with('tourdetails','userinfo')->where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('executive_id', $this->userids);
                                }
                            })->select('id','date', 'userid', 'town', 'objectives', 'type', 'status')->latest()->get();   
    }

    public function headings(): array
    {
        return ['date','id','userid','username', 'town','Actual','objectives', 'type', 'city_id', 'visited_date','visited_cityid', 'last_visited'];
    }

    public function map($data): array
    {
        $cityname = '';
        $cityid = '';
        $visited_date = '';
        $visited_cityid = '';
        $visited_cityname = '';
        $last_visited = '';
        if(!empty($data['tourdetails']))
        {
            foreach ($data['tourdetails'] as $key => $detail) {

                $rowcityname = isset($detail['cityname']['city_name']) ? $detail['cityname']['city_name'].' , ' : '';
                $rowcityid = isset($detail['city_id']) ? $detail['city_id'].' , ' : '';
                $rowvisited_date = isset($detail['visited_date']) ? $detail['visited_date'].' , ' : '';
                $rowvisited_cityid = isset($detail['visited_cityid']) ? $detail['visited_cityid'].' , ' : '';
                $rowvisited_cityname = isset($detail['visitedcities']['city_name']) ? $detail['visitedcities']['city_name'].' , ' : '';
                $rowlast_visited = isset($detail['last_visited']) ? $detail['last_visited'].' , ' : '';
                $cityname = $cityname.' '.$rowcityname;
                $cityid = $cityid.' '.$rowcityid;
                $visited_date = $visited_date.' '.$rowvisited_date;
                $visited_cityid = $visited_cityid.' '.$rowvisited_cityid;
                $visited_cityname = $visited_cityname.' '.$rowvisited_cityname;
                $last_visited = $last_visited.' '.$rowlast_visited;
            }
        }
        return [
            isset($data['date']) ? date("d-m-Y", strtotime($data['date'])) :'',
            $data['id'],
            isset($data['userid']) ? $data['userid'] :'',
            isset($data['userinfo']['name']) ? $data['userinfo']['name'] : '',
            isset($data['town']) ? $data['town'] :'',
            isset($visited_cityname) ? $visited_cityname :'',
            isset($data['objectives']) ? $data['objectives'] :'',
            isset($data['type']) ? $data['type'] :'',
            $cityid,
            $visited_date,
            $visited_cityid,
            $last_visited,
        ];
    }

}