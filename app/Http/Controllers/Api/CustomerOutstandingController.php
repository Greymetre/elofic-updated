<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerOutstanting;
use App\Models\MasterDistributor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerOutstandingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'posting_date' => 'required',
            'customer_code' => 'required',
            'customer_name' => 'required|string',
            'reference' => 'required|string',
            'due_date' => 'nullable',
            'payment_term' => 'nullable|string',
            'ageingInfo' => 'required|array|min:1',
            'ageingInfo.*.day' => 'required|string',
            'ageingInfo.*.amount' => 'required|numeric'
        ]);

        DB::beginTransaction();

        try {

            $distributor = MasterDistributor::where(
                'distributor_code',
                $request->customer_code
            )->first();

            if (!$distributor) {
                return response()->json([
                    'status' => false,
                    'message' => 'Distributor not found.'
                ], 404);
            }

            $records = [];
            $postingDate = Carbon::createFromFormat(
                'd-m-Y',
                $request->posting_date
            );
            
            $dueDate = $request->filled('due_date')
                ? Carbon::createFromFormat('d-m-Y', $request->due_date)
                : $postingDate->copy()->addDays(7);

            foreach ($request->ageingInfo as $item) {

                $records[] = [
                    'posting_date' => Carbon::createFromFormat(
                        'd-m-Y',
                        $request->posting_date
                    )->format('Y-m-d'),

                    'customer_code' => $request->customer_code,
                    'customer_name' => $request->customer_name,
                    'customer_id' => $distributor->id,
                    'reference' => $request->reference,

                    'due_date' => $dueDate->format('Y-m-d'),

                    'payment_term' => $request->payment_term,
                    'days' => $item['day'],
                    'amount' => $item['amount'],

                    'year' => now()->year,
                    'quarter' => ceil(now()->month / 3),

                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            CustomerOutstanting::insert($records);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Outstanding records created successfully.',
                'count' => count($records)
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Error creating records.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}