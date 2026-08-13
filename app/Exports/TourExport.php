<?php

    namespace App\Exports;

    use App\Models\TourProgramme;
    use App\Models\User;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\ShouldAutoSize;
    use Maatwebsite\Excel\Concerns\WithEvents;
    use Maatwebsite\Excel\Events\AfterSheet;
    use Maatwebsite\Excel\Concerns\WithMapping;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    


    class TourExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
    {
        protected $debugData = [];
        public function __construct($request)
        {
            
            $this->userids = getUsersReportingToAuth();

            $this->user_id = $request->input('executive_id');
            $this->division_id = $request->input('division_id');
            $this->start_date = $request->input('start_date');
            $this->end_date = $request->input('end_date');

        }

        public function collection()
        {     
            // return TourProgramme::with('tourdetails','userinfo')->where(function ($query)  {
            //                         if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

            //                         {
            //                             $query->whereIn('executive_id', $this->userids);
            //                         }
            //                     })->select('id','date', 'userid', 'town', 'objectives', 'type', 'status')->latest()->get();

            if(!empty($this->user_id) ||!empty($this->start_date) ||!empty($this->end_date)){
                return TourProgramme::with('tourdetails', 'userinfo', 'attendance')->where(function ($query)  {
                                    if($this->user_id)
                                    {
                                        $query->where('userid', $this->user_id);
                                    }
                                    if($this->division_id)
                                    {
                                        $userIds = User::where('division_id', $this->division_id)->pluck('id');
                                        $query->whereIn('userid', $userIds);
                                    }
                                    if($this->start_date)
                                    {
                                        $query->whereDate('date','>=',$this->start_date);
                                    }
                                    if($this->end_date)
                                    {
                                        $query->whereDate('date','<=',$this->end_date);
                                    }
                                })
                            ->select('id','date', 'userid', 'town','district', 'objectives', 'type', 'status')
                            //->latest()->get(); 
                            ->orderBy(DB::raw('YEAR(date)'), 'DESC')->orderBy(DB::raw('DATE(date)'), 'ASC')->get();    

            }else{

                return TourProgramme::with('tourdetails', 'userinfo', 'attendance')->where(function ($query)  {
                                    if(!empty($this->division_id)){
                                        $userIds = User::where('division_id', $request['division_id'])->pluck('id');
                                        $query->whereIn('executive_id', $userIds);
                                    }elseif(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                    {
                                        $query->whereIn('executive_id', $this->userids);
                                    }
                                })->select('id','date', 'userid', 'town','district', 'objectives', 'type', 'status')
                            //->latest()->get();
                            ->orderBy(DB::raw('YEAR(date)'), 'DESC')->orderBy(DB::raw('DATE(date)'), 'ASC')->get();   

            }



        }

        public function headings(): array
        {
            return ['Date',
            // 'id',
            'Employee Code',
            // 'userid',
            'Username',
            'Designation',
            'District', 'Town','objectives','Zone','Approval Status','Reporting Manager','Based location','Actual','Dif','Distance from Base Location (KM)',
            //  'type', 'city_id',  'last_visited','Division', 'Actual',
            ];
        }

        public function registerEvents(): array
        {
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $lastRow = $event->sheet->getHighestRow();

                    $event->sheet->getStyle("N2:N{$lastRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                },
            ];
        }

        public function map($data): array
        {
            $cityname = '';
            $cityid = '';
            $visited_date = '';
            $visited_cityid = '';
            $visited_cityname = '';
            $last_visited = '';
            if($data->status == '0'){
                $status = "Pending";
            }elseif($data->status == '1'){
                $status = "Approved";
            }else{
                $status = "Rejected";
            }
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

            $reportingManagerName = '—';

        if (!empty($data->userinfo?->reportingid)) {
            $manager = User::select('name')
                ->where('id', $data->userinfo->reportingid)
                ->first();
                
            $reportingManagerName = $manager?->name ?? '—';
        }
        
        

//         $baseCity = '';

// if($data->userinfo && $data->userinfo->latitude && $data->userinfo->longitude){
//     $baseCity = getLatLongToCity(
//         $data->userinfo->latitude,
//         $data->userinfo->longitude
//     );
// }

// $baseCity = 'Not set';
// if(!empty($data->userinfo?->latitude) && !empty($data->userinfo?->longitude)){
//     $baseCity = getLatLongToCity($data->userinfo->latitude, $data->userinfo->longitude);
// }




// $attendance = \App\Models\Attendance::where('user_id', $data->userid)
//     ->whereDate(
//         'punchin_date',
//         Carbon::parse($data->date)->format('Y-m-d')
//     )
//     ->first();

// $actualCity = '';

// dd($attendance);

// if($attendance && $attendance->punchin_latitude && $attendance->punchin_longitude){
//     $actualCity = getLatLongToCity(

//         $attendance->punchin_latitude,
//         $attendance->punchin_longitude
//     );
// }

// dd($actualCity);

$baseCity = '';
$actualCity = '';
$distance = null;

if(!empty($data->tourdetails) && count($data->tourdetails) > 0){

    $tourDetail = $data->tourdetails->first();

    $baseCity = $tourDetail->base_city ?? '';

    $actualCity = $tourDetail->punchin_city ?? '';

}

$baseLatitude = $data->userinfo?->latitude;
$baseLongitude = $data->userinfo?->longitude;
$actualLatitude = $data->attendance?->punchin_latitude;
$actualLongitude = $data->attendance?->punchin_longitude;

if (
    is_numeric($baseLatitude) && is_numeric($baseLongitude) &&
    is_numeric($actualLatitude) && is_numeric($actualLongitude) &&
    $baseLatitude >= -90 && $baseLatitude <= 90 &&
    $actualLatitude >= -90 && $actualLatitude <= 90 &&
    $baseLongitude >= -180 && $baseLongitude <= 180 &&
    $actualLongitude >= -180 && $actualLongitude <= 180
) {
    $distance = getRoadDistance(
        (float) $baseLatitude,
        (float) $baseLongitude,
        (float) $actualLatitude,
        (float) $actualLongitude
    );

    if (!is_numeric($distance)) {
        $distance = round(haversineGreatCircleDistance(
            (float) $baseLatitude,
            (float) $baseLongitude,
            (float) $actualLatitude,
            (float) $actualLongitude
        ), 2);
    }
}


$this->debugData[] = [
    'tour_id' => $data->id,
    'base_city' => $baseCity,
    'actual_city' => $actualCity,
    'distance' => $distance,
];


// $distance = null; // numeric
// if(!empty($attendance)){
//     if(!empty($attendance->punchin_latitude) && !empty($attendance->punchin_longitude)){
//         $actualCity = getLatLongToCity($attendance->punchin_latitude, $attendance->punchin_longitude);

//         if(!empty($data->userinfo->latitude) && !empty($data->userinfo->longitude)){
//             $distance = getRoadDistance(
//                 $data->userinfo->latitude,
//                 $data->userinfo->longitude,
//                 $attendance->punchin_latitude,
//                 $attendance->punchin_longitude
//             ); // just number, no 'KM'
//         }
//     }
// }

// dd([
//     'user_base_lat' => $data->userinfo->latitude,
//     'user_base_long' => $data->userinfo->longitude,
//     'punchin_lat' => $attendance->punchin_latitude ?? 'Not set',
//     'punchin_long' => $attendance->punchin_longitude ?? 'Not set'
// ]);
        
            return [
                isset($data['date']) ? date("d-m-Y", strtotime($data['date'])) :'',
                // $data['id'],
                isset($data['userinfo']['employee_codes']) ? $data['userinfo']['employee_codes'] :'',

                // isset($data['userid']) ? $data['userid'] :'',
                isset($data['userinfo']['name']) ? $data['userinfo']['name'] : '',
                isset($data['userinfo']['getdesignation']['designation_name']) ? $data['userinfo']['getdesignation']['designation_name'] :'',
                
                $data->districtRelation?->district_name ?? $data->districtRelation ?? '',           // District name
                $data->cityRelation?->city_name ?? $data->town ?? '',
                // isset($visited_cityname) ? $visited_cityname :'',
                isset($data['objectives']) ? $data['objectives'] :'',
                isset($data['userinfo']['getbranch']['branch_name']) ? $data['userinfo']['getbranch']['branch_name'] :'',
                $status,
    $reportingManagerName,
    // ($data->userinfo && $data->userinfo->latitude && $data->userinfo->longitude)
    //         ? number_format($data->userinfo->latitude, 4) . ', ' . number_format($data->userinfo->longitude, 4)
    //         : 'Not set',
                // $baseCity, // Base City
                $data->userinfo->location,
                $actualCity,
                
                (
                    empty($data->userinfo->location) &&
                    empty($actualCity)
                )
                ? '-'
                : (
                    trim(strtolower($data->userinfo->location)) ==
                    trim(strtolower($actualCity))
                        ? 'Match'
                        : 'Miss Match'
                ),
                $distance,
                // $visited_date,
                // $visited_cityid,
                // $last_visited,
                
                isset($data['userinfo']['getdepartment']['division_name']) ? $data['userinfo']['getdepartment']['division_name'] :'',
                
            ];
        }

    }
