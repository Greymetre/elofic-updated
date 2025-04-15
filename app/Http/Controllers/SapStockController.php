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
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class SapStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SAPStockDataTable $dataTable, Request $request)
    {
        // $path = '/var/www/html/New dealer import AGRI.xlsx';

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
