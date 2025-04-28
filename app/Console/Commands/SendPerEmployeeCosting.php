<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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
        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertEmployeeCosting";
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

        // Fetch users for both periods with a flag for identification
        $users = collect();

        $users_previous = $this->processUsersForPeriod($previousFYStart, $previousFYEnd)
            ->map(function ($user) use ($previousFYStart) {
                $user->f_year = $previousFYStart->year . '-' . ($previousFYStart->year + 1);
                return $user;
            });

        $users_current = $this->processUsersForPeriod($currentFYStart, $currentFYEnd)
            ->map(function ($user) use ($currentFYStart) {
                $user->f_year = $currentFYStart->year . '-' . ($currentFYStart->year + 1);
                return $user;
            });

        $users = $users->merge($users_previous)->merge($users_current);

        $formatteddata = $users->map(function ($user) {
            $manager = User::where('division_id', $user->division_id)->where('active', 'Y')
                        ->whereRaw('FIND_IN_SET(?, branch_id)', [$user->branch_id])
                        ->whereHas('roles', function ($query) {
                            $query->whereIn('name', [
                                'PUMPCH',
                                'AGRIGM/CH/ZM/RM/SH',
                                'FAN/CH/GM/SH'
                            ]);
                        })
                        ->first();
            return [
                'f_year' => $user->f_year,
                'division' => $user->getdivision?->division_name,
                'branch' => $user->getbranch?->branch_name ?? 'Not Applicable',
                'branch_cluster' => $manager->name ?? 'Anil Srivastava',
                'emp_code' => $user->employee_codes,
                'emp_name' => $user->name,
                'designation' => $user->getdesignation->designation_name,
                'doj' => $user->userinfo->date_of_joining,
                'sales' => $user->sales,
                'salary' => $user->userinfo->gross_salary_monthly,
                'ta_da' => $user->expensesSum,
                'incentive' => '0',
                'total_exp' => $user->total_expe,
                'sal_exp_per' => $user->sal_exp,
            ];
        });

        // Send data in chunks
        $formatteddata->chunk(100)->each(function ($chunk) use ($url) {
            $payload = ['employee_costing' => $chunk->toArray()];
            $response = Http::timeout(240)->post($url, $payload);

            if ($response->successful()) {
                $this->info(count($chunk) . ' records sent successfully.');
            } else {
                $this->error('Failed to send sales data: ' . $response->body());
            }
        });
    }

    function processUsersForPeriod($startDate, $endDate)
    {
        $startDateFormatted = $startDate->toDateString();
        $endDateFormatted = $endDate->toDateString();
        $all_months = getMonthsBetween($startDate, $endDate);

        $query = User::with([
            'primarySales:id,emp_code,invoice_date,net_amount',
            'getbranch',
            'getdesignation',
            'getdivision',
            'userinfo',
            'expenses'
        ])
            ->where('active', 'Y')
            ->where('sales_type', 'Primary')
            ->where('designation_id', '1');
            // ->whereHas('roles', function ($q) {
            //     $q->whereIn('id', ['13', '6', '3', '2']);
            // });

        $users = $query->get();

        // Prepare per-user calculations
        foreach ($users as $user) {
            $user->userinfo->gross_salary_monthly *= count($all_months);

            $user->expensesSum = $user->expenses
                ->whereBetween('date', [$startDateFormatted, $endDateFormatted])
                ->sum('claim_amount');

            $user->total_expe = $user->expensesSum + $user->userinfo->gross_salary_monthly;

            if ($user->sales_type == 'Primary') {
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
