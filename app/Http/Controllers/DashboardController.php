<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Customers, Order, CheckIn, BeatSchedule, Sales, SalesTarget, OrderDetails, TourProgramme, Wallet, Product, UserActivity, UserCityAssign, Address, TourDetail};
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;
use Validator;
use Gate;

class DashboardController extends Controller
{
        public function __construct() 
        {     

                $this->DAILY_VISIT_TARGET = 15;
        }
    public function index(Request $request)
    {
        //abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users= User::where('active','=','Y')->select('id','name')->get();
        return view('dashboard.index',compact('users'));
    }

    public function dashboardData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $yearStartDate = date('Y-m-d',strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d',strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d",strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate ;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate ;
        $month_calendar = collect([]);
        $year_calendar = collect([]);

        for($i = 1; $i <=  date('t'); $i++)
        {
           $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for($i = 1; $i <= 12 ; $i++)
        {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0'.$i : $i ]);
        }
        /*============= Query Customers ====================*/
        $totalcustomers = Customers::where(function($query) use($users) {
                            $query->whereIn('created_by', $users);
                        })
                        ->select('customertype')->get();              
        /*============ Query CheckIn ===============*/
        $datacheckin = CheckIn::with('customers')->where(function($query) use($query_start_date , $query_end_date,$users) {
                                $query->where('checkin_date', '>=', $query_start_date);
                                $query->where('checkin_date', '<=', $query_end_date);
                                $query->whereIn('user_id', $users);
                            })
                            ->select('customer_id','checkin_date','user_id')->get();
        /*============= Query Orders ====================*/
        $dataorders = Order::with('buyers','orderdetails')->where(function($query) use($query_start_date , $query_end_date, $users) {
                                $query->where('order_date', '>=', $query_start_date);
                                $query->where('order_date', '<=', $query_end_date);
                                $query->whereIn('created_by', $users);
                            })
                            ->select('id','grand_total','order_date','buyer_id','seller_id','created_by')
                            ->get();
        $datatours = TourProgramme::where(function($query) use($query_start_date , $query_end_date, $users) {
                                        $query->where('date', '>=', $query_start_date);
                                        $query->where('date', '<=', $query_end_date);
                                        $query->whereIn('userid', $users);
                                })
                                ->whereIn('type',['Tour', 'Central Market', 'Suburban'])
                                ->select('userid','date')->get();
        /*========= KPIs Counts ==============*/
        $tours = $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate);
        $uniquetours = $tours->unique('date')->all();
        $customercount = 0;
        foreach ($uniquetours as $key => $tourdate) {
            $tourcount = $tours->where('date','=', $tourdate['date'])->count();
            if($tourcount >= 1)
            {
                $customercount = $customercount + ($tourcount * $this->DAILY_VISIT_TARGET);
            }
        }
        $data['visittarget'] = $customercount;
        // Visited Counts
        $uniquecheckin = $datacheckin->where('checkin_date', '>=', $fromdate)->where('checkin_date', '<=', $todate);
        $getuniquecheckin = $uniquecheckin->unique('customer_id', 'checkin_date')->values()->all();
        $data['visitedcounter'] = count($getuniquecheckin);
        // Beat Adherance Percentage
        $data['beatadherance'] = ($data['visittarget'] >= 1) ? number_format((float)(($data['visitedcounter'] * 100) / $data['visittarget']), 2, '.', '') : 0;
        // Active Buyer Count
        $data['activebuyercount'] = $dataorders->where('order_date', '>=', $fromdate)
                                                ->where('order_date', '<=', $todate)
                                                ->unique('buyer_id')
                                                ->count();
        // Beat Productivity 
        $data['beatproductivity'] = ($data['activebuyercount'] >= 1) ? number_format((float)($data['activebuyercount'] / $data['visitedcounter']), 2, '.', '') : 0;
        //Active Dealers Count 
        $data['activedealerscount'] = $dataorders->where('order_date', '>=', $activeStartDate)
                                                ->where('buyers.customertype', '=', 2)
                                                ->unique('buyer_id')
                                                ->count();
        // Active Stockist Count
        $data['activeStockistcount'] = $dataorders->where('order_date', '>=', $activeStartDate)
                                                ->where('buyers.customertype', '=', 3)
                                                ->unique('buyer_id')
                                                ->count();
        //Active Fleet Owner Count
        $data['activeFleetOwnercount'] = $dataorders->where('order_date', '>=', $activeStartDate)
                                                ->where('buyers.customertype', '=', 6)
                                                ->unique('buyer_id')
                                                ->count();
        //Active Mechanics Count
        $data['activeMechanicscount'] = Wallet::whereDate('transaction_at', '>=', $activeStartDate)
                                                ->whereHas('customers', function($query) {
                                                    $query->where('customertype', '=', 6);
                                                })
                                                ->groupBy('customer_id')
                                                ->count();

        $data['totaldealerscount'] = $totalcustomers->where('customertype', '=', 2)->count();
        // Active Stockist Count
        $data['totalStockistcount'] = $totalcustomers->where('customertype', '=', 3)->count();
        //Active Fleet Owner Count
        $data['totalFleetOwnercount'] = $totalcustomers->where('customertype', '=', 6)->count();
        //Total Mechanics
        $data['totalMechanicscount'] = $totalcustomers->where('customertype', '=', 4)->count();
        /*============== Monthly Beat Adherence Data ==============*/
        $data['monthbeatAdherence'] = $month_calendar->map(function ($item, $key) use($datacheckin, $dataorders, $datatours) {
                $tourcount = $datatours->where('date','=',$item['date'])->count();
                $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;
                $item['counter_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                                                ->unique('customer_id','checkin_date')
                                                ->count();
                $item['productive_counter'] = $dataorders->where('order_date','=',$item['date'])->unique('buyer_id')->count();
                $item['date'] = date('d',strtotime($item['date']));
                unset($item['beatcustomers']);
                return $item;
        });
        /*============== Yearly Beat Adherence Data ==============*/
        $data['yearbeatAdherence'] = $year_calendar->map(function ($item, $key) use($datacheckin, $dataorders, $datatours) {
            $month_start_date = date('Y-m-d', strtotime($item['year'].'-'.$item['month'].'-'.'01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'].'-'.$item['month'].'-'.'31'));
            $tourcount = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->count();
            $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;
            $monthscheckins = $datacheckin->where('checkin_date', '>=', $month_start_date)
                                                ->where('checkin_date', '<=', $month_end_date);

            $getuniquecheckin = $monthscheckins->unique('customer_id', 'checkin_date')->values()->all();
            $item['counter_visited'] = count($getuniquecheckin);
            $item['productive_counter'] = $dataorders->where('order_date', '>=', $month_start_date)
                                                ->where('order_date', '<=', $month_end_date)
                                                ->unique('buyer_id','order_date')
                                                ->count();
            return $item;
        });

        return response()->json($data);
    }

    public function travelSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d',strtotime(date("Y-m-01")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $yearStartDate = date('Y-m-d',strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d',strtotime(date("Y-12-31")));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate ;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate ;
        $year_calendar = collect([]);
        for($i = 1; $i <= 12 ; $i++)
        {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0'.$i : $i ]);
        }

        /*========== Query Tour =======================*/
        $datatours =  TourProgramme::where(function($query) use($query_start_date , $query_end_date, $users) {
                                $query->where('date', '>=', $query_start_date);
                                $query->where('date', '<=', $query_end_date);
                                $query->whereIn('userid', $users);
                            })
                            ->select('id','userid','date', 'type')
                            ->orderBy('date','asc')->get();

        $datatourdetails = TourDetail::with('tourinfo')->whereHas('tourinfo', function($query) use($query_start_date , $query_end_date, $users) {
                                $query->where('date', '>=', $query_start_date);
                                $query->where('date', '<=', $query_end_date);
                                $query->whereIn('userid', $users);
                            })
                            ->select('tourid','visited_cityid')->get();
        // Days Toured Count
        $data['daystouredcount'] = $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type','=','Tour')->count();
        // Days Office Work Count 
        $data['daysOfficeWorkCount'] = $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type','=','Office Work')->count();
        // Days Central Market Count 
        $data['daysCentralMarketcount'] =  $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type','=','Central Market')->count();
        // Days Suburban Count
        $data['daysSuburbancount'] =  $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type','=','Suburban')->count();
        // Cities Covered Count
        $data['citiescoveredcount'] =  $datatourdetails->unique('visited_cityid')->count();
        /*========= Tour Plan ================*/
        
        $data['yeartours'] = $year_calendar->map(function ($item, $key) use($datatours) {
            $month_start_date = date('Y-m-d', strtotime($item['year'].'-'.$item['month'].'-'.'01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'].'-'.$item['month'].'-'.'31'));
            // Days Toured Count
            $item['daystouredcount'] = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type','=','Tour')->count();
            // Days Office Work Count 
            $item['daysOfficeWorkCount'] = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type','=','Office Work')->count();
            // Days Central Market Count 
            $item['daysCentralMarketcount'] =  $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type','=','Central Market')->count();
            // Days Suburban Count
            $item['daysSuburbancount'] =  $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type','=','Suburban')->count();
            return $item;
        });
        return response()->json($data);

    }
    public function visitSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d',strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d',strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d",strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate ;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate ;
        $month_calendar = collect([]);
        $year_calendar = collect([]);
        for($i = 1; $i <=  date('t'); $i++)
        {
           $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for($i = 1; $i <= 12 ; $i++)
        {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0'.$i : $i ]);
        }
        /*============ Query BeatShedule ===============*/
        $databeatschedule = BeatSchedule::with('beatcustomers','beatcustomers.customers')
                                    ->where(function($query) use($query_start_date , $query_end_date, $users) {
                                        $query->where('beat_date', '>=', $query_start_date);
                                        $query->where('beat_date', '<=', $query_end_date);
                                        $query->whereIn('user_id', $users);
                                    })
                                    ->select('id','beat_id','beat_date')->get();
        /*============= Query Customers ====================*/
        $datacustomers = Customers::with('beatdetails')->where(function($query) use($query_start_date , $query_end_date, $users) {
                            $query->where('created_at', '>=', $query_start_date);
                            $query->where('created_at', '<=', $query_end_date);
                            $query->whereIn('created_by', $users);
                        })
                        ->select('id','customertype','created_at')->get();
        /*============ Query CheckIn ===============*/
        $datacheckin = CheckIn::with('customers')->where(function($query) use($query_start_date , $query_end_date,$users) {
                                $query->where('checkin_date', '>=', $query_start_date);
                                $query->where('checkin_date', '<=', $query_end_date);
                                $query->whereIn('user_id', $users);
                            })
                            ->select('customer_id','checkin_date','user_id')->get();
        /*============= Query Orders ====================*/
        $dataorders = Order::with('buyers','orderdetails')->where(function($query) use($query_start_date , $query_end_date, $users) {
                                $query->where('order_date', '>=', $query_start_date);
                                $query->where('order_date', '<=', $query_end_date);
                                $query->whereIn('created_by', $users);
                            })
                            ->select('id','grand_total','order_date','buyer_id','seller_id','created_by')
                            ->get();
        $datatours = TourProgramme::where(function($query) use($query_start_date , $query_end_date, $users) {
                                        $query->where('date', '>=', $query_start_date);
                                        $query->where('date', '<=', $query_end_date);
                                        $query->whereIn('userid', $users);
                                })
                                ->where('type','=','Tour')
                                ->select('userid','date')->get();
        // Customer Registerd Count                 
        $data['newSTUsregisteredcount'] =  Customers::whereDate('created_at', '>=', $fromdate)
                                                        ->whereDate('created_at', '<=', $todate)
                                                        ->whereIn('created_by', $users)
                                                        ->where('customertype','=','5')
                                                        ->count();
        $data['newFleetOwnercount'] =  Customers::whereDate('created_at', '>=', $fromdate)
                                                        ->whereDate('created_at', '<=', $todate)
                                                        ->whereIn('created_by', $users)
                                                        ->where('customertype','=','6')
                                                        ->count();
        $data['newMechanicregisteredcount'] =  Customers::whereDate('created_at', '>=', $fromdate)
                                                        ->whereDate('created_at', '<=', $todate)
                                                        ->whereIn('created_by', $users)
                                                        ->where('customertype','=',4)
                                                        ->count();
        $data['newDealerRegisteredcount'] =  Customers::whereDate('created_at', '>=', $fromdate)
                                                        ->whereDate('created_at', '<=', $todate)
                                                        ->whereIn('created_by', $users)
                                                        ->where('customertype','=','2')
                                                        ->count();
        // Customer Visited Count  
        $data['visitedstuscount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
                                                ->where('checkin_date', '<=', $todate)
                                                ->where('customers.customertype','=',5)
                                                ->unique('customer_id','checkin_date')
                                                ->count();
        $data['visitedFleetOwnercount']= $datacheckin->where('checkin_date', '>=', $fromdate)
                                                ->where('checkin_date', '<=', $todate)
                                                ->where('customers.customertype','=',6)
                                                ->unique('customer_id','checkin_date')
                                                ->count();
        $data['visitedMechanicCount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
                                                ->where('checkin_date', '<=', $todate)
                                                ->where('customers.customertype','=',4)
                                                ->unique('customer_id','checkin_date')
                                                ->count();
        $data['visitedDealerCount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
                                                ->where('checkin_date', '<=', $todate)
                                                ->where('customers.customertype','=',2)
                                                ->unique('customer_id','checkin_date')
                                                ->count();

        /*========= Counter Data ================*/
        $data['month_created_data'] = $month_calendar->map(function ($item, $key) use($datacustomers, $users) {
                $item['dealer_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                                                        ->whereIn('created_by', $users)
                                                        ->where('customertype','=','2')
                                                        ->count();
                $item['stus_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                                                        ->whereIn('created_by', $users)
                                                        ->where('customertype','=','5')
                                                        ->count();
                $item['mechanic_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                                                        ->whereIn('created_by', $users)
                                                        ->where('customertype','=','4')
                                                        ->count();
                $item['fleet_owner_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                                                        ->whereIn('created_by', $users)
                                                        ->where('customertype','=','6')
                                                        ->count();
                $item['label'] = date('d',strtotime($item['date']));
            return $item ;
        });
        $data['year_created_data'] = $year_calendar->map(function ($item, $key) use($datacustomers, $users) {
                $month_start_date = date('Y-m-d', strtotime($item['year'].'-'.$item['month'].'-'.'01'));
                $month_end_date = date('Y-m-d', strtotime($item['year'].'-'.$item['month'].'-'.'31'));

            $item['dealer_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                                                    ->whereDate('created_at', '<=', $month_end_date)
                                                    ->whereIn('created_by', $users)
                                                    ->where('customertype','=','2')
                                                    ->count();
                $item['stus_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                                                    ->whereDate('created_at', '<=', $month_end_date)
                                                    ->whereIn('created_by', $users)
                                                    ->where('customertype','=','5')
                                                    ->count();
                $item['mechanic_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                                                    ->whereDate('created_at', '<=', $month_end_date)
                                                    ->whereIn('created_by', $users)
                                                    ->where('customertype','=','4')
                                                    ->count();
                $item['fleet_owner_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                                                    ->whereDate('created_at', '<=', $month_end_date)
                                                    ->whereIn('created_by', $users)
                                                    ->where('customertype','=','6')
                                                    ->count();
            return $item ;
        });
        /*============== Beat Adherence Data ==============*/
        $data['monthbeatAdherence'] = $month_calendar->map(function ($item, $key) use($databeatschedule,$datacheckin, $dataorders, $datacustomers, $datatours) {
                $tourcount = $datatours->where('date','=',$item['date'])->count();
                $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;
                $item['counter_visited'] = $datacheckin->where('checkin_date','=',$item['date'])
                                                        ->unique('customer_id','checkin_date')->count();
                $item['dealer_visited'] = $datacheckin->where('checkin_date','=',$item['date'])
                                                    ->where('customers.customertype','=',2)
                                                    ->unique('customer_id','checkin_date')
                                                    ->count();
                $item['stus_visited'] = $datacheckin->where('checkin_date','=',$item['date'])
                                                    ->where('customers.customertype','=',5)
                                                    ->unique('customer_id','checkin_date')
                                                    ->count();
                $item['mechanic_visited'] = $datacheckin->where('checkin_date','=',$item['date'])
                                                    ->where('customers.customertype','=',4)
                                                    ->unique('customer_id','checkin_date')
                                                    ->count();
                $item['fleet_owner_visited'] = $datacheckin->where('checkin_date','=',$item['date'])
                                                    ->where('customers.customertype','=',6)
                                                    ->unique('customer_id','checkin_date')
                                                    ->count();
                $item['productive_counter'] = $dataorders->where('order_date','=',$item['date'])
                                                        ->unique('buyer_id','order_date')->count();
                $item['date'] = date('d',strtotime($item['date']));
                unset($item['beatcustomers']);
                return $item;
        });

        $data['yearbeatAdherence'] = $year_calendar->map(function ($item, $key) use($databeatschedule,$datacheckin, $dataorders, $datacustomers, $datatours) {
                $month_start_date = date('Y-m-d', strtotime($item['year'].'-'.$item['month'].'-'.'01'));
                $month_end_date = date('Y-m-d', strtotime($item['year'].'-'.$item['month'].'-'.'31'));
            $tourcount = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->count();
            $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;

            $item['counter_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                                                ->where('checkin_date', '<=', $month_end_date)
                                                ->unique('checkin_date','customer_id')
                                                ->count();
            $item['productive_counter'] = $dataorders->where('order_date', '>=', $month_start_date)
                                                ->where('order_date', '<=', $month_end_date)
                                                ->unique('order_date','buyer_id')
                                                ->count();
            $item['dealer_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                                                ->where('checkin_date', '<=', $month_end_date)
                                                ->where('customers.customertype','=',2)
                                                ->unique('checkin_date','customer_id')
                                                ->count();
            $item['stus_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                                                ->where('checkin_date', '<=', $month_end_date)
                                                ->where('customers.customertype','=',5)
                                                ->unique('checkin_date','customer_id')
                                                ->count();
            $item['mechanic_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                                                ->where('checkin_date', '<=', $month_end_date)
                                                ->where('customers.customertype','=',4)
                                                ->unique('checkin_date','customer_id')
                                                ->count();
            $item['fleet_owner_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                                                ->where('checkin_date', '<=', $month_end_date)
                                                ->where('customers.customertype','=',6)
                                                ->unique('checkin_date','customer_id')
                                                ->count();
            return $item;
        });
        return response()->json($data);
        
    }
    public function couponSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d',strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d',strtotime(date("Y-12-31")));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate ;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate ;
        /*=========== Query Wallet ======================*/
        $datawallets = Wallet::where(function($query) use($query_start_date , $query_end_date, $users) {
                        $query->where('transaction_at', '>=', $query_start_date);
                        $query->where('transaction_at', '<=', $query_end_date);
                        $query->whereIn('userid', $users);
                    })
                    ->select('point_type','points','quantity','transaction_at','transaction_type')->get();
        /*=========== Points Counts ======================*/
        $data['couponsCollectedValue'] =  $datawallets->where('point_type','=','coupon')
                                                    ->where('transaction_at', '>=', $fromdate)
                                                    ->where('transaction_at', '<=', $todate)
                                                    ->sum('quantity');
        $data['couponsCollectedPoints'] =  $datawallets->where('point_type','=','coupon')
                                                    ->where('transaction_at', '>=', $fromdate)
                                                    ->where('transaction_at', '<=', $todate)
                                                    ->sum('points');
        $data['mrpCollectedValue'] =  $datawallets->where('point_type','=','mrp')
                                                    ->where('transaction_at', '>=', $fromdate)
                                                    ->where('transaction_at', '<=', $todate)
                                                    ->sum('quantity');
        $data['mrpCollectedPoints'] =  $datawallets->where('point_type','=','mrp')
                                                    ->where('transaction_at', '>=', $fromdate)
                                                    ->where('transaction_at', '<=', $todate)
                                                    ->sum('points');
        /*========= Coupons Summary ==========*/
        $monthsumpoints = $datawallets->where('transaction_at', '>=', $monthStartDate)
                                    ->where('transaction_at', '<=', $monthEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->sum('points');
        $monthsumquantity = $datawallets->where('transaction_at', '>=', $monthStartDate)
                                        ->where('transaction_at', '<=', $monthEndDate)
                                        ->where('transaction_type','=','Cr')
                                        ->sum('quantity');
        $yearsumpoints = $datawallets->where('transaction_at', '>=', $yearStartDate)
                                    ->where('transaction_at', '<=', $yearEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->sum('points');
        $yearsumquantity = $datawallets->where('transaction_at', '>=', $yearStartDate)
                                    ->where('transaction_at', '<=', $yearEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->sum('quantity');
        $month_coupon_point = $datawallets->where('transaction_at', '>=', $monthStartDate)
                                    ->where('transaction_at', '<=', $monthEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->where('point_type','=','coupon')
                                    ->sum('points');
        $month_mrp_point = $datawallets->where('transaction_at', '>=', $monthStartDate)
                                    ->where('transaction_at', '<=', $monthEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->where('point_type','=','mrp')
                                    ->sum('points');
        $month_coupon_quantity = $datawallets->where('transaction_at', '>=', $monthStartDate)
                                    ->where('transaction_at', '<=', $monthEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->where('point_type','=','coupon')
                                    ->sum('quantity');
        $month_mrp_quantity = $datawallets->where('transaction_at', '>=', $monthStartDate)
                                    ->where('transaction_at', '<=', $monthEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->where('point_type','=','mrp')
                                    ->sum('points');
        $year_coupon_point = $datawallets->where('transaction_at', '>=', $yearStartDate)
                                    ->where('transaction_at', '<=', $yearEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->where('point_type','=','coupon')
                                    ->sum('points');
        $year_mrp_point = $datawallets->where('transaction_at', '>=', $yearStartDate)
                                    ->where('transaction_at', '<=', $yearEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->where('point_type','=','mrp')
                                    ->sum('points');
        $year_coupon_quantity = $datawallets->where('transaction_at', '>=', $yearStartDate)
                                    ->where('transaction_at', '<=', $yearEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->where('point_type','=','coupon')
                                    ->sum('quantity');
        $year_mrp_quantity = $datawallets->where('transaction_at', '>=', $yearStartDate)
                                    ->where('transaction_at', '<=', $yearEndDate)
                                    ->where('transaction_type','=','Cr')
                                    ->where('point_type','=','mrp')
                                    ->sum('quantity');
        $data['coupon_summary_chart'] = collect([
            'month_coupon_point' => ($monthsumpoints >= 1 && $month_coupon_point >= 1) ? round(($month_coupon_point * 100)/$monthsumpoints) : 0,
            'month_mrp_point' => ($monthsumpoints >= 1 && $month_mrp_point >= 1) ? round(($month_mrp_point * 100)/ $monthsumpoints ) : 0,
            'month_coupon_quantity' => ($monthsumquantity >= 1 && $month_coupon_quantity >= 1) ? round( ($month_coupon_quantity * 100) / $monthsumquantity ) : 0,
            'month_mrp_quantity' => ($monthsumquantity >= 1 && $month_mrp_quantity >= 1) ? round( ($month_mrp_quantity * 100) / $monthsumquantity ) : 0,
            'year_coupon_point' => ($yearsumpoints >= 1 && $year_coupon_point >= 1) ? round( ($year_coupon_point * 100) / $yearsumpoints ) : 0,
            'year_mrp_point' => ($yearsumpoints >= 1 && $year_mrp_point >= 1) ? round(($year_mrp_point* 100) / $yearsumpoints ): 0,
            'year_coupon_quantity' => ($yearsumquantity >= 1 && $year_coupon_quantity >= 1) ? round( ($year_coupon_quantity * 100) / $yearsumquantity ) : 0,
            'year_mrp_quantity' => ($yearsumquantity >= 1 && $year_mrp_quantity >= 1) ? round( ($year_mrp_quantity * 100) / $yearsumquantity) : 0,
        ]);
        return response()->json($data);
        
    }
    public function orderSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d',strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d',strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d",strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate ;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate ;
        $month_calendar = collect([]);
        $year_calendar = collect([]);
        for($i = 1; $i <=  date('t'); $i++)
        {
           $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for($i = 1; $i <= 12 ; $i++)
        {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0'.$i : $i ]);
        }
        /*============= Query Orders ====================*/
        $dataorders = Order::with('buyers','orderdetails')->where(function($query) use($query_start_date , $query_end_date, $users) {
                                $query->where('order_date', '>=', $query_start_date);
                                $query->where('order_date', '<=', $query_end_date);
                                $query->whereIn('created_by', $users);
                            })
                            ->select('id','grand_total','order_date','buyer_id','seller_id','created_by')->get();
        $datatargets = SalesTarget::where(function($query) use($query_start_date , $query_end_date, $users) {
                $query->where('startdate', '>=', $query_start_date);
                $query->where('enddate', '<=', $query_end_date);
                $query->whereIn('userid', $users);
            })
            ->select('startdate','enddate','amount')->get();
         /*============= Query Sales ====================*/
        $datasales = Sales::where(function($query) use($query_start_date , $query_end_date, $users) {
                                $query->where('invoice_date', '>=', $query_start_date);
                                $query->where('invoice_date', '<=', $query_end_date);
                                $query->whereIn('created_by', $users);
                            })
                            ->select('id','grand_total','invoice_date','buyer_id','seller_id','created_by')->get();
        /*============= Orders Count ====================*/
        $data['orderCollectedCount'] = $dataorders->where('order_date', '>=', $fromdate)
                                                ->where('order_date', '<=', $todate)
                                                ->count();
        $data['orderCollectedSum'] = $dataorders->where('order_date', '>=', $fromdate)
                                                ->where('order_date', '<=', $todate)
                                                ->sum('grand_total');
        $ggplids = Product::where('category_id','=','1')->pluck('id')->toArray();
        $gpdids = Product::where('category_id','=','2')->pluck('id')->toArray();
        $gdplids = Product::where('category_id','=','3')->pluck('id')->toArray();
        $data['GGPLorderCollectedCount'] = $dataorders->where('order_date', '>=', $fromdate)
                                                ->where('order_date', '<=', $todate)
                                                ->whereIn('orderdetails.product_id', $ggplids)
                                                ->count();
        $data['GGPLorderCollectedSum'] = $dataorders->where('order_date', '>=', $fromdate)
                                                ->where('order_date', '<=', $todate)
                                                ->whereIn('orderdetails.product_id', $ggplids)
                                                ->sum('grand_total');
        $data['GPDorderCollectedCount'] = $dataorders->where('order_date', '>=', $fromdate)
                                                ->where('order_date', '<=', $todate)
                                                ->whereIn('orderdetails.product_id', $gpdids)
                                                ->count();
        $data['GPDorderCollectedSum'] = $dataorders->where('order_date', '>=', $fromdate)
                                                ->where('order_date', '<=', $todate)
                                                ->whereIn('orderdetails.product_id', $gpdids)
                                                ->sum('grand_total');
        $data['GDGLorderCollectedCount'] = $dataorders->where('order_date', '>=', $fromdate)
                                                ->where('order_date', '<=', $todate)
                                                ->whereIn('orderdetails.product_id', $gdplids)
                                                ->count();
        $data['GDGLorderCollectedSum'] = $dataorders->where('order_date', '>=', $fromdate)
                                                ->where('order_date', '<=', $todate)
                                                ->whereIn('orderdetails.product_id', $gdplids)
                                                ->sum('grand_total');
        /*========= Order Summary =============*/
        $data['top_products'] = OrderDetails::with('products')
                                ->whereHas('orders', function ($query) {
                                    $query->whereYear('order_date', '=', date('Y'));
                                })
                                ->select(['product_id',DB::raw("SUM(price) as total_price"), DB::raw("SUM(quantity) as total_quantity")])
                                ->groupBy('product_id')
                                ->orderBy('total_price')
                                ->limit(10)
                                ->get();
        $data['yeartargetachievement'] = $year_calendar->map(function ($item, $key) use($dataorders, $datasales, $datatargets) {
                $month_start_date = date('Y-m-d', strtotime($item['year'].'-'.$item['month'].'-'.'01'));
                $month_end_date = date('Y-m-d', strtotime($item['year'].'-'.$item['month'].'-'.'31'));
                $item['achievement'] = $dataorders->where('order_date', '>=', $month_start_date)
                                                ->where('order_date', '<=', $month_end_date)
                                                ->sum('grand_total');
                $item['salesachievement'] = $datasales->where('invoice_date', '>=', $month_start_date)
                                                    ->where('invoice_date', '<=', $month_end_date)
                                                    ->sum('grand_total');
                $item['target'] = $datatargets->where('startdate', '>=', $month_start_date)
                                            ->where('startdate', '<=', $month_end_date)
                                            ->where('enddate', '>=', $month_start_date)
                                            ->where('enddate', '<=', $month_end_date)
                                            ->sum('amount');
            return $item ;
        });
        $data['top_representatives'] = Order::with('createdbyname')
                                        ->whereYear('order_date', '=', date('Y'))
                                        ->select(['created_by',DB::raw("SUM(grand_total) as total_amount"), DB::raw("SUM(total_qty) as total_quantity")])
                                        ->groupBy('created_by')
                                        ->orderBy('total_amount')
                                        ->limit(10)
                                        ->get();
        $userscities = UserCityAssign::with('userinfo')->select('userid')->groupBy('userid')->get();
        $data['orders_by_zone'] = $userscities->map(function ($item, $key) {
            $item['zone_name'] =  $item['userinfo']['location'];
            $citiesids = UserCityAssign::where('userid','=',$item['userid'])->pluck('city_id')->toArray();

            $item['order_amount'] = Order::whereHas('customeraddress', function ($query) use($citiesids) {
                                        $query->whereYear('order_date', '=', date('Y'));
                                        $query->whereIn('city_id', $citiesids);
                                    })->sum('grand_total');
            $item['sales_amount'] = Sales::whereHas('customeraddress', function ($query) use($citiesids) {
                                    $query->whereYear('invoice_date', '=', date('Y'));
                                    $query->whereIn('city_id', $citiesids);
                            })->sum('grand_total');
            return $item;
        }); 
        $statesname = Address::with('statename')->whereNotNull('customer_id')->whereNotNull('state_id')->select('state_id')->groupBy('state_id')->get();
        $data['orders_by_state'] = $statesname->map(function ($item, $key) {
            $item['state_name'] =  $item['statename']['state_name'];
            $item['order_amount'] = Order::whereHas('customeraddress', function ($query) use($item) {
                                         $query->whereYear('order_date', '=', date('Y'));
                                        $query->where('state_id', $item['state_id']);
                                    })->sum('grand_total');
            $item['sales_amount'] = Sales::whereHas('customeraddress', function ($query) use($item) {
                                        $query->whereYear('invoice_date', '=', date('Y'));
                                        $query->where('state_id', $item['state_id']);
                                    })->sum('grand_total');
            unset($item['statename']);
            return $item;
        }); 
        return response()->json($data);
        
    }
    public function salesSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d',strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d',strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d",strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate ;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate ;
        $month_calendar = collect([]);
        $year_calendar = collect([]);
        for($i = 1; $i <=  date('t'); $i++)
        {
           $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for($i = 1; $i <= 12 ; $i++)
        {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0'.$i : $i ]);
        }
        /*=========== Query SalesTarget ======================*/
        $datatargets = SalesTarget::where(function($query) use($query_start_date , $query_end_date, $users) {
                        $query->where('startdate', '>=', $query_start_date);
                        $query->where('enddate', '<=', $query_end_date);
                        $query->whereIn('userid', $users);
                    })
                    ->select('startdate','enddate','amount');
        $data['top_sales_representatives'] = Sales::with('createdbyname')
                                        ->whereYear('invoice_date', '=', date('Y'))
                                        ->select(['created_by',DB::raw("SUM(grand_total) as total_amount"), DB::raw("SUM(total_qty) as total_quantity")])
                                        ->groupBy('created_by')
                                        ->orderBy('total_amount')
                                        ->limit(10)
                                        ->get();
        return response()->json($data);
        
    }
    public function activityDashboardCount(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d',strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $users = getUsersReportingToAuth($userid);
        /*========= User Activities ==============*/
        $data['activities'] = UserActivity::with('customers','users')
                                ->where(function($query) use($fromdate , $todate, $users) {
                                    if(!empty($fromdate))
                                    {
                                        $query->whereDate('time', '>=', date('Y-m-d',strtotime($fromdate)));
                                    }
                                    if(!empty($todate))
                                    {
                                        $query->whereDate('time', '<=', date('Y-m-d',strtotime($todate)));
                                    }
                                    $query->whereIn('userid', $users);
                                })
                                ->select('id','userid','customerid','address','description','type','time')
                                ->latest()
                                ->get();

        return response()->json($data);
        
    }
}
