<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendPerEmployeeCosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:per-employee-costing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Per Employee Costing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertCustomerDetails";
        $today = Carbon::now('Asia/Kolkata');

        // Get current and previous financial years
        $currentFYStart = Carbon::create($today->year - ($today->month < 4 ? 1 : 0), 4, 1);
        $currentFYEnd   = Carbon::create($currentFYStart->year + 1, 3, 31);
        $previousFYStart = $currentFYStart->copy()->subYear();
        $previousFYEnd   = $currentFYEnd->copy()->subYear();

        // Adjust current FY end if it's in the future
        if ($currentFYEnd->greaterThan($today)) {
            $currentFYEnd = $today;
        }

        $users_previous_fy = $this->processUsersForPeriod($previousFYStart, $previousFYEnd);
        $users_current_fy  = $this->processUsersForPeriod($currentFYStart, $currentFYEnd);


        dd($users_current_fy->count(), $users_previous_fy->count());

        // $formatteddata = $this->formatData($users_current_fy, $users_previous_fy);


        // // Send data in chunks
        // $formatteddata->chunk(200)->each(function ($chunk) use ($url) {
        //     $payload = ['employee_costing' => $chunk->toArray()];
        //     $response = Http::timeout(240)->post($url, $payload);

        //     if ($response->successful()) {
        //         $this->info(count($chunk) . ' records sent successfully.');
        //     } else {
        //         $this->error('Failed to send sales data: ' . $response->body());
        //     }
        // });

    }

    function processUsersForPeriod($startDate, $endDate)
    {
        $startDateFormatted = $startDate->toDateString();
        $endDateFormatted = $endDate->toDateString();
        $all_months = getMonthsBetween($startDate, $endDate);

        $query = User::with([
            'primarySales:id,emp_code,invoice_date,net_amount',
            'getdesignation',
            'getbranch',
            'getdivision',
            'userinfo',
            'expenses'
        ])
            ->where('active', 'Y')
            ->whereHas('roles', function ($q) {
                $q->whereIn('id', ['13', '6', '3', '2']);
            });

        $users = $query->get();

        // Prepare per-user calculations
        foreach ($users as $user) {
            $user->userinfo->gross_salary_monthly *= count($all_months);

            $expensesSum = $user->expenses
                ->whereBetween('date', [$startDateFormatted, $endDateFormatted])
                ->sum('claim_amount');

            $user->total_expe = $expensesSum + $user->userinfo->gross_salary_monthly;

            if ($user->sales_type === 'Primary') {
                $salesSum = $user->primarySales
                    ->whereBetween('invoice_date', [$startDateFormatted, $endDateFormatted])
                    ->sum('net_amount');
            } else {
                $salesSum = Order::where('created_by', $user->id)
                    ->whereBetween('order_date', [$startDateFormatted, $endDateFormatted])
                    ->sum('sub_total');
            }

            $user->sales = $salesSum > 0 ? number_format($salesSum / 100000, 2) : 0;

            $user->sal_exp = $user->sales > 0
                ? number_format(($user->total_expe / 100000) / $user->sales * 100, 2)
                : 0;
        }

        return $users;
    }
}
