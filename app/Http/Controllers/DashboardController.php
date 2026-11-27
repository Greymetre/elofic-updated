<?php

namespace App\Http\Controllers;

use App\DataTables\VisitorDataTable;
use App\Exports\PrimarySalesExport;
use Illuminate\Http\Request;
use App\Models\{User, Customers, Order, Branch, Division, CheckIn, BeatSchedule, Sales, SalesTarget, OrderDetails, TourProgramme, Wallet, Product, UserActivity, UserCityAssign, Address, TourDetail, TransactionHistory, EmployeeDetail, SalesTargetUsers, Attendance, DealerPortalSettings, ParentDetail, VisitReport, PrimarySales, Redemption};
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Imports\PrimarySalesImport;
use DataTables;
use Validator;
use Gate;
use Carbon\Carbon;
use App\Exports\PrimarySalesTemplate;
use Excel;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->DAILY_VISIT_TARGET = 15;
    }
    public function index(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN, 'Forbidden,' . PHP_EOL . 'You don\'t have the right permissions. Please contact to the admin.');
        $dashboardZones = Branch::where('active', 'Y')
            ->when($this->eloficScopeBranchIds() !== null, fn ($query) => $query->whereIn('id', $this->eloficScopeBranchIds()))
            ->orderBy('branch_name')
            ->get(['id', 'branch_name']);

        return view('dashboard.index', compact('dashboardZones'));
        $users_ids = getUsersReportingToAuth();
        // $users= User::where('active','=','Y')->whereIn('reportingid', $users_ids)->select('id','name')->get();
        $branches = Branch::latest()->get();
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);
        $retailers = Customers::where('customertype', '2')->get();
        $sales_persons = User::latest()->get();
        $uniqueProductsNewGroup = Product::latest()->get()->unique('new_group');
        $dealers_and_distibutors = Customers::where('customertype', [3, 4])->get();
        $users = User::latest()->get();
        $products = Product::latest()->get()->unique('model_no');

        $ps_branches = PrimarySales::select('final_branch')->distinct()->get();
        $ps_divisions = PrimarySales::select('division')->distinct()->get();
        $ps_months = PrimarySales::select('month')->distinct()->get();
        $ps_dealers = PrimarySales::select('dealer')->distinct()->get();
        $ps_new_group_names = PrimarySales::select('new_group')->distinct()->get();
        $ps_product_models = PrimarySales::select('product_name')->distinct()->get();
        $ps_sales_persons = PrimarySales::select('sales_person')->distinct()->get();
        // dd('sssssssssssss');

        $userData = TransactionHistory::select(
            DB::raw('YEAR(max_date) as year'),
            DB::raw('MONTH(max_date) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->from(DB::raw("(SELECT MAX(created_at) as max_date, YEAR(created_at) as year, MONTH(created_at) as month, customer_id 
                        FROM transaction_histories 
                        GROUP BY year, month, customer_id) as temp_table"))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        // dd($userData)    ;


        $labels = [];
        $data = [];

        foreach ($userData as $user) {
            $month = date('F', mktime(0, 0, 0, $user->month, 1)); // Convert month number to month name
            $labels[] = "$month $user->year";
            $data[] = $user->count;
        }

        $userData2 = TransactionHistory::select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(point) as count'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels2 = [];
        $data2 = [];

        foreach ($userData2 as $user) {
            $month = date('F', mktime(0, 0, 0, $user->month, 1)); // Convert month number to month name
            $labels2[] = "$month $user->year";
            $data2[] = $user->count;
        }

        $userData3 = Redemption::select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(redeem_amount) as count'))
            ->where('status', '!=', '2')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels3 = [];
        $data3 = [];

        foreach ($userData3 as $user) {
            $month = date('F', mktime(0, 0, 0, $user->month, 1)); // Convert month number to month name
            $labels3[] = "$month $user->year";
            $data3[] = $user->count;
        }

        $total_qty = PrimarySales::sum('quantity');
        $total_sale = PrimarySales::sum('net_amount');

        $dealer_poster_setting = DealerPortalSettings::first();

        return view('dashboard.index', compact('users', 'branches', 'divisions', 'years', 'sales_persons', 'retailers', 'dealers_and_distibutors', 'products', 'uniqueProductsNewGroup', 'ps_branches', 'ps_divisions', 'ps_months', 'ps_dealers', 'ps_product_models', 'ps_new_group_names', 'ps_sales_persons', 'labels', 'data', 'labels2', 'data2', 'labels3', 'data3', 'total_qty', 'total_sale', 'dealer_poster_setting'));
    }

    public function dashboardData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);

        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }
        /*============= Query Customers ====================*/
        $totalcustomers = Customers::where(function ($query) use ($users) {
            $query->whereIn('created_by', $users);
        })
            ->select('customertype')->get();
        /*============ Query CheckIn ===============*/
        $datacheckin = CheckIn::with('customers')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('checkin_date', '>=', $query_start_date);
            $query->where('checkin_date', '<=', $query_end_date);
            $query->whereIn('user_id', $users);
        })
            ->select('customer_id', 'checkin_date', 'user_id')->get();
        /*============= Query Orders ====================*/
        $dataorders = Order::with('buyers', 'orderdetails')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('order_date', '>=', $query_start_date);
            $query->where('order_date', '<=', $query_end_date);
            $query->whereIn('created_by', $users);
        })
            ->select('id', 'grand_total', 'order_date', 'buyer_id', 'seller_id', 'created_by')
            ->get();
        $datatours = TourProgramme::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('date', '>=', $query_start_date);
            $query->where('date', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->whereIn('type', ['Tour', 'Central Market', 'Suburban'])
            ->select('userid', 'date')->get();
        /*========= KPIs Counts ==============*/
        $tours = $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate);
        $uniquetours = $tours->unique('date')->all();
        $customercount = 0;
        foreach ($uniquetours as $key => $tourdate) {
            $tourcount = $tours->where('date', '=', $tourdate['date'])->count();
            if ($tourcount >= 1) {
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
            ->whereHas('customers', function ($query) {
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
        $data['monthbeatAdherence'] = $month_calendar->map(function ($item, $key) use ($datacheckin, $dataorders, $datatours) {
            $tourcount = $datatours->where('date', '=', $item['date'])->count();
            $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;
            $item['counter_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->unique('customer_id', 'checkin_date')
                ->count();
            $item['productive_counter'] = $dataorders->where('order_date', '=', $item['date'])->unique('buyer_id')->count();
            $item['date'] = date('d', strtotime($item['date']));
            unset($item['beatcustomers']);
            return $item;
        });
        /*============== Yearly Beat Adherence Data ==============*/
        $data['yearbeatAdherence'] = $year_calendar->map(function ($item, $key) use ($datacheckin, $dataorders, $datatours) {
            $month_start_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '31'));
            $tourcount = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->count();
            $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;
            $monthscheckins = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date);

            $getuniquecheckin = $monthscheckins->unique('customer_id', 'checkin_date')->values()->all();
            $item['counter_visited'] = count($getuniquecheckin);
            $item['productive_counter'] = $dataorders->where('order_date', '>=', $month_start_date)
                ->where('order_date', '<=', $month_end_date)
                ->unique('buyer_id', 'order_date')
                ->count();
            return $item;
        });

        return response()->json($data);
    }

    public function travelSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-01")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $year_calendar = collect([]);
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }

        /*========== Query Tour =======================*/
        $datatours =  TourProgramme::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('date', '>=', $query_start_date);
            $query->where('date', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->select('id', 'userid', 'date', 'type')
            ->orderBy('date', 'asc')->get();

        $datatourdetails = TourDetail::with('tourinfo')->whereHas('tourinfo', function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('date', '>=', $query_start_date);
            $query->where('date', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->select('tourid', 'visited_cityid')->get();
        // Days Toured Count
        $data['daystouredcount'] = $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type', '=', 'Tour')->count();
        // Days Office Work Count 
        $data['daysOfficeWorkCount'] = $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type', '=', 'Office Work')->count();
        // Days Central Market Count 
        $data['daysCentralMarketcount'] =  $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type', '=', 'Central Market')->count();
        // Days Suburban Count
        $data['daysSuburbancount'] =  $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type', '=', 'Suburban')->count();
        // Cities Covered Count
        $data['citiescoveredcount'] =  $datatourdetails->unique('visited_cityid')->count();
        /*========= Tour Plan ================*/

        $data['yeartours'] = $year_calendar->map(function ($item, $key) use ($datatours) {
            $month_start_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '31'));
            // Days Toured Count
            $item['daystouredcount'] = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type', '=', 'Tour')->count();
            // Days Office Work Count 
            $item['daysOfficeWorkCount'] = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type', '=', 'Office Work')->count();
            // Days Central Market Count 
            $item['daysCentralMarketcount'] =  $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type', '=', 'Central Market')->count();
            // Days Suburban Count
            $item['daysSuburbancount'] =  $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type', '=', 'Suburban')->count();
            return $item;
        });
        return response()->json($data);
    }
    public function visitSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);
        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }
        /*============ Query BeatShedule ===============*/
        $databeatschedule = BeatSchedule::with('beatcustomers', 'beatcustomers.customers')
            ->where(function ($query) use ($query_start_date, $query_end_date, $users) {
                $query->where('beat_date', '>=', $query_start_date);
                $query->where('beat_date', '<=', $query_end_date);
                $query->whereIn('user_id', $users);
            })
            ->select('id', 'beat_id', 'beat_date')->get();
        /*============= Query Customers ====================*/
        $datacustomers = Customers::with('beatdetails')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('created_at', '>=', $query_start_date);
            $query->where('created_at', '<=', $query_end_date);
            $query->whereIn('created_by', $users);
        })
            ->select('id', 'customertype', 'created_at')->get();
        /*============ Query CheckIn ===============*/
        $datacheckin = CheckIn::with('customers')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('checkin_date', '>=', $query_start_date);
            $query->where('checkin_date', '<=', $query_end_date);
            $query->whereIn('user_id', $users);
        })
            ->select('customer_id', 'checkin_date', 'user_id')->get();
        /*============= Query Orders ====================*/
        $dataorders = Order::with('buyers', 'orderdetails')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('order_date', '>=', $query_start_date);
            $query->where('order_date', '<=', $query_end_date);
            $query->whereIn('created_by', $users);
        })
            ->select('id', 'grand_total', 'order_date', 'buyer_id', 'seller_id', 'created_by')
            ->get();
        $datatours = TourProgramme::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('date', '>=', $query_start_date);
            $query->where('date', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->where('type', '=', 'Tour')
            ->select('userid', 'date')->get();
        // Customer Registerd Count                 
        $data['newSTUsregisteredcount'] =  Customers::whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)
            ->whereIn('created_by', $users)
            ->where('customertype', '=', '5')
            ->count();
        $data['newFleetOwnercount'] =  Customers::whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)
            ->whereIn('created_by', $users)
            ->where('customertype', '=', '6')
            ->count();
        $data['newMechanicregisteredcount'] =  Customers::whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)
            ->whereIn('created_by', $users)
            ->where('customertype', '=', 4)
            ->count();
        $data['newDealerRegisteredcount'] =  Customers::whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)
            ->whereIn('created_by', $users)
            ->where('customertype', '=', '2')
            ->count();
        // Customer Visited Count  
        $data['visitedstuscount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
            ->where('checkin_date', '<=', $todate)
            ->where('customers.customertype', '=', 5)
            ->unique('customer_id', 'checkin_date')
            ->count();
        $data['visitedFleetOwnercount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
            ->where('checkin_date', '<=', $todate)
            ->where('customers.customertype', '=', 6)
            ->unique('customer_id', 'checkin_date')
            ->count();
        $data['visitedMechanicCount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
            ->where('checkin_date', '<=', $todate)
            ->where('customers.customertype', '=', 4)
            ->unique('customer_id', 'checkin_date')
            ->count();
        $data['visitedDealerCount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
            ->where('checkin_date', '<=', $todate)
            ->where('customers.customertype', '=', 2)
            ->unique('customer_id', 'checkin_date')
            ->count();

        /*========= Counter Data ================*/
        $data['month_created_data'] = $month_calendar->map(function ($item, $key) use ($datacustomers, $users) {
            $item['dealer_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '2')
                ->count();
            $item['stus_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '5')
                ->count();
            $item['mechanic_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '4')
                ->count();
            $item['fleet_owner_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '6')
                ->count();
            $item['label'] = date('d', strtotime($item['date']));
            return $item;
        });
        $data['year_created_data'] = $year_calendar->map(function ($item, $key) use ($datacustomers, $users) {
            $month_start_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '31'));

            $item['dealer_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                ->whereDate('created_at', '<=', $month_end_date)
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '2')
                ->count();
            $item['stus_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                ->whereDate('created_at', '<=', $month_end_date)
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '5')
                ->count();
            $item['mechanic_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                ->whereDate('created_at', '<=', $month_end_date)
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '4')
                ->count();
            $item['fleet_owner_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                ->whereDate('created_at', '<=', $month_end_date)
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '6')
                ->count();
            return $item;
        });
        /*============== Beat Adherence Data ==============*/
        $data['monthbeatAdherence'] = $month_calendar->map(function ($item, $key) use ($databeatschedule, $datacheckin, $dataorders, $datacustomers, $datatours) {
            $tourcount = $datatours->where('date', '=', $item['date'])->count();
            $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;
            $item['counter_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->unique('customer_id', 'checkin_date')->count();
            $item['dealer_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->where('customers.customertype', '=', 2)
                ->unique('customer_id', 'checkin_date')
                ->count();
            $item['stus_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->where('customers.customertype', '=', 5)
                ->unique('customer_id', 'checkin_date')
                ->count();
            $item['mechanic_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->where('customers.customertype', '=', 4)
                ->unique('customer_id', 'checkin_date')
                ->count();
            $item['fleet_owner_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->where('customers.customertype', '=', 6)
                ->unique('customer_id', 'checkin_date')
                ->count();
            $item['productive_counter'] = $dataorders->where('order_date', '=', $item['date'])
                ->unique('buyer_id', 'order_date')->count();
            $item['date'] = date('d', strtotime($item['date']));
            unset($item['beatcustomers']);
            return $item;
        });

        $data['yearbeatAdherence'] = $year_calendar->map(function ($item, $key) use ($databeatschedule, $datacheckin, $dataorders, $datacustomers, $datatours) {
            $month_start_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '31'));
            $tourcount = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->count();
            $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;

            $item['counter_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date)
                ->unique('checkin_date', 'customer_id')
                ->count();
            $item['productive_counter'] = $dataorders->where('order_date', '>=', $month_start_date)
                ->where('order_date', '<=', $month_end_date)
                ->unique('order_date', 'buyer_id')
                ->count();
            $item['dealer_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date)
                ->where('customers.customertype', '=', 2)
                ->unique('checkin_date', 'customer_id')
                ->count();
            $item['stus_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date)
                ->where('customers.customertype', '=', 5)
                ->unique('checkin_date', 'customer_id')
                ->count();
            $item['mechanic_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date)
                ->where('customers.customertype', '=', 4)
                ->unique('checkin_date', 'customer_id')
                ->count();
            $item['fleet_owner_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date)
                ->where('customers.customertype', '=', 6)
                ->unique('checkin_date', 'customer_id')
                ->count();
            return $item;
        });
        return response()->json($data);
    }
    public function couponSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        /*=========== Query Wallet ======================*/
        $datawallets = Wallet::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('transaction_at', '>=', $query_start_date);
            $query->where('transaction_at', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->select('point_type', 'points', 'quantity', 'transaction_at', 'transaction_type')->get();
        /*=========== Points Counts ======================*/
        $data['couponsCollectedValue'] =  $datawallets->where('point_type', '=', 'coupon')
            ->where('transaction_at', '>=', $fromdate)
            ->where('transaction_at', '<=', $todate)
            ->sum('quantity');
        $data['couponsCollectedPoints'] =  $datawallets->where('point_type', '=', 'coupon')
            ->where('transaction_at', '>=', $fromdate)
            ->where('transaction_at', '<=', $todate)
            ->sum('points');
        $data['mrpCollectedValue'] =  $datawallets->where('point_type', '=', 'mrp')
            ->where('transaction_at', '>=', $fromdate)
            ->where('transaction_at', '<=', $todate)
            ->sum('quantity');
        $data['mrpCollectedPoints'] =  $datawallets->where('point_type', '=', 'mrp')
            ->where('transaction_at', '>=', $fromdate)
            ->where('transaction_at', '<=', $todate)
            ->sum('points');
        /*========= Coupons Summary ==========*/
        $monthsumpoints = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->sum('points');
        $monthsumquantity = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->sum('quantity');
        $yearsumpoints = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->sum('points');
        $yearsumquantity = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->sum('quantity');
        $month_coupon_point = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'coupon')
            ->sum('points');
        $month_mrp_point = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'mrp')
            ->sum('points');
        $month_coupon_quantity = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'coupon')
            ->sum('quantity');
        $month_mrp_quantity = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'mrp')
            ->sum('points');
        $year_coupon_point = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'coupon')
            ->sum('points');
        $year_mrp_point = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'mrp')
            ->sum('points');
        $year_coupon_quantity = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'coupon')
            ->sum('quantity');
        $year_mrp_quantity = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'mrp')
            ->sum('quantity');
        $data['coupon_summary_chart'] = collect([
            'month_coupon_point' => ($monthsumpoints >= 1 && $month_coupon_point >= 1) ? round(($month_coupon_point * 100) / $monthsumpoints) : 0,
            'month_mrp_point' => ($monthsumpoints >= 1 && $month_mrp_point >= 1) ? round(($month_mrp_point * 100) / $monthsumpoints) : 0,
            'month_coupon_quantity' => ($monthsumquantity >= 1 && $month_coupon_quantity >= 1) ? round(($month_coupon_quantity * 100) / $monthsumquantity) : 0,
            'month_mrp_quantity' => ($monthsumquantity >= 1 && $month_mrp_quantity >= 1) ? round(($month_mrp_quantity * 100) / $monthsumquantity) : 0,
            'year_coupon_point' => ($yearsumpoints >= 1 && $year_coupon_point >= 1) ? round(($year_coupon_point * 100) / $yearsumpoints) : 0,
            'year_mrp_point' => ($yearsumpoints >= 1 && $year_mrp_point >= 1) ? round(($year_mrp_point * 100) / $yearsumpoints) : 0,
            'year_coupon_quantity' => ($yearsumquantity >= 1 && $year_coupon_quantity >= 1) ? round(($year_coupon_quantity * 100) / $yearsumquantity) : 0,
            'year_mrp_quantity' => ($yearsumquantity >= 1 && $year_mrp_quantity >= 1) ? round(($year_mrp_quantity * 100) / $yearsumquantity) : 0,
        ]);
        return response()->json($data);
    }
    public function orderSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);
        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }
        /*============= Query Orders ====================*/
        $dataorders = Order::with('buyers', 'orderdetails')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('order_date', '>=', $query_start_date);
            $query->where('order_date', '<=', $query_end_date);
            $query->whereIn('created_by', $users);
        })
            ->select('id', 'grand_total', 'order_date', 'buyer_id', 'seller_id', 'created_by')->get();
        $datatargets = SalesTarget::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('startdate', '>=', $query_start_date);
            $query->where('enddate', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->select('startdate', 'enddate', 'amount')->get();
        /*============= Query Sales ====================*/
        $datasales = Sales::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('invoice_date', '>=', $query_start_date);
            $query->where('invoice_date', '<=', $query_end_date);
            $query->whereIn('created_by', $users);
        })
            ->select('id', 'grand_total', 'invoice_date', 'buyer_id', 'seller_id', 'created_by')->get();
        /*============= Orders Count ====================*/
        $data['orderCollectedCount'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->count();
        $data['orderCollectedSum'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->sum('grand_total');
        $ggplids = Product::where('category_id', '=', '1')->pluck('id')->toArray();
        $gpdids = Product::where('category_id', '=', '2')->pluck('id')->toArray();
        $gdplids = Product::where('category_id', '=', '3')->pluck('id')->toArray();
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
            ->select(['product_id', DB::raw("SUM(price) as total_price"), DB::raw("SUM(quantity) as total_quantity")])
            ->groupBy('product_id')
            ->orderBy('total_price')
            ->limit(10)
            ->get();
        $data['yeartargetachievement'] = $year_calendar->map(function ($item, $key) use ($dataorders, $datasales, $datatargets) {
            $month_start_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '31'));
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
            return $item;
        });
        $data['top_representatives'] = Order::with('createdbyname')
            ->whereYear('order_date', '=', date('Y'))
            ->select(['created_by', DB::raw("SUM(grand_total) as total_amount"), DB::raw("SUM(total_qty) as total_quantity")])
            ->groupBy('created_by')
            ->orderBy('total_amount')
            ->limit(10)
            ->get();
        $userscities = UserCityAssign::with('userinfo')->select('userid')->groupBy('userid')->get();
        $data['orders_by_zone'] = $userscities->map(function ($item, $key) {
            $item['zone_name'] =  $item['userinfo']['location'];
            $citiesids = UserCityAssign::where('userid', '=', $item['userid'])->pluck('city_id')->toArray();

            $item['order_amount'] = Order::whereHas('customeraddress', function ($query) use ($citiesids) {
                $query->whereYear('order_date', '=', date('Y'));
                $query->whereIn('city_id', $citiesids);
            })->sum('grand_total');
            $item['sales_amount'] = Sales::whereHas('customeraddress', function ($query) use ($citiesids) {
                $query->whereYear('invoice_date', '=', date('Y'));
                $query->whereIn('city_id', $citiesids);
            })->sum('grand_total');
            return $item;
        });
        $statesname = Address::with('statename')->whereNotNull('customer_id')->whereNotNull('state_id')->select('state_id')->groupBy('state_id')->get();
        $data['orders_by_state'] = $statesname->map(function ($item, $key) {
            $item['state_name'] =  $item['statename']['state_name'];
            $item['order_amount'] = Order::whereHas('customeraddress', function ($query) use ($item) {
                $query->whereYear('order_date', '=', date('Y'));
                $query->where('state_id', $item['state_id']);
            })->sum('grand_total');
            $item['sales_amount'] = Sales::whereHas('customeraddress', function ($query) use ($item) {
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
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);
        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }
        /*=========== Query SalesTarget ======================*/
        $datatargets = SalesTarget::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('startdate', '>=', $query_start_date);
            $query->where('enddate', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->select('startdate', 'enddate', 'amount');
        $data['top_sales_representatives'] = Sales::with('createdbyname')
            ->whereYear('invoice_date', '=', date('Y'))
            ->select(['created_by', DB::raw("SUM(grand_total) as total_amount"), DB::raw("SUM(total_qty) as total_quantity")])
            ->groupBy('created_by')
            ->orderBy('total_amount')
            ->limit(10)
            ->get();
        return response()->json($data);
    }
    public function activityDashboardCount(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $users = getUsersReportingToAuth($userid);
        /*========= User Activities ==============*/
        $data['activities'] = UserActivity::with('customers', 'users')
            ->where(function ($query) use ($fromdate, $todate, $users) {
                if (!empty($fromdate)) {
                    $query->whereDate('time', '>=', date('Y-m-d', strtotime($fromdate)));
                }
                if (!empty($todate)) {
                    $query->whereDate('time', '<=', date('Y-m-d', strtotime($todate)));
                }
                $query->whereIn('userid', $users);
            })
            ->select('id', 'userid', 'customerid', 'address', 'description', 'type', 'time')
            ->latest()
            ->get();

        return response()->json($data);
    }

    public function secondary_dashboard_sales(Request $request)
    {
        $retailers = Customers::where('customertype', '2')->get();
        $dealers_and_distibutors = Customers::where('customertype', [3, 4])->get();
        $sales_persons = User::latest()->get();
        $products = Product::latest()->get();;
        $users = User::latest()->get();
        $branches = Branch::latest()->get();
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);

        $orders = Order::with(['buyers', 'orderdetails', 'getuserdetails', 'getsalesdetail'])->get();

        abort_if(Gate::denies('dashboard_secondary_sales_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('secondary_dashboard.index', compact('branches', 'years', 'sales_persons', 'divisions', 'retailers', 'dealers_and_distibutors', 'products'));
    }

    public function secondary_dashboard_sales_list(Request $request)
    {

        $query = OrderDetails::with(['orders', 'orders.sellers', 'orders.buyers', 'orders.createdbyname', 'orders.getuserdetails.getdivision', 'orders.buyers.customeraddress.cityname', 'orders.buyers.customeraddress.statename', 'products', 'products.productpriceinfo', 'orders.getuserdetails.getbranch', 'orders.sellers.customeraddress.cityname', 'orders.createdbyname.getbranch', 'orders.createdbyname.getbranch'])->where(function ($query) use ($request) {
            $query->whereHas('orders', function ($q) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $userids = getUsersReportingToAuth();
                    $customer_ids = Customers::whereIn('executive_id', $userids)->orWhereIn('created_by', $userids)->pluck('id');
                    $q->whereIn('buyer_id', $customer_ids)
                        ->orWhereIn('seller_id', $customer_ids);
                }
            });

            if ($request->month && $request->month != '' && $request->month != null && $request->financial_year && $request->financial_year != '' && $request->financial_year != null) {

                $f_year_array = explode('-', $request->financial_year);

                if ($request->month == 'Jan' || $request->month == 'Feb' || $request->month == 'Mar') {
                    $currentYear = $f_year_array[1];
                    $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$request->month")->startOfMonth();
                    $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$request->month")->endOfMonth();
                    $startDateFormatted = $startDate->toDateString();
                    $endDateFormatted = $endDate->toDateString();
                } else {
                    $currentYear = $f_year_array[0];
                    $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$request->month")->startOfMonth();
                    $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$request->month")->endOfMonth();
                    $startDateFormatted = $startDate->toDateString();
                    $endDateFormatted = $endDate->toDateString();
                }

                $query->whereHas('orders', function ($q) use ($startDateFormatted, $endDateFormatted) {
                    $q->whereDate('order_date', '>=', $startDateFormatted)
                        ->whereDate('order_date', '<=', $endDateFormatted);
                });
            }


            if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                $branchIds = User::where('branch_id', $request->branch_id)->pluck('id');

                $query->whereHas('orders', function ($q) use ($branchIds) {
                    $q->whereIn('orders.created_by', $branchIds);
                });
            }

            if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
                $dealerIds = Customers::where('id', $request->dealer_id)->pluck('id');

                $query->whereHas('orders', function ($q) use ($dealerIds) {
                    $q->whereIn('orders.seller_id', $dealerIds);
                });
            }

            // if($request->user_id && $request->user_id != '' && $request->user_id != null){
            //     $userIds = User::where('id', $request->user_id)->pluck('id');
            //     $query->whereIn('se',$userIds);
            // }

            if ($request->executive_id && $request->executive_id != '' && $request->executive_id != null) {
                $executiveIds = User::where('id', $request->executive_id)->pluck('id');

                $query->whereHas('orders', function ($q) use ($executiveIds) {
                    $q->whereIn('orders.created_by', $executiveIds);
                });
            }

            if ($request->retailer_id && $request->retailer_id != '' && $request->retailer_id != null) {
                $buyerIds = User::where('id', $request->retailer_id)->pluck('id');

                $query->whereHas('orders', function ($q) use ($buyerIds) {
                    $q->whereIn('orders.buyer_id', $buyerIds);
                });
                // $query->whereIn('buyer_id',$buyerIds);
            }

            if ($request->division_id && $request->division_id != '' && $request->division_id != null) {
                $userIds = User::where('division_id', $request->division_id)->pluck('id')->toArray();

                $query->whereHas('orders', function ($q) use ($userIds) {
                    $q->whereIn('orders.created_by', $userIds);
                });
            }

            if ($request->product_model && $request->product_model != '' && $request->product_model != null) {
                $productIds = Product::where('id', $request->product_model)->pluck('id')->toArray();
                $query->whereIn('product_id', $productIds);
            }

            if ($request->new_group && $request->new_group != '') {
                $productIds = Product::where('id', $request->new_group)->pluck('id')->toArray();
                $query->whereIn('product_id', $productIds);
            }

            if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {

                $f_year_array = explode('-', $request->financial_year);
                $financial_year_start = $f_year_array[0] . '-04-01';
                $financial_year_end = $f_year_array[1] . '-03-31';

                $query->whereHas('orders', function ($q) use ($financial_year_start, $financial_year_end) {
                    $q->where('order_date', '>=', $financial_year_start)
                        ->where('order_date', '<=', $financial_year_end);
                });
            }

            if ($request->min_range && $request->min_range != '' && $request->min_range != null && $request->max_range && $request->max_range != '' && $request->max_range != null) {
                $query->whereBetween('points', [$request->min_range, $request->max_range]);
            }
        })->orderBy('id', 'asc');

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('order_date', function ($query) {
                return date('d/m/Y', strtotime($query->orders->order_date)) ?? '';
            })
            ->addColumn('month', function ($query) {
                return date('M Y', strtotime($query->orders->order_date)) ?? '';
            })
            ->addColumn('total_qty', function ($query) {
                return $query->sum('quantity') ?? '';
            })
            ->addColumn('total', function ($query) {
                return $query->products->productpriceinfo->gst ?? '';
            })
            ->addColumn('gst_amount', function ($query) {
                $tax = $query->products->productpriceinfo->gst;
                $new_amount = $query->line_total;

                $gst = number_format((float)(($query->line_total * ($tax / 100))), 2, '.', '');
                // $gst = ($query->line_total* ($tax/100));

                return $gst;
            })
            ->addColumn('total_amount', function ($query) {
                $net_amount = $query->line_total;
                $gst_amount = $query->line_total * ($query->products->productpriceinfo->gst / 100);

                $total_amount = number_format((float)(($net_amount + $gst_amount)), 2, '.', '');

                return $total_amount;
            })
            ->rawColumns(['order_date', 'month', 'total', 'gst_amount', 'total_amount'])
            ->make(true);
    }

    public function secondarySalesKpiData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);

        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }

        // $orderDetails = OrderDetails::with(['orders','products','productdetails','orders.buyers'])->get();

        $orderDetails = OrderDetails::with(['orders', 'orders.sellers', 'orders.buyers', 'orders.createdbyname', 'orders.getuserdetails.getdivision', 'orders.buyers.customeraddress.cityname', 'orders.buyers.customeraddress.statename', 'products', 'products.productpriceinfo', 'orders.getuserdetails.getbranch', 'orders.sellers.customeraddress.cityname', 'orders.createdbyname.getbranch']);

        if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
            $usersIds = User::where('id', $request->user_id)->where('sales_type', 'Secondary')->pluck('id');
        } else {
            $usersIds = User::with('attendance_details')->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
            $usersIds = User::where('branch_id', $request->branch_id)->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->division_id && $request->division_id != '' && $request->division_id != null) {
            $usersIds = User::where('division_id', $request->division_id)->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $orderDetails->whereHas('orders', function ($query) use ($financial_year_end) {
                $query->where('order_date', '>=', $financial_year_start)
                    ->where('order_date', '<=', $financial_year_end);
            });
        }


        if ($request->month && $request->month != '' && $request->month != null) {
            $orderDetails->where('month', $request->month);
        }

        // user with secondary sales type
        $employeeIds = EmployeeDetail::whereIn('user_id', $usersIds)->pluck('customer_id');
        $customerIds = Customers::whereIn('id', $employeeIds)->pluck('id');

        $registeredRetailerCount = $customerIds->count();

        $activeRetailerCount = $orderDetails->whereHas('orders', function ($query) use ($customerIds) {
            $query->whereIn('buyer_id', $customerIds);
        })->count();

        $activeRetailerPercent = 0;
        if ($registeredRetailerCount > 0) {
            $activeRetailerPercent = number_format((float)(($activeRetailerCount / $registeredRetailerCount) * 100), 2, '.', '');
        }

        $transactionHistory = TransactionHistory::with('customer')->get();

        $nosOfRetailerRegistredSaarthi = $transactionHistory->whereIn('customer_id', $customerIds)->groupBy('customer_id')->count();

        $nosOfRetailerRegistredSaarthiPercent = 0;
        if ($registeredRetailerCount > 0) {
            $nosOfRetailerRegistredSaarthiPercent =  number_format((float)(($nosOfRetailerRegistredSaarthi / $registeredRetailerCount) * 100), 2, '.', '');
        }

        $orderTarget = SalesTargetUsers::where('type', 'secondary')->whereIn('user_id', $customerIds)->sum('target');

        $orderAchievement = $orderDetails->where(function ($query) use ($customerIds) {
            $query->whereHas('orders', function ($subquery) use ($customerIds) {
                $subquery->whereIn('buyer_id', $customerIds);
            });
        })->sum('line_total');

        $achievementPercent = 0;
        if ($orderTarget > 0) {
            $achievementPercent = number_format((float)(($orderAchievement / $orderTarget) * 100), 2, '.', '');
        }

        $attendanceCount = Attendance::whereIn('user_id', $usersIds)->groupBy('user_id')->count();
        $totalUsers = $usersIds->count();

        $avgWorkingDays = 0;
        if ($totalUsers > 0) {
            $avgWorkingDays = number_format((float)($attendanceCount / $totalUsers));
        }

        $totalOrders = $orderDetails->where(function ($query) use ($usersIds) {
            $query->whereHas('orders', function ($subquery) use ($usersIds) {
                $subquery->whereIn('buyer_id', $usersIds);
            });
        })->count();

        $averageVisits = VisitReport::whereIn('user_id', $usersIds)->count();


        $perDayAverageSales = ($avgWorkingDays > 0) ? number_format((float)($totalOrders / $avgWorkingDays)) : '0';
        $perDayAverageVisit = ($averageVisits > 0) ?  number_format((float)($totalOrders / $averageVisits)) : '0';
        $data['registeredRetailerCount'] = $registeredRetailerCount;
        $data['activeRetailerCount'] = $activeRetailerCount;
        $data['activeRetailerPercent'] = $activeRetailerPercent;
        $data['nosOfRetailerRegistredSaarthi'] = $nosOfRetailerRegistredSaarthi;
        $data['nosOfRetailerRegistredSaarthiPercent'] = $nosOfRetailerRegistredSaarthiPercent;

        $data['orderTarget'] = $orderTarget;
        $data['orderAchievement'] = $orderAchievement;
        $data['achievementPercent'] = $achievementPercent;
        $data['perDayAverageSales'] = $perDayAverageSales;
        $data['perDayAverageVisit'] = $perDayAverageVisit;

        return response()->json($data);
    }

    public function primary_sales_template(Request $request)
    {
        abort_if(Gate::denies('primary_sales_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PrimarySalesTemplate, 'primary_sales_template.xlsx');
    }

    public function primary_sales_download(Request $request)
    {
        abort_if(Gate::denies('primary_sales_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PrimarySalesExport($request), 'primary_sales.xlsx');
    }

    public function primary_sales_upload(Request $request)
    {
        abort_if(Gate::denies('primary_sales_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new PrimarySalesImport, request()->file('import_file'));

        return back()->with('success', 'Primary Sales Import successfully !!');
    }

    public function primary_dashboard_sales_list(Request $request)
    {

        $query = PrimarySales::query();

        if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
            $usersIds = User::where('id', $request->user_id)->where('sales_type', 'Secondary')->pluck('id');
        } else {
            $usersIds = User::with('attendance_details')->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
            $query->where('final_branch', $request->branch_id);
        }

        if ($request->division_id && $request->division_id != '' && count($request->division_id) > 0) {
            $query->whereIn('division', $request->division_id);
        }
        $role = Role::find(29);
        if ($role && auth()->user()->hasRole($role->name)) {
            $child_customer = ParentDetail::where('parent_id', auth()->user()->customerid)
                ->pluck('customer_id')
                ->push(auth()->user()->customerid);
            $query->whereIn('customer_id', $child_customer);
        }
        if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
            $query->where('dealer', 'like', '%' . $request->dealer_id . '%');
        }

        if ($request->product_model && $request->product_model != '' && $request->product_model != null) {
            $query->where('product_name', $request->product_model);
        }

        if ($request->new_group && $request->new_group != '' && $request->new_group != null) {
            $query->where('new_group', $request->new_group);
        }

        if ($request->executive_id && $request->executive_id != '' && $request->executive_id != null) {
            $query->where('sales_person', $request->executive_id);
        }

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->where(function ($q) use ($f_year_array, $financial_year_start, $financial_year_end) {
                $q->where('invoice_date', '>=', $financial_year_start)
                    ->where('invoice_date', '<=', $financial_year_end);;
            });
        }

        if ($request->month && $request->month != '' && count($request->month) > 0 && $request->financial_year && $request->financial_year != '' && $request->financial_year != null) {

            $f_year_array = explode('-', $request->financial_year);

            if (in_array('Jan', $request->month) || in_array('Feb', $request->month) || in_array('Mar', $request->month)) {
                $currentYear = $f_year_array[1];
                $monthNumbers = array_map(function ($month) {
                    return Carbon::parse($month)->month;
                }, $request->month);

                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);

                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $startDateFormatted = $firstDate->toDateString();
                $endDateFormatted = $lastDate->toDateString();
            } else {
                $currentYear = $f_year_array[0];
                $monthNumbers = array_map(function ($month) {
                    return Carbon::parse($month)->month;
                }, $request->month);

                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);

                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $startDateFormatted = $firstDate->toDateString();
                $endDateFormatted = $lastDate->toDateString();
            }

            $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->where('invoice_date', '>=', $startDateFormatted)
                    ->where('invoice_date', '<=', $endDateFormatted);;
            });
        }

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('invoice_date', function ($query) {
                return date('d/M/Y', strtotime($query->invoice_date));
            })
            ->rawColumns(['invoice_date'])
            ->make(true);
    }

    /*
    Secondary sales total order value and quantity
    */

    public function total_order_value(Request $request)
    {
        $orderDetails = OrderDetails::with(['orders', 'orders.sellers', 'orders.buyers', 'orders.createdbyname', 'orders.getuserdetails.getdivision', 'orders.buyers.customeraddress.cityname', 'orders.buyers.customeraddress.statename', 'products', 'products.productpriceinfo', 'orders.getuserdetails.getbranch', 'orders.sellers.customeraddress.cityname', 'orders.createdbyname.getbranch']);

        $totalOrderValue = $orderDetails->get()->SUM('line_total');
        $totalOrderQty = $orderDetails->get()->SUM('quantity');
        $totalOrders = $orderDetails->groupBy('order_id')->count();

        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
            $branchIds = User::where('branch_id', $request->branch_id)->pluck('id');

            $totalOrderValue = $orderDetails->whereHas('orders', function ($q) use ($branchIds) {
                $q->whereIn('created_by', $branchIds);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($q) use ($branchIds) {
                $q->whereIn('created_by', $branchIds);
            })->get()->SUM('quantity');
            $totalOrders = $orderDetails->whereHas('orders', function ($q) use ($branchIds) {
                $q->whereIn('created_by', $branchIds);
            })->get()->count();
        }

        if ($request->division_id && $request->division_id != '' && $request->division_id != null) {

            $divisionIds = User::where('division_id', $request->division_id)->pluck('id');

            $totalOrderValue = $orderDetails->whereHas('orders', function ($q) use ($divisionIds) {
                $q->whereIn('created_by', $divisionIds);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($q) use ($divisionIds) {
                $q->whereIn('created_by', $divisionIds);
            })->get()->SUM('quantity');

            $totalOrders = $orderDetails->whereHas('orders', function ($q) use ($divisionIds) {
                $q->whereIn('created_by', $divisionIds);
            })->pluck('id')->count();
        }

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {

            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $totalOrderValue = $orderDetails->whereHas('orders', function ($query) use ($financial_year_start, $financial_year_end) {
                $query->where('order_date', '>=', $financial_year_start)
                    ->where('order_date', '<=', $financial_year_end);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($query) use ($financial_year_start, $financial_year_end) {
                $query->where('order_date', '>=', $financial_year_start)
                    ->where('order_date', '<=', $financial_year_end);
            })->get()->SUM('quantity');
        }

        if ($request->month && $request->month != '' && $request->month != null) {
            $month = $request->month;
            $totalOrderValue = $orderDetails->whereHas('orders', function ($query) use ($month) {
                $query->whereMonth('order_date', '=', $month);
            })->get();
            $totalOrderQty = $orderDetails->whereHas('orders', function ($query) use ($month) {
                $query->whereMonth('order_date', '=', $month);
            })->get();
        }

        if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
            $dealerIds = Customers::where('id', $request->dealer_id)->pluck('id');

            $totalOrderValue = $orderDetails->whereHas('orders', function ($q) use ($dealerIds) {
                $q->whereIn('orders.seller_id', $dealerIds);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($q) use ($dealerIds) {
                $q->whereIn('orders.seller_id', $dealerIds);
            })->get()->SUM('quantity');
        }

        if ($request->executive_id && $request->executive_id != '' && $request->executive_id != null) {
            $executiveIds = User::where('id', $request->executive_id)->pluck('id');

            $totalOrderValue = $orderDetails->whereHas('orders', function ($q) use ($executiveIds) {
                $q->whereIn('orders.created_by', $executiveIds);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($q) use ($executiveIds) {
                $q->whereIn('orders.created_by', $executiveIds);
            })->get()->SUM('quantity');
        }

        // if($request->user_id && $request->user_id != '' && $request->user_id != null){
        //     $userIds = User::where('id', $request->user_id)->pluck('id');
        //     $query->whereIn('se',$userIds);
        // }

        if ($request->retailer_id && $request->retailer_id != '' && $request->retailer_id != null) {
            $buyerIds = User::where('id', $request->retailer_id)->pluck('id');

            $totalOrderValue = $orderDetails->whereHas('orders', function ($q) use ($buyerIds) {
                $q->whereIn('orders.buyer_id', $buyerIds);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($q) use ($buyerIds) {
                $q->whereIn('orders.buyer_id', $buyerIds);
            })->get()->SUM('quantity');
        }

        if ($request->product_model && $request->product_model != '' && $request->product_model != null) {
            $productIds = Product::where('id', $request->product_model)->pluck('id')->toArray();
            $totalOrderValue = $orderDetails->whereIn('product_id', $productIds)->get()->SUM('line_total');
            $totalOrderQty = $orderDetails->whereIn('product_id', $productIds)->get()->SUM('quantity');
        }

        if ($request->new_group && $request->new_group != '') {
            $productIds = Product::where('id', $request->new_group)->pluck('id')->toArray();
            $totalOrderValue = $orderDetails->whereIn('product_id', $productIds)->get()->SUM('line_total');
            $totalOrderQty = $orderDetails->whereIn('product_id', $productIds)->get()->SUM('quantity');
        }

        $data['total_order_value'] = $totalOrderValue;
        $data['total_order_qty'] = $totalOrderQty;
        $data['total_order'] = $totalOrders;

        return response()->json($data);
    }

    public function eloficSecondarySalesDashboard(Request $request)
    {
        return $this->eloficCachedJson($request, 'secondary', 300, fn () => $this->eloficSecondarySalesData($request));
    }

    private function eloficSecondarySalesData(Request $request): array
    {
        [$fromdate, $todate, $periodLabel] = $this->eloficDashboardPeriod($request);
        $zoneId = $this->eloficDashboardZoneId($request);
        $activeFrom = Carbon::parse($todate)->subMonths(12)->toDateString();

        // Secondary sales source: order_details.line_total joined to orders by order_id.
        // This follows the existing secondary dashboard total_order_value() calculation.
        // One grouped query gives the period total and the monthly split
        $secondaryMonthly = $this->eloficSecondaryMonthly($fromdate, $todate, $zoneId);
        $achievement = (float) array_sum($secondaryMonthly);
        $target = $this->eloficTargetTotal($fromdate, $todate, $zoneId);

        $registrations = DB::table('secondary_customers as sc');
        if ($zoneId && $this->efHasColumn('secondary_customers', 'created_by')) {
            $registrations->join('users as scu', 'scu.id', '=', 'sc.created_by')
                ->where('scu.branch_id', $zoneId);
        }
        $scopeIds = $this->eloficScopeUserIds();
        if ($scopeIds !== null) {
            if ($this->efHasColumn('secondary_customers', 'created_by')) {
                $registrations->whereIn('sc.created_by', $scopeIds);
            } else {
                $registrations->whereRaw('1 = 0');
            }
        }
        $totalRegistrations = (int) (clone $registrations)->count();
        $breakdown = (clone $registrations)
            ->selectRaw("UPPER(sc.type) as type, COUNT(*) as total")
            ->groupBy(DB::raw('UPPER(sc.type)'))
            ->get()
            ->keyBy('type');

        $activeBreakdown = DB::table('secondary_customers as sc')
            ->join('orders as o', 'o.buyer_id', '=', 'sc.id')
            ->whereBetween('o.order_date', [$activeFrom, $todate])
            ->whereNull('o.deleted_at');

        if ($zoneId) {
            $activeBreakdown->join('users as zu', 'zu.id', '=', 'o.created_by')
                ->where('zu.branch_id', $zoneId);
        }
        if ($scopeIds !== null) {
            $activeBreakdown->whereIn('o.created_by', $scopeIds);
        }

        $activeBreakdown = $activeBreakdown
            ->selectRaw("UPPER(sc.type) as type, COUNT(DISTINCT sc.id) as active")
            ->groupBy(DB::raw('UPPER(sc.type)'))
            ->pluck('active', 'type');

        $types = [
            'RETAILER' => 'Retailers',
            'MECHANIC' => 'Mechanics',
            'GARAGE' => 'Garages',
            'WORKSHOP' => 'Workshops',
        ];
        $customerBreakdown = collect($types)->map(function ($label, $key) use ($breakdown, $activeBreakdown) {
            $total = (int) optional($breakdown->get($key))->total;
            $active = (int) ($activeBreakdown[$key] ?? 0);
            return [
                'type' => $label,
                'total' => $total,
                'active' => $active,
                'inactive' => max($total - $active, 0),
                'activePercent' => $total > 0 ? round(($active / $total) * 100) : 0,
            ];
        })->values();
        $activeCustomers = (int) $customerBreakdown->sum('active');

        $topCustomers = $this->eloficOrderDetailsBase($fromdate, $todate, $zoneId)
            ->leftJoin('secondary_customers as sc', 'sc.id', '=', 'o.buyer_id')
            ->leftJoin('states as st', 'st.id', '=', 'sc.state_id')
            ->leftJoin('cities as ct', 'ct.id', '=', 'sc.city_id')
            ->selectRaw("COALESCE(NULLIF(sc.shop_name, ''), NULLIF(sc.owner_name, ''), CONCAT('Customer #', o.buyer_id)) as label, COALESCE(NULLIF(st.state_name, ''), '-') as state, COALESCE(ct.city_name, '') as city, SUM(od.line_total) as value, COUNT(DISTINCT o.id) as orders")
            ->groupBy('o.buyer_id', 'sc.shop_name', 'sc.owner_name', 'st.state_name', 'ct.city_name')
            ->orderByDesc('value')
            ->limit(10)
            ->get();

        $monthlySales = $this->eloficMonthlyBuckets($fromdate, $todate)->map(function ($bucket) use ($zoneId, $secondaryMonthly) {
            return [
                'label' => $bucket['label'],
                'achievement' => $secondaryMonthly[substr($bucket['from'], 0, 7)] ?? 0.0,
                'target' => $this->eloficTargetTotal($bucket['from'], $bucket['to'], $zoneId),
            ];
        });

        $salesTrend = $monthlySales->map(fn ($item) => [
            'label' => $item['label'],
            'value' => $item['achievement'],
        ]);

        // 3 financial years x 12 months from a single grouped query (was 36 queries)
        $yoyGrowth = $this->eloficYoySeries($todate, fn ($from, $to) => $this->eloficSecondaryMonthly($from, $to, $zoneId));

        $zoneSales = $this->eloficOrderDetailsBase($fromdate, $todate, $zoneId)
            ->leftJoin('users as u', 'u.id', '=', 'o.created_by')
            ->leftJoin('branches as b', 'b.id', '=', 'u.branch_id')
            ->selectRaw("b.id as branch_id, COALESCE(NULLIF(b.branch_name, ''), 'Unassigned') as label, SUM(od.line_total) as achievement")
            ->groupBy('b.id', 'label')
            ->get()
            ->keyBy('branch_id');

        $zoneBranches = DB::table('branches')
            ->where('active', 'Y')
            ->when($zoneId, fn ($query) => $query->where('id', $zoneId))
            ->when($scopeIds !== null, fn ($query) => $query->whereIn('id', $this->eloficScopeBranchIds()))
            ->orderBy('branch_name')
            ->get(['id', 'branch_name']);

        $zoneWiseSales = $zoneBranches->map(function ($branch) use ($fromdate, $todate, $zoneSales) {
                $sales = $zoneSales->get($branch->id);
                return [
                    'label' => $branch->branch_name,
                    'achievement' => (float) optional($sales)->achievement,
                    'target' => $this->eloficTargetTotal($fromdate, $todate, (int) $branch->id),
                ];
            })
            ->values();

        $zoneWiseTarget = $zoneWiseSales;

        return [
            'periodLabel' => $periodLabel,
            'source' => 'orders + order_details.line_total',
            'filter' => compact('fromdate', 'todate', 'zoneId'),
            'kpis' => [
                'totalRegistrations' => $totalRegistrations,
                'activeCustomers' => $activeCustomers,
                'secondarySalesAchievement' => $achievement,
                'secondarySalesTarget' => $target,
                'overallAchievementPercent' => $target > 0 ? round(($achievement / $target) * 100, 2) : 0,
            ],
            'customerBreakdown' => $customerBreakdown,
            'charts' => [
                'topCustomers' => $topCustomers,
                'salesVsTarget' => $zoneWiseTarget,
                'salesTrend' => $salesTrend,
                'yoyGrowth' => $yoyGrowth,
                'regionWiseSales' => $zoneWiseSales,
            ],
        ];
    }

    public function eloficProductDashboard(Request $request)
    {
        return $this->eloficCachedJson($request, 'product', 300, fn () => $this->eloficProductData($request));
    }

    private function eloficProductData(Request $request): array
    {
        [$fromdate, $todate, $periodLabel] = $this->eloficDashboardPeriod($request);
        $zoneId = $this->eloficDashboardZoneId($request);
        $base = $this->eloficOrderDetailsBase($fromdate, $todate, $zoneId)
            ->join('products as p', 'p.id', '=', 'od.product_id');

        // NPD = API entry_date (products.launch_date) within the last 12 months and not manually excluded
        $hasNpd = $this->efHasColumn('products', 'npd_excluded') && $this->efHasColumn('products', 'launch_date');
        $npdCutoff = \App\Models\Product::npdCutoff()->toDateTimeString();
        $npdCondition = 'p.npd_excluded = 0 AND p.launch_date IS NOT NULL AND p.launch_date >= ?';

        $productRanking = (clone $base)
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->selectRaw("COALESCE(NULLIF(p.product_name, ''), CONCAT('Product #', od.product_id)) as label, COALESCE(NULLIF(p.product_code, ''), '') as code, COALESCE(NULLIF(c.category_name, ''), NULLIF(p.new_group, ''), '') as segment, SUM(od.line_total) as value, SUM(od.quantity) as qty")
            ->groupBy('od.product_id', 'p.product_name', 'p.product_code', 'c.category_name', 'p.new_group');
        if ($hasNpd) {
            $productRanking->selectRaw("MAX(CASE WHEN {$npdCondition} THEN 1 ELSE 0 END) as is_npd, MAX(p.launch_date) as launch_date", [$npdCutoff]);
        }

        $npdSummary = ['sales' => 0, 'qty' => 0, 'skusSold' => 0, 'catalog' => 0];
        $topNpdByValue = collect();
        $topNpdByQty = collect();
        $bottomNpdByValue = collect();
        $bottomNpdByQty = collect();
        if ($hasNpd) {
            $npdRanking = (clone $productRanking)->whereRaw($npdCondition, [$npdCutoff]);
            $topNpdByValue = (clone $npdRanking)->orderByDesc('value')->limit(10)->get();
            $topNpdByQty = (clone $npdRanking)->orderByDesc('qty')->orderByDesc('value')->limit(10)->get();
            // Bottom 10 = NPD products that sold something in the period, lowest first
            $bottomNpdByValue = (clone $npdRanking)->havingRaw('SUM(od.line_total) > 0')->orderBy('value')->orderBy('qty')->limit(10)->get();
            $bottomNpdByQty = (clone $npdRanking)->havingRaw('SUM(od.quantity) > 0')->orderBy('qty')->orderBy('value')->limit(10)->get();

            $npdTotals = (clone $base)->whereRaw($npdCondition, [$npdCutoff])
                ->selectRaw('SUM(od.line_total) as amount, SUM(od.quantity) as qty, COUNT(DISTINCT od.product_id) as skus')
                ->first();
            $npdSummary = [
                'sales' => (float) ($npdTotals->amount ?? 0),
                'qty' => (float) ($npdTotals->qty ?? 0),
                'skusSold' => (int) ($npdTotals->skus ?? 0),
                'catalog' => (int) \App\Models\Product::npd()->where('active', 'Y')->whereNull('deleted_at')->count(),
            ];
        }

        $topProducts = (clone $productRanking)
            ->orderByDesc('value')
            ->limit(23)
            ->get();

        $topProductsByQty = (clone $productRanking)
            ->orderByDesc('qty')
            ->orderByDesc('value')
            ->limit(23)
            ->get();

        $slowMoving = (clone $base)
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->selectRaw("COALESCE(NULLIF(p.product_name, ''), CONCAT('Product #', od.product_id)) as label, COALESCE(NULLIF(p.product_code, ''), '') as code, COALESCE(NULLIF(c.category_name, ''), NULLIF(p.new_group, ''), '') as segment, SUM(od.line_total) as value, SUM(od.quantity) as qty")
            ->groupBy('od.product_id', 'p.product_name', 'p.product_code', 'c.category_name', 'p.new_group')
            ->havingRaw('SUM(od.line_total) > 0')
            ->orderBy('value')
            ->limit(10)
            ->get();

        $segmentWise = (clone $base)
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->selectRaw("COALESCE(NULLIF(c.category_name, ''), NULLIF(p.new_group, ''), 'Unassigned') as label, SUM(od.line_total) as value, SUM(od.quantity) as qty, COUNT(DISTINCT od.product_id) as skus")
            ->groupBy('label')
            ->orderByDesc('value')
            ->get();

        $topSkuIds = $topProducts->take(10)->pluck('label')->values();

        $productTotals = (clone $base)
            ->selectRaw('SUM(od.line_total) as amount, SUM(od.quantity) as qty, COUNT(DISTINCT od.product_id) as skus')
            ->first();
        $productSales = (float) ($productTotals->amount ?? 0);
        $productQty = (float) ($productTotals->qty ?? 0);
        $productsSold = (int) ($productTotals->skus ?? 0);
        $catalogQuery = DB::table('products')->whereNull('deleted_at');
        if ($this->efHasColumn('products', 'active')) {
            $catalogQuery->where('active', 'Y');
        }
        $catalogProducts = (int) $catalogQuery->count();
        if ($catalogProducts === 0) {
            $catalogProducts = (int) DB::table('products')->whereNull('deleted_at')->count();
        }
        $topSkuProducts = (clone $base)
            ->selectRaw('od.product_id, p.product_name, p.product_code, SUM(od.line_total) as value, SUM(od.quantity) as qty')
            ->groupBy('od.product_id', 'p.product_name', 'p.product_code')
            ->orderByRaw('SUM(od.line_total) DESC')
            ->limit(10)
            ->get();
        $buckets = $this->eloficMonthlyBuckets($fromdate, $todate);
        // Monthly sales of the top 10 SKUs in one grouped query (was 10 x months queries)
        $skuMonthly = [];
        if ($topSkuProducts->isNotEmpty()) {
            $this->eloficOrderDetailsBase($fromdate, $todate, $zoneId)
                ->whereIn('od.product_id', $topSkuProducts->pluck('product_id')->all())
                ->selectRaw("od.product_id, DATE_FORMAT(o.order_date, '%Y-%m') as ym, SUM(od.line_total) as total")
                ->groupBy('od.product_id', 'ym')
                ->get()
                ->each(function ($row) use (&$skuMonthly) {
                    $skuMonthly[$row->product_id][$row->ym] = (float) $row->total;
                });
        }
        $topSkuTrend = $topSkuProducts->map(function ($product) use ($buckets, $skuMonthly) {
            return [
                'label' => $product->product_name ?: ('Product #' . $product->product_id),
                'code' => (string) ($product->product_code ?? ''),
                'value' => (float) $product->value,
                'qty' => (float) $product->qty,
                'data' => $buckets->map(fn ($bucket) => [
                    'label' => $bucket['label'],
                    'value' => $skuMonthly[$product->product_id][substr($bucket['from'], 0, 7)] ?? 0.0,
                ])->values(),
            ];
        })->values();

        return [
            'periodLabel' => $periodLabel,
            'source' => 'orders + order_details.line_total + products',
            'filter' => compact('fromdate', 'todate', 'zoneId'),
            'kpis' => [
                'productSales' => $productSales,
                'totalQuantity' => $productQty,
                'productsSold' => $productsSold,
                'catalogProducts' => $catalogProducts,
                'segments' => $segmentWise->count(),
                'topSegment' => optional($segmentWise->first())->label,
                'npdSales' => $npdSummary['sales'],
                'npdQuantity' => $npdSummary['qty'],
                'npdSkusSold' => $npdSummary['skusSold'],
                'npdCatalog' => $npdSummary['catalog'],
            ],
            'charts' => [
                'topProducts' => $topProducts,
                'topProductsByQty' => $topProductsByQty,
                'topNpdByValue' => $topNpdByValue,
                'topNpdByQty' => $topNpdByQty,
                'bottomNpdByValue' => $bottomNpdByValue,
                'bottomNpdByQty' => $bottomNpdByQty,
                'slowMovingProducts' => $slowMoving,
                'segmentWiseSales' => $segmentWise,
                'topSkuTrend' => $topSkuTrend,
                'topSkuLabels' => $topSkuIds,
            ],
        ];
    }

    public function eloficPrimarySalesDashboard(Request $request)
    {
        return $this->eloficCachedJson($request, 'primary', 300, fn () => $this->eloficPrimarySalesData($request));
    }

    private function eloficPrimarySalesData(Request $request): array
    {
        [$fromdate, $todate, $periodLabel] = $this->eloficDashboardPeriod($request);
        $zoneId = $this->eloficDashboardZoneId($request);
        $zoneName = $this->eloficDashboardZoneName($zoneId);
        $totals = $this->eloficPrimarySalesBase($fromdate, $todate, $zoneName)
            ->selectRaw('SUM(net_amount) as amount, SUM(quantity) as qty, COUNT(DISTINCT invoiceno) as invoices')
            ->first();
        $achievement = (float) ($totals->amount ?? 0);
        $target = $this->eloficPrimaryTargetTotal($fromdate, $todate, $zoneName);
        $totalQty = (float) ($totals->qty ?? 0);
        $totalInvoices = (int) ($totals->invoices ?? 0);

        $primaryMonthly = $this->eloficPrimaryMonthly($fromdate, $todate, $zoneName);
        $salesVsTarget = $this->eloficMonthlyBuckets($fromdate, $todate)->map(function ($bucket) use ($zoneName, $primaryMonthly) {
            return [
                'label' => $bucket['label'],
                'achievement' => $primaryMonthly[substr($bucket['from'], 0, 7)] ?? 0.0,
                'target' => $this->eloficPrimaryTargetTotal($bucket['from'], $bucket['to'], $zoneName),
            ];
        });

        $salesTrend = $salesVsTarget->map(fn ($item) => [
            'label' => $item['label'],
            'value' => $item['achievement'],
        ]);

        // 3 financial years x 12 months from a single grouped query (was 36 queries)
        $yoyGrowth = $this->eloficYoySeries($todate, fn ($from, $to) => $this->eloficPrimaryMonthly($from, $to, $zoneName));

        $regionWise = $this->eloficPrimarySalesBase($fromdate, $todate, $zoneName)
            ->selectRaw("COALESCE(NULLIF(final_branch, ''), NULLIF(branch, ''), 'Unassigned') as label, SUM(net_amount) as achievement")
            ->groupBy('label')
            ->orderByDesc('achievement')
            ->limit(8)
            ->get()
            ->map(function ($row) use ($fromdate, $todate) {
                return [
                    'label' => $row->label,
                    'achievement' => (float) $row->achievement,
                    'target' => $this->eloficPrimaryTargetTotal($fromdate, $todate, $row->label),
                ];
            });

        $topDealers = $this->eloficPrimarySalesBase($fromdate, $todate, $zoneName)
            ->selectRaw("COALESCE(NULLIF(dealer, ''), 'Unassigned') as label, COALESCE(MAX(NULLIF(state, '')), '-') as state, COALESCE(MAX(NULLIF(city, '')), '') as city, SUM(net_amount) as value, COUNT(DISTINCT invoiceno) as orders")
            ->groupBy('label')
            ->orderByDesc('value')
            ->limit(10)
            ->get();

        return [
            'periodLabel' => $periodLabel,
            'source' => 'primary_sales.net_amount',
            'filter' => compact('fromdate', 'todate', 'zoneId'),
            'kpis' => [
                'primarySalesAchievement' => $achievement,
                'primarySalesTarget' => $target,
                'overallAchievementPercent' => $target > 0 ? round(($achievement / $target) * 100, 2) : 0,
                'totalQuantity' => $totalQty,
                'totalInvoices' => $totalInvoices,
            ],
            'charts' => [
                'topDealers' => $topDealers,
                'salesVsTarget' => $salesVsTarget,
                'salesTrend' => $salesTrend,
                'yoyGrowth' => $yoyGrowth,
                'regionWiseSales' => $regionWise,
            ],
        ];
    }

    public function eloficEmployeesDashboard(Request $request)
    {
        return $this->eloficCachedJson($request, 'employees', 120, fn () => $this->eloficEmployeesData($request));
    }

    private function eloficEmployeesData(Request $request): array
    {
        [$fromdate, $todate, $periodLabel] = $this->eloficDashboardPeriod($request);
        $zoneId = $this->eloficDashboardZoneId($request);
        $zoneName = $this->eloficDashboardZoneName($zoneId);
        $today = Carbon::today()->toDateString();
        $users = $this->eloficEmployeeUsers($zoneId);
        $userIds = $users->pluck('id');

        $todayAttendance = DB::table('attendances')
            ->whereDate('punchin_date', $today)
            ->whereIn('user_id', $userIds);
        $todayOnMarket = (int) (clone $todayAttendance)
            ->whereNotNull('punchin_time')
            ->where(function ($query) {
                $query->whereNull('working_type')
                    ->orWhere('working_type', 'not like', '%leave%');
            })
            ->distinct('user_id')
            ->count('user_id');
        $leaveQuery = (clone $todayAttendance)
            ->whereNotNull('punchin_time')
            ->where('working_type', 'like', '%leave%');
        $todayOnLeave = (int) $leaveQuery->distinct('user_id')->count('user_id');

        $punchedInUserIds = (clone $todayAttendance)
            ->whereNotNull('punchin_time')
            ->distinct()
            ->pluck('user_id');
        $misPunch = $userIds->diff($punchedInUserIds)->count();

        $secondaryOrders = DB::table('orders as o')
            ->leftJoin('order_details as od', 'od.order_id', '=', 'o.id')
            ->whereBetween('o.order_date', [$fromdate, $todate])
            ->whereNull('o.deleted_at')
            ->whereIn('o.created_by', $userIds);

        $orderTotals = (clone $secondaryOrders)
            ->selectRaw('SUM(od.quantity) as qty, SUM(od.line_total) as amount, COUNT(DISTINCT o.buyer_id) as customers')
            ->first();
        $totalOrderQty = (float) ($orderTotals->qty ?? 0);
        $totalOrderValue = (float) ($orderTotals->amount ?? 0);
        $uniqueActiveCustomers = (int) ($orderTotals->customers ?? 0);

        $primaryValue = (float) $this->eloficPrimarySalesBase($fromdate, $todate, $zoneName)->sum('net_amount');

        // Per-employee figures in a handful of grouped queries instead of 4-8 queries per employee
        $secondaryByUser = (clone $secondaryOrders)
            ->groupBy('o.created_by')
            ->selectRaw('o.created_by as uid, SUM(od.line_total) as total')
            ->pluck('total', 'uid');
        $primaryByUser = $this->eloficPrimaryAchievementByUser($fromdate, $todate, $users);
        $targetsByUser = $this->eloficEmployeeTargetsByUser($fromdate, $todate, $userIds->all());

        $employeeTargets = $users->map(function ($user) use ($secondaryByUser, $primaryByUser, $targetsByUser) {
            $secondaryAchievement = (float) ($secondaryByUser[$user->id] ?? 0);
            $primaryAchievement = (float) ($primaryByUser[$user->id] ?? 0);
            $target = (float) ($targetsByUser[$user->id] ?? 0);
            $achievement = $secondaryAchievement + $primaryAchievement;
            $employeeCode = trim((string) ($user->name ?? ''));
            return [
                'label' => $employeeCode !== '' ? $employeeCode : ('User #' . $user->id),
                'name' => $user->name ?: ('User #' . $user->id),
                'code' => trim((string) ($user->employee_codes ?? '')),
                'achievement' => $achievement,
                'target' => $target,
                'achievementPercent' => $target > 0 ? round(($achievement / $target) * 100, 2) : 0,
            ];
        })
            ->filter(fn ($row) => $row['achievement'] > 0 || $row['target'] > 0)
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->take(60);

        $visits = $this->eloficVisitStats($fromdate, $todate, $userIds);

        return [
            'periodLabel' => $periodLabel,
            'source' => 'users + attendances + orders/order_details + primary_sales + check_in',
            'filter' => compact('fromdate', 'todate', 'zoneId'),
            'kpis' => [
                'totalEmployees' => $users->count(),
                'todayOnMarket' => $todayOnMarket,
                'todayOnLeave' => $todayOnLeave,
                'misPunch' => $misPunch,
            ],
            'summary' => [
                'totalOrderQty' => $totalOrderQty,
                'totalOrderValue' => $totalOrderValue + $primaryValue,
                'uniqueActiveCustomers' => $uniqueActiveCustomers,
            ],
            'charts' => [
                'employeeTargetVsAchievement' => $employeeTargets,
                'visits' => $visits,
            ],
        ];
    }

    /**
     * Active employee users. Same rule as User Management: users holding a customer role
     * (distributors created as users) are not employees.
     */
    private function eloficEmployeeUsers(?int $zoneId)
    {
        $customerRoleIds = (array) config('constants.customer_roles', []);
        return DB::table('users')
            ->where('active', 'Y')
            ->when(!empty($customerRoleIds), fn ($query) => $query->whereNotIn('id', function ($sub) use ($customerRoleIds) {
                $sub->select('model_id')
                    ->from(config('permission.table_names.model_has_roles', 'model_has_roles'))
                    ->where('model_type', (new \App\Models\User)->getMorphClass())
                    ->whereIn('role_id', $customerRoleIds);
            }))
            ->when($zoneId, fn ($query) => $query->where('branch_id', $zoneId))
            ->when($this->eloficScopeUserIds() !== null, fn ($query) => $query->whereIn('id', $this->eloficScopeUserIds()))
            ->select('id', 'name', 'employee_codes')
            ->get();
    }

    /**
     * Employee-wise visits for its own date range (a single day works too).
     * Params: fromDate, toDate (Y-m-d, default today), zoneId.
     * Per employee and customer type: visits, unique customers, new customers (created in range by that employee).
     */
    public function eloficEmployeeVisits(Request $request)
    {
        return $this->eloficCachedJson($request, 'employee-visits', 120, fn () => $this->eloficEmployeeVisitsData($request));
    }

    private function eloficEmployeeVisitsData(Request $request): array
    {
        $today = Carbon::today()->toDateString();
        try {
            $from = Carbon::parse($request->input('fromDate') ?: $today);
            $to = Carbon::parse($request->input('toDate') ?: $from->toDateString());
        } catch (\Throwable $e) {
            $from = $to = Carbon::today();
        }
        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }
        $fromdate = $from->toDateString();
        $todate = $to->toDateString();
        $types = ['RETAILER', 'MECHANIC', 'GARAGE', 'WORKSHOP'];
        $label = $fromdate === $todate ? $from->format('d M Y') : $from->format('d M Y') . ' - ' . $to->format('d M Y');

        $users = $this->eloficEmployeeUsers($this->eloficDashboardZoneId($request));
        $zero = fn () => ['visits' => 0, 'unique' => 0, 'new' => 0];
        $rows = [];
        foreach ($users as $user) {
            $rows[$user->id] = [
                'id' => $user->id,
                'name' => trim((string) $user->name) !== '' ? $user->name : ('User #' . $user->id),
                'code' => trim((string) ($user->employee_codes ?? '')),
                'ALL' => $zero(),
            ];
            foreach (array_merge($types, ['OTHER']) as $t) {
                $rows[$user->id][$t] = $zero();
            }
        }

        if ($this->efHasTable('check_in') && !empty($rows)) {
            $newCondition = 'sc.created_at BETWEEN ? AND ?';
            if ($this->efHasColumn('secondary_customers', 'created_by')) {
                $newCondition .= ' AND sc.created_by = ci.user_id';
            }
            $typeExpr = "CASE WHEN UPPER(sc.type) IN ('" . implode("','", $types) . "') THEN UPPER(sc.type) ELSE 'OTHER' END";
            $stats = DB::table('check_in as ci')
                ->join('secondary_customers as sc', 'sc.id', '=', 'ci.entity_id')
                ->where('ci.entity_type', 'secondary_customer')
                ->whereBetween('ci.checkin_date', [$fromdate, $todate])
                ->whereIn('ci.user_id', array_keys($rows))
                ->selectRaw("ci.user_id, {$typeExpr} as ctype, COUNT(*) as visits, COUNT(DISTINCT ci.entity_id) as uniq, COUNT(DISTINCT CASE WHEN {$newCondition} THEN ci.entity_id END) as new_cust", [$fromdate . ' 00:00:00', $todate . ' 23:59:59'])
                ->groupBy('ci.user_id', 'ctype')
                ->get();
            foreach ($stats as $r) {
                if (!isset($rows[$r->user_id])) {
                    continue;
                }
                $vals = ['visits' => (int) $r->visits, 'unique' => (int) $r->uniq, 'new' => (int) $r->new_cust];
                $rows[$r->user_id][$r->ctype] = $vals;
                // customer types are disjoint, so distinct counts can be summed for ALL
                foreach ($vals as $k => $v) {
                    $rows[$r->user_id]['ALL'][$k] += $v;
                }
            }
        }

        $rows = collect($rows)->sortByDesc(fn ($r) => $r['ALL']['visits'])->values();

        return [
            'periodLabel' => $label,
            'filter' => compact('fromdate', 'todate'),
            'types' => $types,
            'employees' => $rows,
        ];
    }

    /**
     * Secondary-customer visits (check_in) by employees, split by customer type.
     * - visits:    every check-in
     * - unique:    distinct customers visited
     * - new:       distinct customers visited that were added in this period by the same employee
     * Daily buckets for periods up to 62 days, monthly beyond that.
     */
    private function eloficVisitStats(string $fromdate, string $todate, $userIds): array
    {
        $types = ['RETAILER', 'MECHANIC', 'GARAGE', 'WORKSHOP'];
        $daily = Carbon::parse($fromdate)->diffInDays(Carbon::parse($todate)) <= 62;

        $buckets = [];
        if ($daily) {
            for ($d = Carbon::parse($fromdate); $d->lte(Carbon::parse($todate)); $d->addDay()) {
                $buckets[$d->format('Y-m-d')] = $d->format('d M');
            }
        } else {
            for ($d = Carbon::parse($fromdate)->startOfMonth(); $d->lte(Carbon::parse($todate)); $d->addMonth()) {
                $buckets[$d->format('Y-m')] = $d->format('M y');
            }
        }

        $empty = ['granularity' => $daily ? 'day' : 'month', 'types' => $types, 'buckets' => [], 'totals' => []];
        if (!$this->efHasTable('check_in') || collect($userIds)->isEmpty()) {
            return $empty;
        }

        $newCondition = 'sc.created_at BETWEEN ? AND ?';
        $newBindings = [$fromdate . ' 00:00:00', $todate . ' 23:59:59'];
        if ($this->efHasColumn('secondary_customers', 'created_by')) {
            $newCondition .= ' AND sc.created_by = ci.user_id';
        }

        $base = DB::table('check_in as ci')
            ->join('secondary_customers as sc', 'sc.id', '=', 'ci.entity_id')
            ->where('ci.entity_type', 'secondary_customer')
            ->whereBetween('ci.checkin_date', [$fromdate, $todate])
            ->whereIn('ci.user_id', collect($userIds)->all());

        $typeExpr = "CASE WHEN UPPER(sc.type) IN ('" . implode("','", $types) . "') THEN UPPER(sc.type) ELSE 'OTHER' END";
        $metrics = "COUNT(*) as visits, COUNT(DISTINCT ci.entity_id) as uniq, COUNT(DISTINCT CASE WHEN {$newCondition} THEN ci.entity_id END) as new_cust";
        $bucketExpr = $daily ? "DATE_FORMAT(ci.checkin_date, '%Y-%m-%d')" : "DATE_FORMAT(ci.checkin_date, '%Y-%m')";

        $byBucket = (clone $base)
            ->selectRaw("{$bucketExpr} as bucket, {$typeExpr} as ctype, {$metrics}", $newBindings)
            ->groupBy('bucket', 'ctype')
            ->get();
        $byType = (clone $base)
            ->selectRaw("{$typeExpr} as ctype, {$metrics}", $newBindings)
            ->groupBy('ctype')
            ->get()
            ->keyBy('ctype');

        $zero = fn () => ['visits' => 0, 'unique' => 0, 'new' => 0];
        $rows = [];
        foreach ($buckets as $key => $label) {
            $rows[$key] = ['label' => $label, 'ALL' => $zero()];
            foreach (array_merge($types, ['OTHER']) as $t) {
                $rows[$key][$t] = $zero();
            }
        }
        foreach ($byBucket as $r) {
            if (!isset($rows[$r->bucket])) {
                continue;
            }
            $vals = ['visits' => (int) $r->visits, 'unique' => (int) $r->uniq, 'new' => (int) $r->new_cust];
            $rows[$r->bucket][$r->ctype] = $vals;
            // customer types are disjoint, so distinct counts can be summed for ALL
            foreach ($vals as $k => $v) {
                $rows[$r->bucket]['ALL'][$k] += $v;
            }
        }

        $totals = ['ALL' => $zero()];
        foreach (array_merge($types, ['OTHER']) as $t) {
            $row = $byType->get($t);
            $totals[$t] = $row ? ['visits' => (int) $row->visits, 'unique' => (int) $row->uniq, 'new' => (int) $row->new_cust] : $zero();
            foreach ($totals[$t] as $k => $v) {
                $totals['ALL'][$k] += $v;
            }
        }

        return [
            'granularity' => $daily ? 'day' : 'month',
            'types' => $types,
            'buckets' => array_values($rows),
            'totals' => $totals,
        ];
    }

    private function eloficDashboardPeriod(Request $request): array
    {
        $type = strtoupper($request->input('filterType', 'MTD'));
        $today = Carbon::today();

        if ($type === 'YTD') {
            $fyStartYear = $today->month >= 4 ? $today->year : $today->year - 1;
            $from = Carbon::create($fyStartYear, 4, 1);
            $to = $today;
            return [$from->toDateString(), $to->toDateString(), 'YTD Apr ' . $fyStartYear . ' - ' . $to->format('M Y')];
        }

        if ($type === 'CUSTOM' && $request->filled('fromDate') && $request->filled('toDate')) {
            $from = Carbon::parse($request->input('fromDate'));
            $to = Carbon::parse($request->input('toDate'));
            if ($from->gt($to)) {
                [$from, $to] = [$to, $from];
            }
            return [$from->toDateString(), $to->toDateString(), $from->format('d M Y') . ' - ' . $to->format('d M Y')];
        }

        return [$today->copy()->startOfMonth()->toDateString(), $today->toDateString(), 'MTD ' . $today->format('M Y')];
    }

    private function eloficDashboardZoneId(Request $request): ?int
    {
        $zoneId = $request->input('zoneId');
        return is_numeric($zoneId) && (int) $zoneId > 0 ? (int) $zoneId : null;
    }

    /**
     * Roles that see the whole company on the dashboard. Everyone else (BM, ZM, ASM, ...)
     * only sees the employees reporting to them (getUsersReportingToAuth). Same list as
     * getUsersReportingToAuth's "all users" branch, minus the branch-based roles.
     */
    private const EF_FULL_ACCESS_ROLES = [
        'superadmin', 'Admin', 'subAdmin', 'CRM', 'HR_Admin', 'HO_Account', 'Sub_Support',
        'Service Admin', 'All Customers', 'Sub billing', 'Sales Admin', 'Marketing_Admin',
        'MIS_ADMIN', 'Data_Crm',
    ];

    private bool $efScopeResolved = false;
    private ?array $efScopeIds = null;
    private ?array $efScopeUsers = null;

    /** null = no restriction; otherwise the user ids (self + reporting hierarchy) the viewer may see. */
    private function eloficScopeUserIds(): ?array
    {
        if (!$this->efScopeResolved) {
            $this->efScopeResolved = true;
            $user = Auth::user();
            if ($user && ((int) $user->id === 41012 || !$user->hasAnyRole(self::EF_FULL_ACCESS_ROLES))) {
                $this->efScopeIds = array_values(array_unique(array_map('intval', getUsersReportingToAuth($user->id))));
                if (empty($this->efScopeIds)) {
                    $this->efScopeIds = [(int) $user->id];
                }
            }
        }

        return $this->efScopeIds;
    }

    /** id, name, employee_codes, branch_id of the scoped users (only when scoped). */
    private function eloficScopeUsers()
    {
        $ids = $this->eloficScopeUserIds();
        if ($ids === null) {
            return null;
        }
        if ($this->efScopeUsers === null) {
            $this->efScopeUsers = DB::table('users')->whereIn('id', $ids)
                ->get(['id', 'name', 'employee_codes', 'branch_id'])->all();
        }

        return collect($this->efScopeUsers);
    }

    /** Branches the scoped users belong to (branch_id may be a comma list). */
    private function eloficScopeBranchIds(): ?array
    {
        $users = $this->eloficScopeUsers();
        if ($users === null) {
            return null;
        }

        return $users->flatMap(fn ($user) => explode(',', (string) $user->branch_id))
            ->map(fn ($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /** Restricts a primary_sales query to rows of the scoped users (emp_code, else sales_person name). */
    private function eloficApplyPrimaryScope($query, string $prefix = '')
    {
        $users = $this->eloficScopeUsers();
        if ($users === null) {
            return $query;
        }
        $codes = $users->pluck('employee_codes')->map(fn ($v) => trim((string) $v))->filter()->unique()->values()->all();
        $names = $users->pluck('name')->map(fn ($v) => trim((string) $v))->filter()->unique()->values()->all();
        $hasCode = $this->efHasColumn('primary_sales', 'emp_code');
        $hasPerson = $this->efHasColumn('primary_sales', 'sales_person');

        $query->where(function ($sub) use ($codes, $names, $hasCode, $hasPerson, $prefix) {
            $sub->whereRaw('1 = 0');
            if ($hasCode && !empty($codes)) {
                $sub->orWhereIn($prefix . 'emp_code', $codes);
            }
            if ($hasPerson && !empty($names)) {
                $sub->orWhereIn($prefix . 'sales_person', $names);
            }
        });

        return $query;
    }

    private function eloficDashboardZoneName(?int $zoneId): ?string
    {
        if (!$zoneId) {
            return null;
        }

        return DB::table('branches')
            ->where('id', $zoneId)
            ->value('branch_name');
    }

    private function eloficOrderDetailsBase(string $fromdate, string $todate, ?int $zoneId = null)
    {
        $query = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->whereBetween('o.order_date', [$fromdate, $todate])
            ->whereNull('o.deleted_at');

        if ($zoneId) {
            $query->join('users as zu', 'zu.id', '=', 'o.created_by')
                ->where('zu.branch_id', $zoneId);
        }
        if (($scopeIds = $this->eloficScopeUserIds()) !== null) {
            $query->whereIn('o.created_by', $scopeIds);
        }

        return $query;
    }

    private function eloficPrimarySalesBase(string $fromdate, string $todate, ?string $branchName = null)
    {
        $query = DB::table('primary_sales')
            ->whereBetween('invoice_date', [$fromdate, $todate]);

        if ($branchName) {
            $query->where(function ($subQuery) use ($branchName) {
                $subQuery->where('final_branch', $branchName)
                    ->orWhere('branch', $branchName);
            });
        }

        return $this->eloficApplyPrimaryScope($query);
    }

    private function eloficTargetLakhToRupees(float $target): float
    {
        return $target * 100000;
    }

    private function eloficTargetBase(string $fromdate, string $todate, ?int $zoneId = null)
    {
        $query = DB::table('sales_targets')
            ->whereDate('sales_targets.startdate', '<=', $todate)
            ->where(function ($dateQuery) use ($fromdate) {
                $dateQuery->whereNull('sales_targets.enddate')
                    ->orWhereDate('sales_targets.enddate', '>=', $fromdate);
            });

        if ($zoneId) {
            $query->join('users as tu', 'tu.id', '=', 'sales_targets.userid')
                ->where('tu.branch_id', $zoneId);
        }
        if (($scopeIds = $this->eloficScopeUserIds()) !== null) {
            $query->whereIn('sales_targets.userid', $scopeIds);
        }

        return $query;
    }

    private function eloficTargetTotal(string $fromdate, string $todate, ?int $zoneId = null): float
    {
        $target = $this->eloficTargetLakhToRupees((float) $this->eloficTargetBase($fromdate, $todate, $zoneId)->sum('amount'));
        if ($target <= 0 && $this->efHasTable('salestargetusers')) {
            $target = (float) $this->eloficLegacyTargetTotal($fromdate, $todate, $zoneId);
        }
        if ($target <= 0 && $this->efHasTable('branchwise_targets')) {
            $target = (float) $this->eloficBranchwiseTargetTotal($fromdate, $todate, $zoneId, 'secondary');
        }
        return $target;
    }

    private function eloficLegacyTargetTotal(string $fromdate, string $todate, ?int $zoneId = null): float
    {
        $query = DB::table('salestargetusers');
        if ($this->efHasColumn('salestargetusers', 'type')) {
            $query->where(function ($typeQuery) {
                $typeQuery->where('type', 'secondary')
                    ->orWhereNull('type')
                    ->orWhere('type', '');
            });
        }
        if ($zoneId) {
            if ($this->efHasColumn('salestargetusers', 'branch_id')) {
                $query->where('branch_id', $zoneId);
            } elseif ($this->efHasColumn('salestargetusers', 'userid')) {
                $query->join('users as tu', 'tu.id', '=', 'salestargetusers.userid')
                    ->where('tu.branch_id', $zoneId);
            } elseif ($this->efHasColumn('salestargetusers', 'user_id')) {
                $query->join('users as tu', 'tu.id', '=', 'salestargetusers.user_id')
                    ->where('tu.branch_id', $zoneId);
            }
        }
        if ($this->efHasColumn('salestargetusers', 'month')) {
            $query->whereIn('month', $this->eloficTargetMonthValues($fromdate, $todate));
        }
        if ($this->efHasColumn('salestargetusers', 'year')) {
            $query->whereIn(DB::raw('YEAR(year)'), $this->eloficTargetYears($fromdate, $todate));
        }
        if (!$this->eloficApplyTargetUserScope($query, 'salestargetusers')) {
            return 0.0;
        }
        return $this->eloficTargetLakhToRupees((float) $query->sum('target'));
    }

    private function eloficBranchwiseTargetTotal(string $fromdate, string $todate, ?int $zoneId = null, ?string $type = null): float
    {
        $query = DB::table('branchwise_targets');
        if ($type && $this->efHasColumn('branchwise_targets', 'type')) {
            $query->where(function ($typeQuery) use ($type) {
                $typeQuery->where('type', $type)
                    ->orWhereNull('type')
                    ->orWhere('type', '');
            });
        }
        if ($zoneId && $this->efHasColumn('branchwise_targets', 'branch_id')) {
            $query->where('branch_id', $zoneId);
        }
        if (!$this->eloficApplyTargetUserScope($query, 'branchwise_targets')) {
            return 0.0;
        }
        if ($this->efHasColumn('branchwise_targets', 'month')) {
            $query->whereIn('month', $this->eloficTargetMonthValues($fromdate, $todate));
        }
        if ($this->efHasColumn('branchwise_targets', 'year')) {
            $query->whereIn(DB::raw('YEAR(year)'), $this->eloficTargetYears($fromdate, $todate));
        }

        $target = $this->efHasColumn('branchwise_targets', 'target') ? (float) (clone $query)->sum('target') : 0;
        if ($target <= 0 && $this->efHasColumn('branchwise_targets', 'amount')) {
            $target = (float) $query->sum('amount');
        }

        return $this->eloficTargetLakhToRupees($target);
    }

    private function eloficPrimaryTargetTotal(string $fromdate, string $todate, ?string $branchName = null): float
    {
        $monthNames = $this->eloficMonthlyBuckets($fromdate, $todate)->pluck('label')->map(function ($label) {
            return Carbon::createFromFormat('M y', $label)->format('M');
        })->values();

        $target = 0;
        if ($this->efHasTable('salestargetusers')) {
            $query = DB::table('salestargetusers');
            if ($this->efHasColumn('salestargetusers', 'type')) {
                $query->where('type', 'primary');
            }
            if ($this->efHasColumn('salestargetusers', 'month')) {
                $query->whereIn('month', $this->eloficTargetMonthValues($fromdate, $todate));
            }
            if ($this->efHasColumn('salestargetusers', 'year')) {
                $query->whereIn(DB::raw('YEAR(year)'), $this->eloficTargetYears($fromdate, $todate));
            }
            if ($branchName && $this->efHasColumn('salestargetusers', 'branch_id')) {
                $branchIds = DB::table('branches')->where('branch_name', $branchName)->pluck('id');
                if ($branchIds->isNotEmpty()) {
                    $query->whereIn('branch_id', $branchIds);
                }
            }
            if ($this->eloficApplyTargetUserScope($query, 'salestargetusers')) {
                $target += $this->eloficTargetLakhToRupees((float) $query->sum('target'));
            }
        }

        if ($target <= 0 && $this->efHasTable('branchwise_targets')) {
            $query = DB::table('branchwise_targets');
            if ($this->efHasColumn('branchwise_targets', 'type')) {
                $query->where('type', 'primary');
            }
            if ($this->efHasColumn('branchwise_targets', 'month')) {
                $query->whereIn('month', $this->eloficTargetMonthValues($fromdate, $todate));
            }
            if ($this->efHasColumn('branchwise_targets', 'year')) {
                $query->whereIn(DB::raw('YEAR(year)'), $this->eloficTargetYears($fromdate, $todate));
            }
            if ($branchName) {
                $query->where('branch_name', $branchName);
            }
            if ($this->eloficApplyTargetUserScope($query, 'branchwise_targets')) {
                $target += $this->eloficTargetLakhToRupees((float) $query->sum('target'));
            }
        }

        return $target;
    }

    /**
     * Limits a target table to the scoped users. Returns false when the viewer is scoped but the
     * table has no user column (a branch-level target cannot be split per team, so it is skipped).
     */
    private function eloficApplyTargetUserScope($query, string $table): bool
    {
        $scopeIds = $this->eloficScopeUserIds();
        if ($scopeIds === null) {
            return true;
        }
        $column = $this->efHasColumn($table, 'user_id') ? 'user_id' : ($this->efHasColumn($table, 'userid') ? 'userid' : null);
        if (!$column) {
            return false;
        }
        $query->whereIn($table . '.' . $column, $scopeIds);

        return true;
    }

    private function eloficTargetYears(string $fromdate, string $todate): array
    {
        return $this->eloficMonthlyBuckets($fromdate, $todate)
            ->map(fn ($bucket) => (int) Carbon::parse($bucket['from'])->format('Y'))
            ->unique()
            ->values()
            ->all();
    }

    private function eloficTargetMonthValues(string $fromdate, string $todate): array
    {
        return $this->eloficMonthlyBuckets($fromdate, $todate)
            ->flatMap(function ($bucket) {
                $date = Carbon::parse($bucket['from']);
                return [
                    $date->format('M'),
                    $date->format('F'),
                    $date->format('m'),
                    $date->format('n'),
                ];
            })
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Dashboard responses are cached per endpoint + filter (+ viewer, for team-scoped roles) for a few minutes.
     * The key includes today's date so MTD / YTD roll over at midnight. Pass ?refresh=1 to rebuild.
     */
    private function eloficCachedJson(Request $request, string $name, int $ttlSeconds, \Closure $build)
    {
        $params = [
            'filterType' => strtoupper((string) $request->input('filterType', 'MTD')),
            'fromDate' => (string) $request->input('fromDate', ''),
            'toDate' => (string) $request->input('toDate', ''),
            'zoneId' => (string) $request->input('zoneId', ''),
        ];
        // Scoped viewers (BM etc.) get their own cache entry; full-access roles share one.
        $scopeIds = $this->eloficScopeUserIds();
        $params['scope'] = $scopeIds === null ? 'all' : 'u' . Auth::id();
        $key = 'elofic_dash:v3:' . $name . ':' . Carbon::today()->toDateString() . ':' . md5(json_encode($params));
        if ($request->boolean('refresh')) {
            Cache::forget($key);
        }

        return response()->json(Cache::remember($key, $ttlSeconds, $build));
    }

    /** @var array<string, array{exists: bool, columns: array<string, bool>}> */
    private array $efSchema = [];

    /**
     * Schema::hasTable / hasColumn hit information_schema on every call; these are called inside loops,
     * so the column list of each table is cached (1 hour) and memoised per request.
     */
    private function efSchemaInfo(string $table): array
    {
        if (!isset($this->efSchema[$table])) {
            $this->efSchema[$table] = Cache::remember('elofic_dash:schema:' . $table, 3600, function () use ($table) {
                if (!\Schema::hasTable($table)) {
                    return ['exists' => false, 'columns' => []];
                }
                $columns = array_map('strtolower', \Schema::getColumnListing($table));
                return ['exists' => true, 'columns' => array_fill_keys($columns, true)];
            });
        }

        return $this->efSchema[$table];
    }

    private function efHasTable(string $table): bool
    {
        return $this->efSchemaInfo($table)['exists'];
    }

    private function efHasColumn(string $table, string $column): bool
    {
        return isset($this->efSchemaInfo($table)['columns'][strtolower($column)]);
    }

    /** Secondary sales (order_details.line_total) per month in one query: ['Y-m' => value]. */
    private function eloficSecondaryMonthly(string $fromdate, string $todate, ?int $zoneId = null): array
    {
        return $this->eloficOrderDetailsBase($fromdate, $todate, $zoneId)
            ->selectRaw("DATE_FORMAT(o.order_date, '%Y-%m') as ym, SUM(od.line_total) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym')
            ->map(fn ($value) => (float) $value)
            ->all();
    }

    /** Primary sales (net_amount) per month in one query: ['Y-m' => value]. */
    private function eloficPrimaryMonthly(string $fromdate, string $todate, ?string $branchName = null): array
    {
        return $this->eloficPrimarySalesBase($fromdate, $todate, $branchName)
            ->selectRaw("DATE_FORMAT(invoice_date, '%Y-%m') as ym, SUM(net_amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym')
            ->map(fn ($value) => (float) $value)
            ->all();
    }

    /**
     * Monthly series for the last three financial years (FY-2 .. current FY) from one per-month map.
     * $monthly is a callable (from, to) => ['Y-m' => value].
     */
    private function eloficYoySeries(string $todate, callable $monthly)
    {
        $to = Carbon::parse($todate);
        $currentFyStart = $to->month >= 4 ? $to->year : $to->year - 1;
        $values = $monthly(
            Carbon::create($currentFyStart - 2, 4, 1)->toDateString(),
            Carbon::create($currentFyStart + 1, 3, 31)->toDateString()
        );

        return collect([2, 1, 0])->map(function ($yearsBack) use ($currentFyStart, $values) {
            $fyStartYear = $currentFyStart - $yearsBack;
            $from = Carbon::create($fyStartYear, 4, 1)->toDateString();
            $to = Carbon::create($fyStartYear + 1, 3, 31)->toDateString();
            return [
                'label' => 'FY' . substr((string) ($fyStartYear + 1), -2),
                'data' => $this->eloficMonthlyBuckets($from, $to)->map(fn ($bucket) => [
                    'label' => $bucket['label'],
                    'value' => $values[substr($bucket['from'], 0, 7)] ?? 0.0,
                ])->values(),
            ];
        })->values();
    }

    /**
     * Primary achievement per employee in at most two grouped queries.
     * Same matching rule as before: emp_code when the column exists and the user has a code, else sales_person = name.
     */
    private function eloficPrimaryAchievementByUser(string $fromdate, string $todate, $users): array
    {
        $hasCode = $this->efHasColumn('primary_sales', 'emp_code');
        $hasPerson = $this->efHasColumn('primary_sales', 'sales_person');
        $norm = fn ($value) => mb_strtolower(trim((string) $value));
        $usesCode = fn ($user) => $hasCode && !empty($user->employee_codes);

        $sumBy = function (string $column, array $values) use ($fromdate, $todate, $norm) {
            $totals = [];
            if (empty($values)) {
                return $totals;
            }
            $rows = $this->eloficPrimarySalesBase($fromdate, $todate)
                ->whereIn($column, $values)
                ->selectRaw("{$column} as k, SUM(net_amount) as total")
                ->groupBy($column)
                ->get();
            foreach ($rows as $row) {
                $key = $norm($row->k);
                $totals[$key] = ($totals[$key] ?? 0) + (float) $row->total;
            }
            return $totals;
        };

        $byCode = $sumBy('emp_code', $hasCode ? $users->filter($usesCode)->pluck('employee_codes')->unique()->values()->all() : []);
        $byPerson = $sumBy('sales_person', $hasPerson
            ? $users->reject($usesCode)->filter(fn ($user) => !empty($user->name))->pluck('name')->unique()->values()->all()
            : []);

        $result = [];
        foreach ($users as $user) {
            if ($usesCode($user)) {
                $result[$user->id] = $byCode[$norm($user->employee_codes)] ?? 0.0;
            } elseif ($hasPerson && !empty($user->name)) {
                $result[$user->id] = $byPerson[$norm($user->name)] ?? 0.0;
            } else {
                $result[$user->id] = 0.0;
            }
        }

        return $result;
    }

    /** Employee targets (rupees) per user in grouped queries: sales_targets + salestargetusers + branchwise_targets. */
    private function eloficEmployeeTargetsByUser(string $fromdate, string $todate, array $userIds): array
    {
        $targets = [];
        if (empty($userIds)) {
            return $targets;
        }
        $add = function ($rows) use (&$targets) {
            foreach ($rows as $userId => $amount) {
                $targets[$userId] = ($targets[$userId] ?? 0) + $this->eloficTargetLakhToRupees((float) $amount);
            }
        };
        $monthValues = $this->eloficTargetMonthValues($fromdate, $todate);
        $years = $this->eloficTargetYears($fromdate, $todate);

        $add(DB::table('sales_targets')
            ->whereIn('userid', $userIds)
            ->whereBetween(DB::raw('DATE(startdate)'), [$fromdate, $todate])
            ->groupBy('userid')
            ->selectRaw('userid as uid, SUM(amount) as total')
            ->pluck('total', 'uid'));

        if ($this->efHasTable('salestargetusers')) {
            $userColumn = $this->efHasColumn('salestargetusers', 'user_id') ? 'user_id' : ($this->efHasColumn('salestargetusers', 'userid') ? 'userid' : null);
            if ($userColumn) {
                $query = DB::table('salestargetusers')->whereIn($userColumn, $userIds);
                if ($this->efHasColumn('salestargetusers', 'month')) {
                    $query->whereIn('month', $monthValues);
                }
                if ($this->efHasColumn('salestargetusers', 'year')) {
                    $query->whereIn(DB::raw('YEAR(year)'), $years);
                }
                $add($query->groupBy($userColumn)->selectRaw("{$userColumn} as uid, SUM(target) as total")->pluck('total', 'uid'));
            }
        }

        if ($this->efHasTable('branchwise_targets') && $this->efHasColumn('branchwise_targets', 'user_id')) {
            $query = DB::table('branchwise_targets')->whereIn('user_id', $userIds);
            if ($this->efHasColumn('branchwise_targets', 'month')) {
                $query->whereIn('month', $monthValues);
            }
            if ($this->efHasColumn('branchwise_targets', 'year')) {
                $query->whereIn(DB::raw('YEAR(year)'), $years);
            }
            $add($query->groupBy('user_id')->selectRaw('user_id as uid, SUM(target) as total')->pluck('total', 'uid'));
        }

        return $targets;
    }

    private function eloficMonthlyBuckets(string $fromdate, string $todate)
    {
        $start = Carbon::parse($fromdate)->startOfMonth();
        $end = Carbon::parse($todate)->endOfMonth();
        $buckets = collect([]);

        while ($start->lte($end)) {
            $bucketFrom = $start->copy()->max(Carbon::parse($fromdate));
            $bucketTo = $start->copy()->endOfMonth()->min(Carbon::parse($todate));
            $buckets->push([
                'label' => $start->format('M y'),
                'from' => $bucketFrom->toDateString(),
                'to' => $bucketTo->toDateString(),
            ]);
            $start->addMonth();
        }

        return $buckets;
    }

    /*
    Primary Sales KPI
    */

    public function primarySalesKpiData(Request $request)
    {

        dd($request->all());
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);

        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }

        // $orderDetails = OrderDetails::with(['orders','products','productdetails','orders.buyers'])->get();

        $orderDetails = OrderDetails::with(['orders', 'orders.sellers', 'orders.buyers', 'orders.createdbyname', 'orders.getuserdetails.getdivision', 'orders.buyers.customeraddress.cityname', 'orders.buyers.customeraddress.statename', 'products', 'products.productpriceinfo', 'orders.getuserdetails.getbranch', 'orders.sellers.customeraddress.cityname', 'orders.createdbyname.getbranch']);

        if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
            $usersIds = User::where('id', $request->user_id)->where('sales_type', 'Secondary')->pluck('id');
        } else {
            $usersIds = User::with('attendance_details')->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
            $usersIds = User::where('branch_id', $request->branch_id)->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->division_id && $request->division_id != '' && $request->division_id != null) {
            $usersIds = User::where('division_id', $request->division_id)->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $orderDetails->whereHas('orders', function ($query) use ($financial_year_end) {
                $query->where('order_date', '>=', $financial_year_start)
                    ->where('order_date', '<=', $financial_year_end);
            });
        }


        if ($request->month && $request->month != '' && $request->month != null) {
            $query->where('month', $request->month);
        }

        // user with secondary sales type
        $employeeIds = EmployeeDetail::whereIn('user_id', $usersIds)->pluck('customer_id');
        $customerIds = Customers::whereIn('id', $employeeIds)->pluck('id');

        $registeredRetailerCount = $customerIds->count();

        $activeRetailerCount = $orderDetails->whereHas('orders', function ($query) use ($customerIds) {
            $query->whereIn('buyer_id', $customerIds);
        })->count();

        $activeRetailerPercent = 0;
        if ($registeredRetailerCount > 0) {
            $activeRetailerPercent = number_format((float)(($activeRetailerCount / $registeredRetailerCount) * 100), 2, '.', '');
        }

        $transactionHistory = TransactionHistory::with('customer')->get();

        $nosOfRetailerRegistredSaarthi = $transactionHistory->whereIn('customer_id', $customerIds)->count();

        $nosOfRetailerRegistredSaarthiPercent = 0;
        if ($registeredRetailerCount > 0) {
            $nosOfRetailerRegistredSaarthiPercent =  number_format((float)(($nosOfRetailerRegistredSaarthi / $registeredRetailerCount) * 100), 2, '.', '');
        }

        $orderTarget = SalesTargetUsers::where('type', 'secondary')->whereIn('user_id', $customerIds)->sum('target');

        $orderAchievement = $orderDetails->where(function ($query) use ($customerIds) {
            $query->whereHas('orders', function ($subquery) use ($customerIds) {
                $subquery->whereIn('buyer_id', $customerIds);
            });
        })->sum('line_total');

        $achievementPercent = 0;
        if ($orderTarget > 0) {
            $achievementPercent = number_format((float)(($orderAchievement / $orderTarget) * 100), 2, '.', '');
        }

        $attendanceCount = Attendance::whereIn('user_id', $usersIds)->groupBy('user_id')->count();
        $totalUsers = $usersIds->count();

        $avgWorkingDays = 0;
        if ($totalUsers > 0) {
            $avgWorkingDays = number_format((float)($attendanceCount / $totalUsers));
        }

        $totalOrders = $orderDetails->where(function ($query) use ($usersIds) {
            $query->whereHas('orders', function ($subquery) use ($usersIds) {
                $subquery->whereIn('buyer_id', $usersIds);
            });
        })->count();

        $averageVisits = VisitReport::whereIn('user_id', $usersIds)->count();


        $perDayAverageSales = ($avgWorkingDays > 0) ? number_format((float)($totalOrders / $avgWorkingDays)) : '0';
        $perDayAverageVisit = ($averageVisits > 0) ?  number_format((float)($totalOrders / $averageVisits)) : '0';
        $data['registeredRetailerCount'] = $registeredRetailerCount;
        $data['activeRetailerCount'] = $activeRetailerCount;
        $data['activeRetailerPercent'] = $activeRetailerPercent;
        $data['nosOfRetailerRegistredSaarthi'] = $nosOfRetailerRegistredSaarthi;
        $data['nosOfRetailerRegistredSaarthiPercent'] = $nosOfRetailerRegistredSaarthiPercent;

        $data['orderTarget'] = $orderTarget;
        $data['orderAchievement'] = $orderAchievement;
        $data['achievementPercent'] = $achievementPercent;
        $data['perDayAverageSales'] = $perDayAverageSales;
        $data['perDayAverageVisit'] = $perDayAverageVisit;

        return response()->json($data);
    }


    public function visitors(VisitorDataTable $dataTable)
    {
        abort_if(Gate::denies('visitor_log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('visitor.index');
    }

    public function dealer_dashboard(Request $request)
    {
        abort_if(Gate::denies('dealer_dashboard'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $dealer_poster_setting = DealerPortalSettings::first();
        $all_customer_ids = ParentDetail::where('parent_id', Auth::user()->customerid)->pluck('customer_id')->toArray();
        $all_customer_ids[] = Auth::user()->customerid;
        $today = now();
        $startOfQuarter = $today->copy()->startOfQuarter();
        $endOfQuarter = $today->copy()->endOfQuarter();
        $startOfFinancialYear = $today->month >= 4
            ? Carbon::create($today->year, 4, 1)
            : Carbon::create($today->year - 1, 4, 1);

        $salesSummary = [
            'month' => DB::table('primary_sales')
                ->whereMonth('invoice_date', $today->month)
                ->whereYear('invoice_date', $today->year)
                ->whereIn('customer_id', $all_customer_ids)
                ->sum('net_amount') / 100000,

            'quarter' => DB::table('primary_sales')
                ->whereBetween('invoice_date', [$startOfQuarter, $endOfQuarter])
                ->whereIn('customer_id', $all_customer_ids)
                ->sum('net_amount') / 100000,

            'financial_year' => DB::table('primary_sales')
                ->whereBetween('invoice_date', [$startOfFinancialYear, $today])
                ->whereIn('customer_id', $all_customer_ids)
                ->sum('net_amount') / 100000,
        ];

        return view('dashboard.dealer_dashboard', compact('dealer_poster_setting', 'salesSummary'));
    }
}
