<?php

namespace App\Exports;

use App\Models\CheckIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class CheckinExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        
        $this->userids = getUsersReportingToAuth();
    }
    public function collection()
    {
        return CheckIn::with('beatschedules','customers','users','orders','visitreports','visitreports.visittypename')->where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('user_id', $this->userids);
                                }
                                if($this->startdate)
                                {
                                    $query->whereDate('checkin_date','>=',$this->startdate);
                                }
                                if($this->enddate)
                                {
                                    $query->whereDate('checkin_date','<=',$this->enddate);
                                }
                            })
                            ->select('id','customer_id', 'user_id', 'checkin_date', 'checkin_time', 'checkout_date', 'checkout_time', 'beatscheduleid','created_at','checkin_address','checkout_address','distance')
                            ->latest()->limit(5000)->get();   
    }

    public function headings(): array
    {
        return ['id','User ID', 'User Name','Checkin Date', 'Checkin Time','Checkout Time', 'Spend Time', 'Beat Name', 'Customer Id', 'Customer Name', 'Customer Mobile','District','City','Address','Existing','Order Qty','Order Value','Unique Orders','Visit Remark', 'Report Title', 'Visit Type', 'Checkin Address' , 'Checkout Address' , 'Distance'];
    }

    public function map($data): array
    {
        $interval = strtotime($data->checkout_time) - strtotime($data->checkin_time) ;
        return [
            $data['id'],
            isset($data['user_id']) ? $data['user_id'] :'',
            isset($data['users']['name']) ? $data['users']['name'] :'',
            isset($data['checkin_date']) ? $data['checkin_date'] :'',
            isset($data['checkin_time']) ? $data['checkin_time'] :'',
            isset($data['checkout_time']) ? $data['checkout_time'] :'',
            date("H:i:s",($interval)),
            isset($data['beatschedules']['beats']['beat_name']) ? $data['beatschedules']['beats']['beat_name'] :'',
            isset($data['customer_id']) ? $data['customer_id'] :'',
            isset($data['customers']['name']) ? $data['customers']['name'] :'',
            isset($data['customers']['mobile']) ? $data['customers']['mobile'] :'',
            isset($data['customers']['customeraddress']['districtname']['district_name']) ? $data['customers']['customeraddress']['districtname']['district_name'] :'',
            isset($data['customers']['customeraddress']['cityname']['city_name']) ? $data['customers']['customeraddress']['cityname']['city_name'] :'',
            isset($data['customers']['customeraddress']['address1']) ? $data['customers']['customeraddress']['address1'].' '.$data['customers']['customeraddress']['address2'] :'',
            (date("Y-m-d", strtotime($data['customers']['created_at'])) == date("Y-m-d", strtotime($data['checkin_date']))) ? 'New' : 'Existing', 
            (!empty($data['orders'])) ? $data['orders']->sum('total_qty') : 0,
            (!empty($data['orders'])) ? $data['orders']->sum('grand_total') : 0,
            (!empty($data['orders'])) ? $data['orders']->count() : 0,
            isset($data['visitreports']['description']) ? $data['visitreports']['description'] :'',
            isset($data['visitreports']['report_title']) ? $data['visitreports']['report_title'] :'',
            isset($data['visitreports']['visittypename']['type_name']) ? $data['visitreports']['visittypename']['type_name'] :'',
            isset($data['checkin_address']) ? $data['checkin_address'] :'',
            isset($data['checkout_address']) ? $data['checkout_address'] :'',
            isset($data['distance']) ? $data['distance'] :'',
        ];
    }

}