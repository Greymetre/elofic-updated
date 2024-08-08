<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PrimarySales;
use Validator;
use DB;

class ReportController extends Controller
{
    public function __construct()
    {
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

    public function primarySales(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $perPage = $request->per_page ??  5;
            $user_employee_codes = $user->employee_codes ?? '';

            $user_ids = getUsersReportingToAuth($user->id);

            $all_employee_code = User::whereIn('id', $user_ids)->pluck('employee_codes');

            // Get unique dealers and branches
            if (isset($user_employee_codes) && $user_employee_codes != "Greymetre Test") {
                $all_users = PrimarySales::select('dealer', 'id')->where('emp_code', $user_employee_codes)->latest()->get()->unique('dealer');
            } else {
                $all_users = PrimarySales::select('dealer', 'id')->latest()->get()->unique('dealer');
            }

            $all_branches = PrimarySales::select('final_branch', 'id')->latest()->get()->unique('final_branch');
            $users = $all_users->values()->toArray();
            $branches = $all_branches->values()->toArray();
            $currentYear = Carbon::now()->year;
            $years = range($currentYear, $currentYear + 1);
            $year_range = collect([]);
            foreach ($years as $key => $year) {
                $year_range->push([
                    'range' => ($year - 1) . '-' . $year,
                ]);
            }
            // Determine the current year and current date based on the financial year from the request
            if ($request->has('financial_year') && $request->financial_year != '') {
                $f_year_array = explode('-', $request->financial_year);
                $currentYear = (int)$f_year_array[0];
                if ($currentYear == Carbon::now()->year) {
                    $currentYear = Carbon::now()->year;
                    $currentDate = Carbon::now();
                } else {
                    $currentDate = Carbon::createFromFormat('Y-m-d', (int)$f_year_array[1] . '-03-31');
                }
            } else {
                $currentYear = Carbon::now()->year;
                $currentDate = Carbon::now();
            }

            // Calculate date ranges based on the current year and date
            $currentMonthStart = $currentDate->copy()->startOfMonth();
            $currentMonthEnd = $currentDate;
            // $lastMonthStart = $currentDate->copy()->subMonth()->startOfMonth();
            // $lastMonthEnd = $currentDate->copy()->subMonth()->endOfMonth();
            $sameMonthLastYear = Carbon::now()->subYear();
            $lastMonthStart = $sameMonthLastYear->copy()->startOfMonth()->toDateString();
            $lastMonthEnd = $sameMonthLastYear->copy()->endOfMonth()->toDateString();

            // Last year's range
            $lastYearStart = Carbon::create($currentYear - 1, 4, 1);
            $lastYearEnd = Carbon::create($currentYear, 3, 31);

            // Current year's range
            $currentYearStart = Carbon::create($currentYear, 4, 1);

            
            // Query for all the sales
            $query = PrimarySales::query();
            if (isset($user_employee_codes) && $user_employee_codes != "Greymetre Test") {
                $query->whereIn('emp_code', $all_employee_code);
            }
            if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                $query->where('final_branch', $request->branch_id);
            }
            if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
                $query->where('dealer', 'like', '%' . $request->dealer_id . '%');
            }

            if ($request->division_id && $request->division_id != '' && $request->division_id != null) {
                $query->where('division', 'like', '%' . $request->division_id . '%');
            }
            
            // Function to get sales data for a period
            $getSalesData = function ($query, $startDate, $endDate, $perPage) {
                return $query->clone()
                ->whereDate('invoice_date', '>=', $startDate)
                ->whereDate('invoice_date', '<=', $endDate)
                ->select('dealer')
                ->selectRaw('SUM(net_amount) as total_net_amount')
                ->selectRaw('SUM(quantity) as total_quantity')
                ->groupBy('dealer')
                ->paginate($perPage);
            };
            
            // Get sales data for each period with pagination
            $lastYearSales = $getSalesData($query, $lastYearStart, $lastYearEnd, $perPage);
            $currentYearSales = $getSalesData($query, $currentYearStart, $currentDate, $perPage);
            $lastMonthSales = $getSalesData($query, $lastMonthStart, $lastMonthEnd, $perPage);
            $currentMonthSales = $getSalesData($query, $currentMonthStart, $currentMonthEnd, $perPage);

            // Combine all results into a single array
            $salesData = [];
            $dealers = array_unique(array_merge(
                $lastYearSales->pluck('dealer')->toArray(),
                $currentYearSales->pluck('dealer')->toArray(),
                $lastMonthSales->pluck('dealer')->toArray(),
                $currentMonthSales->pluck('dealer')->toArray()
            ));

            foreach ($dealers as $dealer) {
                $salesData[] = [
                    'dealer' => $dealer,
                    'total_net_amount_last_year' => isset($lastYearSales->firstWhere('dealer', $dealer)->total_net_amount) ? number_format(($lastYearSales->firstWhere('dealer', $dealer)->total_net_amount / 100000), 2, '.', '')  : "",
                    'total_quantity_last_year' => $lastYearSales->firstWhere('dealer', $dealer)->total_quantity ?? "",
                    'total_net_amount_current_year' =>  isset($currentYearSales->firstWhere('dealer', $dealer)->total_net_amount) ? number_format(($currentYearSales->firstWhere('dealer', $dealer)->total_net_amount / 100000), 2, '.', '')  :  "",
                    'total_quantity_current_year' => $currentYearSales->firstWhere('dealer', $dealer)->total_quantity ?? "",
                    'total_net_amount_last_month' =>  isset($lastMonthSales->firstWhere('dealer', $dealer)->total_net_amount) ? number_format(($lastMonthSales->firstWhere('dealer', $dealer)->total_net_amount / 100000), 2, '.', '')  :   "",
                    'total_quantity_last_month' => $lastMonthSales->firstWhere('dealer', $dealer)->total_quantity ?? "",
                    'total_net_amount_current_month' =>  isset($currentMonthSales->firstWhere('dealer', $dealer)->total_net_amount) ? number_format(($currentMonthSales->firstWhere('dealer', $dealer)->total_net_amount / 100000), 2, '.', '')  :   "",
                    'total_quantity_current_month' => $currentMonthSales->firstWhere('dealer', $dealer)->total_quantity ?? "",
                ];
            }

            $pagination =  [
                'total' => $lastYearSales->total(),
                'per_page' => $lastYearSales->perPage(),
                'current_page' => $lastYearSales->currentPage(),
                'last_page' => $lastYearSales->lastPage(),
                'from' => $lastYearSales->firstItem(),
                'to' => $lastYearSales->lastItem(),
                'request->dealer_id' => $request->branch_id ?? ''
            ];

            $ps_divisions = PrimarySales::distinct()->pluck('division');
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $salesData, 'users' => $users, 'branches' => $branches,  'year_rang' => $year_range, 'currentYear' => $currentYear,'ps_divisions'=>$ps_divisions, 'pagination' => $pagination], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function monthlySales(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'financial_year'  => 'required',
            'dealer_id'  => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->messages()->all()], $this->badrequest);
        }
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $query = PrimarySales::select(
            'dealer',
            'final_branch',
            'city',
            DB::raw('SUM(net_amount) as total_net_amounts'),
            DB::raw('GROUP_CONCAT(net_amount) as net_amounts'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(invoice_date) as invoice_dates'),
        );

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);
            $months = [];

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $startDate = Carbon::createFromFormat('Y-m-d', $financial_year_start);
            $endDate = Carbon::createFromFormat('Y-m-d', $financial_year_end);
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $monthName = $currentDate->format('F');
                if (!in_array($monthName, $months)) {
                    $months[] = $monthName;
                }
                $currentDate->addMonth()->startOfMonth();
            }
            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        }

        $db_data = $query->where('dealer', 'like', '%' . $request->dealer_id . '%')->groupBy('dealer', 'final_branch', 'city')->first();

        $response = array();
        $invoice_dates = explode(',', $db_data->invoice_dates);
        $net_amounts = explode(',', $db_data->net_amounts);

        foreach ($months as $k => $val) {
            $tsale = 0;
            foreach ($invoice_dates as $key => $value) {
                $invDate = Carbon::createFromFormat('Y-m-d', $value);
                $currentDate = $invDate->copy();
                $monthName = $currentDate->format('F');
                if ($monthName == $val) {
                    $tsale += $net_amounts[$key];
                }
            }
            if ($tsale > 0) {
                $response[$val] = number_format(($tsale / 100000), 2, '.', '');
            } else {
                $response[$val] = "0.0";
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $response], $this->successStatus);

        dd($response);
    }
}
