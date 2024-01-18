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
use App\Models\User;
use App\Models\Attendance;
use App\Models\BeatSchedule;
use App\Models\BeatCustomer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Sales;
use App\Models\CheckIn;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\SalesTarget;
use App\Models\Customers;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->users = new User();
        
        
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }

    public function dashboard(Request $request)
    {
        try
        { 
            $user_id = $request->user()->id;
            $filter_date = $request->input('filter_date');
            $fromdate = isset($request->fromdate) ? date('Y-m-d',strtotime($request->fromdate)) : date('Y-m-d');
            $todate = isset($request->todate) ? date('Y-m-d',strtotime($request->todate)) :date('Y-m-d');
            $year = (date('m') > 3) ? date("Y",strtotime("+ 1 year")) : date('Y');
            $lastyear = (date('m') < 4) ? date("Y",strtotime("- 1 year")) : date('Y');
            switch ($filter_date) {
                case 'Today':
                    $fromdate = date('Y-m-d') ;
                    $todate = date('Y-m-d') ;
                    break;
                case 'This Month':
                    $fromdate = date('Y-m-01');
                    $todate = date('Y-m-t');
                    break;
                case 'Last Month':
                    $fromdate = date('Y-m-d',strtotime('first day of last month'));
                    $todate = date('Y-m-t',strtotime('last month'));
                    break;
                case 'Quarter 1':
                    $fromdate = date('Y-m-d',strtotime(date('Y').'-04-01'));
                    $todate = date('Y-m-d',strtotime(date('Y').'-06-30'));
                    break;
                case 'Quarter 2':
                    $fromdate = date('Y-m-d',strtotime(date('Y').'-07-01'));
                    $todate = date('Y-m-d',strtotime(date('Y').'-09-30'));
                    break;
                case 'Quarter 3':
                    $fromdate = date('Y-m-d',strtotime(date('Y').'-10-01'));
                    $todate = date('Y-m-d',strtotime(date('Y').'-12-31'));
                    break;
                case 'Quarter 4':
                    $fromdate = date('Y-m-d',strtotime($year.'-01-01'));
                    $todate = date('Y-m-d',strtotime($year.'-03-31'));
                    break;
                case 'YTM':
                    $fromdate = date('Y-m-d',strtotime($lastyear.'-04-01'));
                    $todate = date('Y-m-t',strtotime('last month'));
                    break;
                case 'Last Year':
                    $fromdate = date('Y-m-d',strtotime($lastyear.'-04-01'));
                    $todate = date('Y-m-d',strtotime($year.'-03-31'));
                    break;
                default:
                    $fromdate = date('Y-m-d');
                    $todate = date('Y-m-d');
                    break;
            }
            $punchin = Attendance::where('user_id',$user_id)->whereDate('punchin_date',getcurentDate())->select('punchin_time','punchout_time')->first();
            $orders = Order::where(function ($query) use($user_id, $fromdate, $todate){
                                $query->where('created_by', '=', $user_id);
                                if(!empty($fromdate) && !empty($todate))
                                {
                                    $query->whereDate('order_date','>=', $fromdate);
                                    $query->whereDate('order_date','<=', $todate);
                                    //$query->whereBetween('order_date', [$fromdate, $todate]);
                                }
                            })
                            ->select('grand_total','id','buyer_id','beatscheduleid')->get();
   
            $achievement = !empty($orders) ? $orders->sum('grand_total') : 0 ;
            $target = SalesTarget::where('userid','=',$user_id)
                                    ->whereYear('startdate', '=', date('Y', strtotime($fromdate)))
                                    ->whereMonth('startdate', '=', date('m', strtotime($fromdate)))
                                    ->sum('amount');
            
            $sales = Sales::where(function ($query) use($user_id, $fromdate, $todate){
                                    $query->where('created_by', '=', $user_id);
                                    if(!empty($fromdate) && !empty($todate))
                                    {
                                        $query->whereDate('invoice_date','>=', $fromdate);
                                        $query->whereDate('invoice_date','<=', $todate);
                                        //$query->whereBetween('invoice_date', [$fromdate, $todate]);
                                    }
                                })->select('grand_total','order_id','id')->get();
            $sales_amount = !empty($sales) ? $sales->sum('grand_total') : 0 ;
            
            $beatschedule = BeatSchedule::with('beatcustomers')->where(function($query) use($fromdate , $todate, $user_id) {
                if(!empty($fromdate) && !empty($todate))
                {
                    $query->whereDate('beat_date','>=', $fromdate);
                    $query->whereDate('beat_date','<=', $todate);
                    //$query->whereBetween('beat_date', [$fromdate, $todate]);
                }
                $query->where('user_id','=',$user_id);
            })
            ->select('id','beat_id')
            ->get();
            $total_beat_counter = 0;
            $total_visited_counter = 0;
            if(!empty($beatschedule))
            {
                foreach ($beatschedule as $key => $counter) {
                    $total_visited_counter += $counter['beatcheckininfo']->unique('customer_id','checkin_date')->count();
                    $total_beat_counter += count($counter['beatcustomers']);
                }
            }
            //Assign Counter
            $assign_counter = Customers::where('executive_id','=',$user_id)->count();
            $new_added_counter = Customers::where(function ($query) use($fromdate , $todate, $user_id) {
                if(!empty($fromdate) && !empty($todate))
                {
                    $query->whereDate('created_at','>=', $fromdate);
                    $query->whereDate('created_at','<=', $todate);

                    //$query->whereBetween('created_at', [$fromdate, $todate]);
                }
                $query->where('created_by','=',$user_id);
            })->count();
            $active_counter = $orders->unique('buyer_id')->count();
            // Payment 
            $collectionamount = PaymentDetail::whereIn('sales_id',$sales->pluck('id'))->sum('amount');
            //Working Days
            $workings = Attendance::where(function ($query) use($user_id, $fromdate, $todate){
                                        $query->where('user_id', '=', $user_id);
                                        $query->where('working_type', '=', 'fields');
                                        if(!empty($fromdate) && !empty($todate))
                                        {
                                            $query->whereDate('punchin_date','>=', $fromdate);
                                            $query->whereDate('punchin_date','<=', $todate);
                                            //$query->whereBetween('punchin_date', [$fromdate, $todate]);
                                        }
                                    })
                                    ->select('id','worked_time')->get();
            
            $attendances = $workings->map(function ($item){
                $days = 0;
                switch ($item->worked_time) {
                    case (date('H',strtotime($item->worked_time))  >= 4 && date('H',strtotime($item->worked_time)) < 7):
                        $days = 0.5;
                        break;
                    case (date('H',strtotime($item->worked_time)) >= 7):
                        $days = 1;
                        break;
                    default:
                        break;
                }
                $item['working_days'] = $days ;
                return $item;
            });
            $avgsales = ($achievement > 0 && $attendances->sum('working_days') > 0) ? $achievement/ $attendances->sum('working_days') : 0;
            $data = collect([
                'punchin' => (!empty($punchin['punchin_time'])) ? true : false,
                'punchout' => (!empty($punchin['punchout_time'])) ? true : false,
                'buyer' => 'Retailer',
                'seller' => 'Distributor',
                'totalcounter' => $total_beat_counter,
                'visitcounter' => $total_visited_counter,
                'adherence' => ($total_beat_counter >= 1) ? number_format((float)($total_visited_counter *100)/$total_beat_counter, 1, '.', '').' %'  : '',
                'productive_counter' => (string)$active_counter,
                'productivity' => ($total_visited_counter >= 1) ? number_format((float)($orders->unique('buyer_id','beatscheduleid')->count() *100)/$total_visited_counter, 1, '.', '').' %'  : '',
                'target_amount' => amountConversion($target),
                'achievement_amount' => amountConversion($achievement),
                'achievement_percent' => ($target >= 1)? ($achievement * 100)/$target : 0,
                'target' => $target,
                'achievement' => $achievement,
                'orders_count' => $orders->count(),
                'outstanding_amount' => (string)($sales_amount - $collectionamount),
                'new_added_counter' => $new_added_counter,
                'active_counter_percent' => ($active_counter >= 1 && $assign_counter >= 1)? number_format((float)($active_counter *100)/$assign_counter, 1, '.', '').' %'  : '',
                'uniquesku_qty' => 0,
                'sales_amount' => amountConversion($sales_amount),
                'average_sales' => amountConversion($avgsales),
                'collection_amount'=> amountConversion($collectionamount),
            ]);
            return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data ],200);              
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }        
    }
}

