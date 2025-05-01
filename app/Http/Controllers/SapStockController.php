<?php

namespace App\Http\Controllers;

use App\DataTables\SAPStockDataTable;
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

class SapStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SAPStockDataTable $dataTable, Request $request)
    {

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
