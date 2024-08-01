<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PrimarySales;

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

    public function primarySales(Request $request){
         try{
            $user = $request->user();
            $user_id = $user->id;
            $perPage = $request->per_page ??  5;
            $user_employee_codes = $user->employee_codes ?? '';
        
            // Get unique dealers and branches
            if (isset($user_employee_codes) && $user_employee_codes != "Greymetre Test") {
                $all_users = PrimarySales::select('dealer', 'id')->where('emp_code', $user_employee_codes)->latest()->get()->unique('dealer');
            }else{
                $all_users = PrimarySales::select('dealer', 'id')->latest()->get()->unique('dealer');
            }
           
            $all_branches = PrimarySales::select('final_branch', 'id')->latest()->get()->unique('final_branch');
            $users = $all_users->values()->toArray();
            $branches = $all_branches->values()->toArray();
            $currentYear = Carbon::now()->year;
            $years = range($currentYear , $currentYear + 1);
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
                if($currentYear == Carbon::now()->year){
                    $currentYear = Carbon::now()->year;
                    $currentDate = Carbon::now();
                }else{
                    $currentDate = Carbon::createFromFormat('Y-m-d', $currentYear . '-04-01');
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
                $query->where('emp_code', $user_employee_codes);
            }
            if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                $query->where('final_branch', $request->branch_id );
            }
            if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
                $query->where('dealer', 'like', '%' . $request->dealer_id . '%');
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
                    'total_net_amount_last_year' => isset($lastYearSales->firstWhere('dealer', $dealer)->total_net_amount) ? number_format(($lastYearSales->firstWhere('dealer', $dealer)->total_net_amount/100000),2,'.','')  : "",
                    'total_quantity_last_year' => $lastYearSales->firstWhere('dealer', $dealer)->total_quantity ?? "",
                    'total_net_amount_current_year' =>  isset($currentYearSales->firstWhere('dealer', $dealer)->total_net_amount) ? number_format(($currentYearSales->firstWhere('dealer', $dealer)->total_net_amount/100000),2,'.','')  :  "",
                    'total_quantity_current_year' => $currentYearSales->firstWhere('dealer', $dealer)->total_quantity ?? "",
                    'total_net_amount_last_month' =>  isset($lastMonthSales->firstWhere('dealer', $dealer)->total_net_amount) ? number_format(($lastMonthSales->firstWhere('dealer', $dealer)->total_net_amount/100000),2,'.','')  :   "",
                    'total_quantity_last_month' => $lastMonthSales->firstWhere('dealer', $dealer)->total_quantity ?? "",
                    'total_net_amount_current_month' =>  isset($currentMonthSales->firstWhere('dealer', $dealer)->total_net_amount) ? number_format(($currentMonthSales->firstWhere('dealer', $dealer)->total_net_amount/100000),2,'.','')  :   "",
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
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $salesData , 'users' => $users , 'branches' => $branches ,  'year_rang' => $year_range , 'currentYear' => $currentYear , 'pagination' => $pagination], $this->successStatus);
             
         }catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
         }
    }
}
