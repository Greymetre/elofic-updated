<?php

namespace App\Http\Controllers;

use App\DataTables\SAPStockDataTable;
use App\Models\Category;
use App\Models\SapStock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PlannedSOP;
use App\Models\PlannedSopSaleData;
use App\Models\PrimarySales;
use App\Models\ProductDetails;
use Gate;

use App\Models\Customers;
use App\Models\DealerAppointment;
use App\Models\DealerAppointmentKyc;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Laravel\Passport\Token;
use Auth;

class SapStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SAPStockDataTable $dataTable, Request $request)
    {

        // $path = '/var/www/html/new_psop.xlsx';

        // if (!file_exists($path)) {
        //     return 'File not found!';
        // }

        // Excel::import(new class implements ToCollection {
        //     public function collection(Collection $rows)
        //     {
        //         foreach ($rows as $index => $row) {
        //             if ($index == 0) continue;
        //             $data = [
        //                 'product_id' => $row[1],
        //                 'branch_id'  => $row[2],
        //                 'date'       => 'May 2025',
        //             ];

        //             // create a new Request instance with that data
        //             $new_request = new Request($data);

        //             $ajaxController = new AjaxController();

        //             $other_details = $ajaxController->getFullDetailsOfProduct($new_request);
        //             $dataObject = $other_details->getData();

        //             $dataArray = json_decode(json_encode($dataObject), true);

        //             $row[0] = 'May 2025';
        //             if (isset($row[0])) {
        //                 $formatted_date = Carbon::createFromFormat('F Y', $row[0])->startOfMonth();
        //                 $for_oder_id = $formatted_date->format("F/Y");
        //                 $planning_month = $formatted_date->format("Y-m-d");
        //             }
        //             $division = Category::find($row[3]);
        //             $total = PlannedSOP::latest('id')->value('id');
        //             $formattedTotal = str_pad($total, 3, '0', STR_PAD_LEFT);
        //             $order_id = strtoupper(substr($division->category_name, 0, 3)) . '/' . $for_oder_id . '/' . $formattedTotal;

        //             $openingStock = $dataArray['opening_stock']['opening_stocks'] ?? 0;
        //             $planNextMonth = $dataArray['branchOprningQuantity']['plan_next_month'] ?? 0;

        //             $pro_qty = $row[4] - ($openingStock - $planNextMonth);
        //             $plannedsop = PlannedSOP::updateOrCreate(
        //                 [
        //                     'planning_month' => $planning_month,
        //                     'product_id'     => $row[1] ?? '',
        //                     'branch_id'      => $row[2] ?? '',
        //                 ],
        //                 [
        //                     'plan_next_month' => $row[4] ?? '',
        //                     'order_id'             => $order_id ?? '',
        //                     'division_id'          => $row[3] ?? Null,
        //                     'opening_stock'        => $openingStock ?? NULL,
        //                     'open_order_qty'       => $planNextMonth ?? Null,
        //                     'production_qty'       => $pro_qty ?? 0,
        //                     'budget_for_month'     => NULL,
        //                     'last_month_sale'      => NULL,
        //                     'last_three_month_avg' => $dataArray['threeMonthAvg'] ?? NULL,
        //                     'last_year_month_sale' => $dataArray['sameMonthLastYearSales'] ?? NULL,
        //                     'sku_unit_price'       => $dataArray['product']['productdetails'][0]['price'] ?? NULL,
        //                     's_op_val'             => NULL,
        //                     'top_sku'              => NULL,
        //                     'created_by'           => Auth::user()->name ?? NULL,
        //                     'view_only'            => '10',
        //                     'plan_next_month_value' => $row[4] * $dataArray['product']['productdetails'][0]['price'],
        //                     'status'               => 1,
        //                 ]
        //             );

        //             $collection = collect($dataArray['sales_by_month'])->map(fn($value) => (int) $value);

        //             $max = $collection->max();
        //             $min = $collection->min();
        //             $avg = $collection->avg();

        //             PlannedSopSaleData::updateOrCreate(
        //                 ['planned_sop_id' => $plannedsop->id],
        //                 [
        //                     'planned_sop_id',
        //                     'month_1' => $dataArray['sales_by_month']['2024-04'],
        //                     'month_2' => $dataArray['sales_by_month']['2024-05'],
        //                     'month_3'  => $dataArray['sales_by_month']['2024-06'],
        //                     'month_4' => $dataArray['sales_by_month']['2024-07'],
        //                     'month_5' => $dataArray['sales_by_month']['2024-08'],
        //                     'month_6' => $dataArray['sales_by_month']['2024-09'],
        //                     'month_7' => $dataArray['sales_by_month']['2024-10'],
        //                     'month_8' => $dataArray['sales_by_month']['2024-11'],
        //                     'month_9' => $dataArray['sales_by_month']['2024-12'],
        //                     'month_10' => $dataArray['sales_by_month']['2025-01'],
        //                     'month_11' => $dataArray['sales_by_month']['2025-02'],
        //                     'month_12' => $dataArray['sales_by_month']['2025-03'],
        //                     'min'      => $min,
        //                     'max'      => $max,
        //                     'avg'      => $avg,
        //                 ]
        //             );
        //         }
        //     }
        // }, $path);
        // dd('done');



        // $all_customers = Customers::where(['active' => 'Y', 'customertype' => '4'])->get();
        // foreach ($all_customers as $customer) {
        //     $passis = generatePassword();
        //     if (strlen($customer['mobile']) > 10 && substr($customer['mobile'], 0, 2) === '91') {
        //         $customer['mobile'] = substr($customer['mobile'], 2);
        //     }
        //     if($customer['mobile'] == '+ 9198395938' || $customer['mobile'] == '9198395938'){
        //         dd(User::where('mobile', $customer['mobile'])->exists(), $customer['mobile']);
        //     }
        //     if (!User::where('mobile', $customer['mobile'])->exists()) {
        //         $user = User::create([
        //             'active'   =>  isset($customer['active']) ? $customer['active'] : 'Y',
        //             'name'   =>  isset($customer['name']) ? $customer['name'] : $customer['first_name'] . ' ' . $customer['last_name'],
        //             'first_name'   =>  isset($customer['first_name']) ? $customer['first_name'] : '',
        //             'last_name'   =>  isset($customer['last_name']) ? $customer['last_name'] : '',
        //             'mobile'   =>  isset($customer['mobile']) ? $customer['mobile'] : null,
        //             'email'   =>  isset($customer['email']) ? $customer['email'] : 'customer' . $customer->id . '@gmail.com',
        //             'password'   =>  Hash::make($passis),
        //             'reportingid' => !empty($customer['created_by']) ? $customer['created_by'] : null,
        //             'password_string'   =>  $passis,
        //             'customerid' => $customer->id,
        //         ]);
        //         $user->roles()->sync(['40']);
        //         $permissions = $user->getPermissionsViaRoles()->pluck('name');
        //         $user->givePermissionTo($permissions);
        //     }
        // }

        // dd($all_customers);

        //     // Revoke all tokens for this customer
        //     $tokens = Token::where('user_id', $customer->id)
        //         ->where('revoked', false)
        //         ->get();

        //     foreach ($tokens as $token) {
        //         $token->revoke();
        //     }

        //     echo "Revoked tokens for Customer ID: " . $customer->id . '<br>';
        // }

        // dd('Testing');

        // $path = '/var/www/html/New dealer importnn.xlsx';

        // if (!file_exists($path)) {
        //     return 'File not found!';
        // }

        // Excel::import(new class implements ToCollection {
        //     public function collection(Collection $rows)
        //     {
        //         foreach ($rows as $index => $row) {
        //             if ($index > 0) {
        //                 if (isset($row[0]) && is_numeric($row[0])) {
        //                     $excelDate = $row[0] - 25569; // Adjust for Excel's epoch
        //                     $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
        //                     $row[0] = !empty($row[0]) ? Carbon::createFromTimestamp($unixTimestamp)->toDateString() : '';
        //                 }
        //                 if (!empty($row[1]) && $row[1] != '=#N/A') {
        //                     $new_app = DealerAppointment::create([
        //                         'appointment_date' => isset($row[0]) ? $row[0] : '',
        //                         'division' => isset($row[2]) ? $row[2] : '',
        //                         'branch' => isset($row[3]) ? $row[3] : '',
        //                         'customertype' => isset($row[4]) ? $row[4] : '',
        //                         'firm_name' => isset($row[5]) ? $row[5] : '',
        //                         'district' => isset($row[6]) ? $row[6] : '',
        //                         'city' => isset($row[7]) ? $row[7] : '',
        //                         'approval_status' => 3,    
        //                     ]);

        //                     if ($new_app) {
        //                         $new_app->save();
        //                         DealerAppointmentKyc::create([
        //                             'appointment_id' => $new_app->id,
        //                             'dealer_code' => isset($row[1]) ? $row[1] : '',
        //                         ]);
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }, $path);

        // dd('Import completed!');

        abort_if(Gate::denies('sap_stock_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('sap_stock.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function show(SapStock $sapStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function edit(SapStock $sapStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SapStock $sapStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SapStock  $sapStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(SapStock $sapStock)
    {
        //
    }
}
