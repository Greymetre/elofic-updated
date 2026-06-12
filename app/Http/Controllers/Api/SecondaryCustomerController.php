<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SecondaryCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SecondaryCustomersExport;
use App\Exports\SecondaryCustomersTemplateExport;
use Illuminate\Validation\Rule;   // ← this line is MISSING or commented out
use App\Models\User;
class SecondaryCustomerController extends Controller
{
    
    private function getVisibleUserIds(User $user): array
    {
        $allIds = [$user->id];           // include myself
    
        $this->collectDownlineIds($user->id, $allIds);
    
        return array_unique($allIds);
    }
    
    /**
     * Recursively collect all user IDs in the downline
     */
    private function collectDownlineIds(int $managerId, array &$ids): void
    {
        $directReports = User::where('reportingid', $managerId)
            ->pluck('id')
            ->toArray();
    
        if (empty($directReports)) {
            return;
        }
    
        foreach ($directReports as $reportId) {
            if (!in_array($reportId, $ids)) {   // prevent potential cycles (rare)
                $ids[] = $reportId;
                $this->collectDownlineIds($reportId, $ids);
            }
        }
    }
    
    public function index(Request $request)
    {
        $type = $request->query('type');

        if (!$type || !in_array($type, ['RETAILER', 'WORKSHOP', 'MECHANIC', 'GARAGE'])) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or missing type parameter.',
            ], 400);
        }

        try {
            $authUser = $request->user();

            if (!$authUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated - please provide valid token',
                ], 401);
            }
            

            $today = now()->startOfDay()->toDateString(); // e.g. '2026-02-25'
            // ────────────────────────────────────────────────
            // SUPERADMIN CHECK (Only change is here)
            // ────────────────────────────────────────────────
            $isSuperAdmin = false;
    
            // First try: Spatie hasRole (most accurate)
            if (method_exists($authUser, 'hasRole')) {
                $isSuperAdmin = $authUser->hasRole('superadmin');
            }
    
            // Fallback: Check roles relation if loaded
            if (!$isSuperAdmin && $authUser->relationLoaded('roles')) {
                $isSuperAdmin = $authUser->roles->pluck('name')->contains('superadmin');
            }
    
            // Final fallback: Check user_type (as sent in login response)
            if (!$isSuperAdmin && !empty($authUser->user_type)) {
                $userTypes = $authUser->user_type;
                if (is_string($userTypes)) {
                    $userTypes = json_decode($userTypes, true) ?? [];
                }
                $isSuperAdmin = in_array('superadmin', (array)$userTypes, true);
            }
        
    
            if ($isSuperAdmin) {
                $query = SecondaryCustomer::with([
                    'country', 'state', 'district', 'city', 'pincode', 'beat', 'distributor'
                ])
                ->where('type', $type)
                ->where('active', 'Y')
                ->select('secondary_customers.*');
    
                // If for_user_id is provided → show only that user's customers
                if ($request->filled('for_user_id')) {
                    $targetUserId = $request->for_user_id;
    
                    $targetUser = User::find($targetUserId);
                    if (!$targetUser) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Requested user not found',
                        ], 404);
                    }
    
                    $query->where(function ($q) use ($targetUserId) {
                        $q->where('created_by', $targetUserId)
                          ->orWhere('employee_id', $targetUserId);
                    });
                }
                // Else → Superadmin sees ALL customers (no restriction)
            } else {
                // ────────────────────────────────────────────────
                // Normal User / Hierarchy Logic (Existing)
                // ────────────────────────────────────────────────
                $targetUserId = $request->query('for_user_id');
    
                if ($targetUserId) {
                    $targetUser = User::find($targetUserId);
                    if (!$targetUser) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Requested user not found',
                        ], 404);
                    }
    
                    $myVisibleIds = $this->getVisibleUserIds($authUser);
                    $myVisibleIds[] = $authUser->id;
                    $myVisibleIds = array_unique($myVisibleIds);
    
                    if (!in_array($targetUserId, $myVisibleIds)) {
                        return response()->json([
                            'status' => false,
                            'message' => 'You do not have permission to view this user\'s customers',
                        ], 403);
                    }
    
                    $visibleUserIds = [$targetUserId];
                } else {
                    $visibleUserIds = $this->getVisibleUserIds($authUser);
                    $visibleUserIds[] = $authUser->id;
                    $visibleUserIds = array_unique($visibleUserIds);
                }
    
                $query = SecondaryCustomer::with([
                    'country', 'state', 'district', 'city', 'pincode', 'beat', 'distributor'
                ])
                ->where('type', $type)
                ->where('active', 'Y')
                ->where(function ($q) use ($visibleUserIds) {
                    $q->whereIn('created_by', $visibleUserIds)
                      ->orWhereIn('employee_id', $visibleUserIds);
                })
                ->select('secondary_customers.*');
            }

            // Global search
            if ($request->filled('global_search')) {
                $search = $request->global_search;
                $query->where(function ($q) use ($search) {
                    $q->where('owner_name', 'like', "%{$search}%")
                    ->orWhere('shop_name', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%");
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('city_name')) {
                $query->whereHas('city', function ($q) use ($request) {
                    $q->where('city_name', 'like', '%' . $request->city_name . '%');
                });
            }

            // Individual filters
            if ($request->filled('owner_name')) {
                $query->where('owner_name', 'like', "%{$request->owner_name}%");
            }
            if ($request->filled('shop_name')) {
                $query->where('shop_name', 'like', "%{$request->shop_name}%");
            }
            if ($request->filled('mobile')) {
                $query->where('mobile_number', 'like', "%{$request->mobile}%");
            }
            if ($request->filled('beat_id')) {
                $query->where('beat_id', $request->beat_id);
            }
            if ($request->filled('state_id')) {
                $query->where('state_id', $request->state_id);
            }
            if ($request->filled('city_id')) {
                $query->where('city_id', $request->city_id);
            }
            if ($request->filled('opportunity_status')) {
                $query->where('opportunity_status', $request->opportunity_status);
            }

            // Awareness status filter
            if ($request->filled('awareness_status')) {
                $status = $request->awareness_status === 'Done' ? 'Done' : 'Not Done';

                if (in_array($type, ['RETAILER', 'WORKSHOP'])) {
                    $query->where('nistha_awareness_status', $status);
                } else {
                    $query->where('saathi_awareness_status', $status);
                }
            }

            // ── Add check-in & check-out status (per logged-in user) ──
            $query->addSelect([
                // Check-in fields
                'last_checkin_date' => \App\Models\CheckIn::select('checkin_date')
                    ->whereColumn('entity_id', 'secondary_customers.id')
                    ->where('entity_type', 'secondary_customer')
                    ->where('user_id', $authUser->id)
                    ->orderByDesc('checkin_date')
                    ->orderByDesc('checkin_time')
                    ->limit(1),

                'last_checkin_time' => \App\Models\CheckIn::select('checkin_time')
                    ->whereColumn('entity_id', 'secondary_customers.id')
                    ->where('entity_type', 'secondary_customer')
                    ->where('user_id', $authUser->id)
                    ->orderByDesc('checkin_date')
                    ->orderByDesc('checkin_time')
                    ->limit(1),

                'has_checked_in_today' => \App\Models\CheckIn::selectRaw('IF(COUNT(*) > 0, 1, 0)')
                    ->whereColumn('entity_id', 'secondary_customers.id')
                    ->where('entity_type', 'secondary_customer')
                    ->where('user_id', $authUser->id)
                    ->whereDate('checkin_date', $today),

                // Check-out fields
                'last_checkout_date' => \App\Models\CheckIn::select('checkout_date')
                    ->whereColumn('entity_id', 'secondary_customers.id')
                    ->where('entity_type', 'secondary_customer')
                    ->where('user_id', $authUser->id)
                    ->whereNotNull('checkout_date')
                    ->orderByDesc('checkout_date')
                    ->orderByDesc('checkout_time')
                    ->limit(1),
                'current_visit_is_open' => \App\Models\CheckIn::selectRaw('IF(COUNT(*) > 0, 1, 0)')
                    ->whereColumn('entity_id', 'secondary_customers.id')
                    ->where('entity_type', 'secondary_customer')
                    ->where('user_id', $authUser->id)
                    ->whereNull('checkout_date')
                    ->whereDate('checkin_date', $today),

                'last_checkout_time' => \App\Models\CheckIn::select('checkout_time')
                    ->whereColumn('entity_id', 'secondary_customers.id')
                    ->where('entity_type', 'secondary_customer')
                    ->where('user_id', $authUser->id)
                    ->whereNotNull('checkout_date')
                    ->orderByDesc('checkout_date')
                    ->orderByDesc('checkout_time')
                    ->limit(1),

                'has_checked_out_today' => \App\Models\CheckIn::selectRaw('IF(COUNT(*) > 0, 1, 0)')
                    ->whereColumn('entity_id', 'secondary_customers.id')
                    ->where('entity_type', 'secondary_customer')
                    ->where('user_id', $authUser->id)
                    ->whereDate('checkout_date', $today),
                    
                'last_checkin_id' => \App\Models\CheckIn::select('id')
                    ->whereColumn('entity_id', 'secondary_customers.id')
                    ->where('entity_type', 'secondary_customer')
                    ->where('user_id', $authUser->id)
                    ->orderByDesc('checkin_date')
                    ->orderByDesc('checkin_time')
                    ->limit(1),
            ]);

            // Sort by created_at DESCENDING (newest first)
            $query->orderBy('created_at', 'desc');

            // Pagination (still using Laravel paginator, but we'll clean the output)
            $perPage   = $request->query('per_page', 10);
            $customers = $query->paginate($perPage);

            // Clean response - remove unwanted pagination link fields
            $cleanData = [
                'current_page' => $customers->currentPage(),
                'data'         => $customers->items(),           // only the records
                'from'         => $customers->firstItem(),
                'to'           => $customers->lastItem(),
                'per_page'     => $customers->perPage(),
                'total'        => $customers->total(),
                'last_page'    => $customers->lastPage(),
                // removed: links, first_page_url, last_page_url, next_page_url, prev_page_url, path
            ];

            return response()->json([
                'status'  => true,
                'message' => 'Secondary customers retrieved successfully',
                'data'    => $cleanData,
                
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch secondary customers',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    public function getUsedCities()
    {
        try {
            $cities = \App\Models\City::whereIn('id', function ($query) {
                    $query->select('city_id')
                          ->from('secondary_customers')
                          ->where('active', 'Y') // optional but recommended
                          ->whereNotNull('city_id')
                          ->distinct();
                })
                ->where('active', 'Y')
                ->select('id', 'city_name')
                ->orderBy('city_name')
                ->get();
    
            return response()->json([
                'status' => true,
                'message' => 'Cities retrieved successfully',
                'data' => $cities
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch cities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $authUser = $request->user();
            if (!$authUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated - please provide valid token',
                ], 401);
            }
    
            // Load Secondary Customer with relationships
            $customer = SecondaryCustomer::with([
                'country', 
                'state', 
                'district', 
                'city', 
                'pincode', 
                'beat', 
                'distributor'
            ])->find($id);
    
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found',
                ], 404);
            }
    
            // ────────────────────────────────────────────────
            // Get Order Statistics (Last Order Date, Total Value & Total Quantity)
            // ────────────────────────────────────────────────
            $orderStats = \App\Models\Order::where('buyer_id', $id)
                ->select([
                    \DB::raw('COUNT(*) as total_orders'),
                    \DB::raw('COALESCE(SUM(grand_total), 0) as total_order_value_from_grand'),
                    // Better approach: Calculate from order_details (more accurate)
                    \DB::raw('COALESCE((
                        SELECT SUM(quantity) 
                        FROM order_details 
                        WHERE order_details.order_id = orders.id
                    ), 0) as total_quantity'),
                    \DB::raw('MAX(created_at) as last_order_date')
                ])
                ->first();
    
            // ────────────────────────────────────────────────
            // Check-in / Check-out Data
            // ────────────────────────────────────────────────
            $today = now()->startOfDay()->toDateString();
    
            $checkInQuery = \App\Models\CheckIn::where('entity_type', 'secondary_customer')
                ->where('entity_id', $id)
                ->where('user_id', $authUser->id);
    
            $lastCheckIn = (clone $checkInQuery)
                ->orderByDesc('checkin_date')
                ->orderByDesc('checkin_time')
                ->first([
                    'id', 'checkin_date', 'checkin_time', 'checkin_address',
                    'checkout_date', 'checkout_time', 'checkout_address', 'time_interval'
                ]);
    
            $lastCheckOut = (clone $checkInQuery)
                ->whereNotNull('checkout_date')
                ->orderByDesc('checkout_date')
                ->orderByDesc('checkout_time')
                ->first(['checkout_date', 'checkout_time', 'checkout_address']);
    
            $hasCheckedInToday = (clone $checkInQuery)->whereDate('checkin_date', $today)->exists();
            $hasCheckedOutToday = (clone $checkInQuery)->whereDate('checkout_date', $today)->exists();
    
            $checkData = [
                'last_checkin' => $lastCheckIn ? [
                    'checkin_id'       => $lastCheckIn->id,
                    'checkin_datetime' => $lastCheckIn->checkin_date . ' ' . $lastCheckIn->checkin_time,
                    'checkin_address'  => $lastCheckIn->checkin_address,
                    'checkout_datetime'=> $lastCheckIn->checkout_date 
                                            ? $lastCheckIn->checkout_date . ' ' . $lastCheckIn->checkout_time 
                                            : null,
                    'checkout_address' => $lastCheckIn->checkout_address,
                    'duration'         => $lastCheckIn->time_interval ?? null,
                ] : null,
    
                'last_checkout' => $lastCheckOut ? [
                    'checkout_datetime' => $lastCheckOut->checkout_date . ' ' . $lastCheckOut->checkout_time,
                    'checkout_address'  => $lastCheckOut->checkout_address,
                ] : null,
    
                'today' => [
                    'has_checked_in'  => $hasCheckedInToday,
                    'has_checked_out' => $hasCheckedOutToday,
                ],
            ];
    
            $lastOrderDate = $orderStats->last_order_date 
                    ? \Carbon\Carbon::parse($orderStats->last_order_date)->format('Y-m-d H:i:s') 
                    : null;
            $totalOrderValue = \App\Models\Order::where('buyer_id', $id)
                ->whereHas('orderdetails') 
                ->withSum('orderdetails', 'line_total')   // This is cleanest way
                ->get()
                ->sum('orderdetails_sum_line_total');
                
            $orders = \App\Models\Order::where('buyer_id', $id)
                        ->with('orderdetails')           // Important: Load relationship
                        ->get();
    
            $total_quantity = 0;
            $total_order_value = 0;
    
            foreach ($orders as $order) {
                // Same logic as your getOrderList()
                $qty = $order->orderdetails->sum('quantity') ?? 0;
                $value = $order->orderdetails->sum('line_total') ?? 0;   // Better than grand_total
    
                $total_quantity += $qty;
                $total_order_value += $value;
            }
            // Order Summary
            $orderSummary = [
                'total_orders'      => $orders->count(),
                'total_order_value' => round($total_order_value, 2),
                'total_quantity'    => (int) $total_quantity,          // ← This will now be correct
                'last_order_date'   => $orders->max('created_at') 
                                        ? \Carbon\Carbon::parse($orders->max('created_at'))
                                            ->format('Y-m-d H:i:s')
                                        : null,
            ];
    
            return response()->json([
                'status'        => true,
                'message'       => 'Customer retrieved successfully',
                'data'          => $customer,
                'order_summary' => $orderSummary,     // ← Added as requested
                'check_status'  => $checkData,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to retrieve customer',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        ini_set('memory_limit', '1024M');

        \Log::info('Store request received', [
            'files_count' => count($request->allFiles()),
            'memory_start_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
        ]);

        $validated = $this->validateData($request);

        DB::beginTransaction();
        try {
            
            $validated['employee_id'] = $request->user()->id;

            // (optional but recommended)
            $validated['created_by'] = $request->user()->id;
            
            foreach (['owner_photo', 'shop_photo'] as $field) {
                if ($request->hasFile($field)) {
                    $validated[$field] = $this->uploadFile($request, $field);
                }
            }

            $customer = SecondaryCustomer::create($validated);
            
            
            
            // -------------------------
            
            
            
            $newData = $customer;
            
            logActivity(
                'secondary_customer',
                $customer->id,
                'created',
                'api', // 👈 important (API se create hua)
                null,
                $newData,
                $customer->type ?? null
            );




// ---------------------

            DB::commit();

            \Log::info('Store completed', [
                'customer_id' => $customer->id,
                'memory_peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2)
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Secondary customer created successfully',
                'data'    => $customer // no relations for now – add later if needed
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Store failed', [
                'error' => $e->getMessage(),
                'memory_peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status'  => false,
                'message' => 'Server error during creation',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $customer = SecondaryCustomer::findOrFail($id);

        $validated = $this->validateData($request, $id);

        DB::beginTransaction();
        try {
            foreach (['owner_photo', 'shop_photo'] as $file) {
                if ($request->hasFile($file) && $request->file($file)->isValid()) {
                    // Delete old file if exists
                    if ($customer->$file && Storage::disk('public')->exists($customer->$file)) {
                        Storage::disk('public')->delete($customer->$file);
                    }
                    $validated[$file] = $this->uploadFile($request, $file);
                }
            }
            
            // ---------------------
            
            // Get old values before update
$oldValues = $customer->only([
    'name',
    'mobile_number',
    'address',
    'type'
]);

// Update customer
$customer->update($validated);

// Refresh model
$customer->refresh();

// Get new values
$newValues = $customer->only([
    'name',
    'mobile_number',
    'address',
    'type'
]);

$changesOld = [];
$changesNew = [];

foreach ($oldValues as $field => $oldValue) {

    $newValue = $newValues[$field] ?? null;

    if ((string)$oldValue !== (string)$newValue) {

        $changesOld[$field] = $oldValue;
        $changesNew[$field] = $newValue;
    }
}

// Save log
if (!empty($changesNew)) {

    \Log::info('Saving activity log');

    logActivity(
        'secondary_customer',
        $customer->id,
        'updated',
        'api',
        $changesOld,
        $changesNew,
        $customer->type ?? null
    );
}




            //---------------------

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Secondary customer updated successfully',
                'data'    => $customer->load([
                    'country', 'state', 'district', 'city', 'pincode', 'beat', 'distributor'
                ]),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update secondary customer',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $customer = SecondaryCustomer::find($id);

            if (!$customer) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            // delete files if exist
            foreach (['owner_photo', 'shop_photo'] as $file) {
                if ($customer->$file && Storage::exists($customer->$file)) {
                    Storage::delete($customer->$file);
                }
            }

            $customer->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Customer deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to delete customer',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function validateData(Request $request, $id = null)
    {
        $rules = [
            'type'                  => 'required|in:RETAILER,WORKSHOP,MECHANIC,GARAGE',
            'owner_name'            => 'required|string|max:255',
            'shop_name'             => 'required|string|max:255',
            'mobile_number'         => [
                'required',
                'digits:10',
                Rule::unique('secondary_customers', 'mobile_number')->ignore($id),
            ],
            'whatsapp_number'       => 'nullable|digits:10',
            'address_line'          => 'required|string|max:500',
            'gps_location'          => 'nullable|string|max:100',

            'country_id'            => 'required|integer|exists:countries,id',
            'state_id'              => 'required|integer|exists:states,id',
            'district_id'           => 'required|integer|exists:districts,id',
            'city_id'               => 'required|integer|exists:cities,id',
            'pincode_id'            => 'required|integer|exists:pincodes,id',

            'beat_id'               => 'nullable|integer|exists:beats,id',
            'opportunity_status'    => 'required|in:HOT,WARM,COLD,LOST',

            'nistha_awareness_status' => 'nullable|in:Done,Not Done',
            'saathi_awareness_status' => 'nullable|in:Done,Not Done',

            'sub_type'              => 'nullable|string|max:100',
            'vehicle_segment'       => 'nullable|string|max:100',
            'belt_area_market_name' => 'nullable|string|max:150',
            'sales_exception_assignment'=> 'nullable|string|max:255',
            // ──── LIGHT FILE VALIDATION ────
            // No 'image' rule — avoids GD memory spike
            'owner_photo'           => 'nullable|file|max:10240',  // max 10 MB
            'shop_photo'            => 'nullable|file|max:10240',
        ];

        // Mechanic-specific
        if ($request->input('type') === 'MECHANIC') {
            $rules['sub_type'] = 'required|string|max:100';
        }

        // Retailer / Workshop specific
        if (in_array($request->input('type'), ['RETAILER', 'WORKSHOP'])) {
            $rules['distributor_name'] = 'required|integer|exists:master_distributors,id';
            $rules['nistha_awareness_status'] = 'required|in:Done,Not Done';
        } else {
            $rules['saathi_awareness_status'] = 'required|in:Done,Not Done';
        }

        $messages = [
            'mobile_number.unique' => 'This mobile number is already registered.',
            'owner_photo.max'      => 'Owner photo must not exceed 10MB.',
            'shop_photo.max'       => 'Shop photo must not exceed 10MB.',
        ];

        return $request->validate($rules, $messages);
    }

    // ────────────────────────────────────────────────
    //   FILE UPLOAD HELPER  (this was missing)
    // ────────────────────────────────────────────────
    private function uploadFile(Request $request, string $field): ?string
    {
        if (!$request->hasFile($field) || !$request->file($field)->isValid()) {
            return null;
        }

        $file = $request->file($field);

        // Minimal logging
        \Log::info("Minimal upload start", ['field' => $field, 'size' => $file->getSize()]);

        // Use move() instead of storeAs() — sometimes more memory efficient in certain PHP versions
        $path = 'secondary-customers/' . now()->format('Y/m') . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(storage_path('app/public/' . dirname($path)), basename($path));

        \Log::info("Minimal upload done", ['path' => $path]);

        return $path;
    }

    // Helper endpoints
    public function getCities(Request $request) {
        $state_id = $request->state_id;
        if (!$state_id) return response()->json([], 400);
        $cities = \App\Models\City::where('state_id', $state_id)->orderBy('city_name')->get(['id', 'city_name']);
        return response()->json($cities);
    }

    public function downloadExcel(Request $request) {
        $type = $request->query('type');
        if (!$type) return response()->json(['error' => 'Missing type'], 400);
        $filename = strtolower($type) . 's_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new SecondaryCustomersExport($request->all(), $type), $filename);
    }

    public function downloadTemplate(Request $request) {
        $type = $request->query('type');
        if (!$type) return response()->json(['error' => 'Missing type'], 400);
        $filename = 'template_' . strtolower($type) . 's_upload.xlsx';
        return Excel::download(new SecondaryCustomersTemplateExport($type), $filename);
    }
    
    /**
     * GET /api/my-hierarchy-users
     * Returns list of all users in your downline + yourself
     * with count, ids, names
     */
    public function getMyHierarchyUsers(Request $request)
    {
        try {
            $authUser = $request->user();
            if (!$authUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
    
            $allIds   = [$authUser->id];
            $this->collectDownlineIds($authUser->id, $allIds);
    
            $users = User::whereIn('id', $allIds)
                ->select('id', 'name', 'mobile', 'designation_id', 'reportingid') // add more fields if needed
                ->orderByRaw("FIELD(id, " . implode(',', $allIds) . ")") // try to keep roughly hierarchical order
                ->get();
    
            // Optional: Build a simple tree structure if frontend wants nested view
            // $tree = $this->buildSimpleTree($users, $authUser->id);
    
            return response()->json([
                'status'       => true,
                'message'      => 'Hierarchy users retrieved',
                'total_users'  => $users->count(),
                'myself'       => [
                    'id'   => $authUser->id,
                    'name' => $authUser->name,
                ],
                'users'        => $users->map(function ($u) {
                    return [
                        'id'          => $u->id,
                        'name'        => $u->name,
                        'mobile'      => $u->mobile ?? null,
                        'reportingid' => $u->reportingid,
                        // 'designation' => $u->getdesignation->designation_name ?? null, // if relation exists
                    ];
                }),
                // 'tree'      => $tree,   // uncomment if you want nested structure
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch hierarchy',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}