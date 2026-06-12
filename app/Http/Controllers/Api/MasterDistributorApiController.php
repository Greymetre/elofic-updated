<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterDistributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MasterDistributorsExport;
use App\Exports\MasterDistributorsTemplateExport;
use App\Models\User;

class MasterDistributorApiController extends Controller
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
        try {
            $authUser = $request->user();
            if (!$authUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
            $query = MasterDistributor::query();
            
            // =========================================
            // Hierarchy Logic
            // =========================================
            
            $isSuperAdmin = false;
            
            if (method_exists($authUser, 'hasRole')) {
                $isSuperAdmin =
                    $authUser->hasRole('superadmin') ||
                    $authUser->hasRole('subAdmin');
            }
            
            if (!$isSuperAdmin && $authUser->relationLoaded('roles')) {
                $roles = $authUser->roles->pluck('name');
            
                $isSuperAdmin =
                    $roles->contains('superadmin') ||
                    $roles->contains('subAdmin');
            }
            
            if (!$isSuperAdmin) {
            
                $visibleUserIds = $this->getVisibleUserIds($authUser);
            
                $visibleUserIds[] = $authUser->id;
            
                $visibleUserIds = array_unique($visibleUserIds);
            
                // BM Role
                $isBM = false;
            
                if (method_exists($authUser, 'hasRole')) {
                    $isBM = $authUser->hasRole('BM.');
                }
            
                if (!$isBM && $authUser->relationLoaded('roles')) {
                    $roles = $authUser->roles->pluck('name');
                    $isBM = $roles->contains('BM.');
                }
            
                // BM → whole branch users
                if ($isBM) {
            
                    $visibleUserIds = User::where('branch_id', $authUser->branch_id)
                        ->pluck('id')
                        ->toArray();
                }
            
                $query->where(function ($q) use ($visibleUserIds) {
            
                    $q->whereIn('created_by', $visibleUserIds);
            
                    foreach ($visibleUserIds as $userId) {
            
                        $id = (int) $userId;
            
                        $q->orWhere('sales_executive_id', 'LIKE', "%\"{$id}\"%")
                          ->orWhere('sales_executive_id', 'LIKE', "%{$id}%")
                          ->orWhereRaw("JSON_CONTAINS(sales_executive_id, '\"{$id}\"')")
                          ->orWhereRaw("JSON_SEARCH(sales_executive_id, 'one', '{$id}') IS NOT NULL");
                    }
                });
            }

            // Global search
            if ($request->filled('global_search')) {
                $search = $request->global_search;
                $query->where(function ($q) use ($search) {
                    $q->where('distributor_code', 'like', "%{$search}%")
                      ->orWhere('legal_name', 'like', "%{$search}%")
                      ->orWhere('trade_name', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                });
            }

            // Individual filters
            if ($request->filled('distributor_code')) {
                $query->where('distributor_code', 'like', "%{$request->distributor_code}%");
            }
            if ($request->filled('legal_name')) {
                $query->where('legal_name', 'like', "%{$request->legal_name}%");
            }
            if ($request->filled('trade_name')) {
                $query->where('trade_name', 'like', "%{$request->trade_name}%");
            }
            if ($request->filled('contact_person')) {
                $query->where('contact_person', 'like', "%{$request->contact_person}%");
            }
            if ($request->filled('mobile')) {
                $query->where('mobile', 'like', "%{$request->mobile}%");
            }
            if ($request->filled('business_status')) {
                $query->where('business_status', $request->business_status);
            }
            // Sort by created_at DESCENDING (newest first)
            $query->orderBy('created_at', 'desc');
            // Add check-in info (last check-in + whether today)
            $today = now()->startOfDay()->toDateString(); // '2026-02-25'

            // Add check-in / check-out subqueries
            $query->addSelect([
                // Check-in related
                'last_checkin_date' => \App\Models\CheckIn::select('checkin_date')
                    ->whereColumn('entity_id', 'master_distributors.id')
                    ->where('entity_type', 'distributor')
                    ->where('user_id', $authUser->id)
                    ->orderByDesc('checkin_date')
                    ->orderByDesc('checkin_time')
                    ->limit(1),

                'last_checkin_time' => \App\Models\CheckIn::select('checkin_time')
                    ->whereColumn('entity_id', 'master_distributors.id')
                    ->where('entity_type', 'distributor')
                    ->where('user_id', $authUser->id)
                    ->orderByDesc('checkin_date')
                    ->orderByDesc('checkin_time')
                    ->limit(1),

                'has_checked_in_today' => \App\Models\CheckIn::selectRaw('IF(COUNT(*) > 0, 1, 0)')
                    ->whereColumn('entity_id', 'master_distributors.id')
                    ->where('entity_type', 'distributor')
                    ->where('user_id', $authUser->id)
                    ->whereDate('checkin_date', $today),

                // Check-out related
                'last_checkout_date' => \App\Models\CheckIn::select('checkout_date')
                    ->whereColumn('entity_id', 'master_distributors.id')
                    ->where('entity_type', 'distributor')
                    ->where('user_id', $authUser->id)
                    ->whereNotNull('checkout_date')
                    ->orderByDesc('checkout_date')
                    ->orderByDesc('checkout_time')
                    ->limit(1),
                    
                'current_visit_is_open' => \App\Models\CheckIn::selectRaw('IF(COUNT(*) > 0, 1, 0)')
                    ->whereColumn('entity_id', 'master_distributors.id')
                    ->where('entity_type', 'distributor')
                    ->where('user_id', $authUser->id)
                    ->whereNull('checkout_date')
                    ->whereDate('checkin_date', $today),

                'last_checkout_time' => \App\Models\CheckIn::select('checkout_time')
                    ->whereColumn('entity_id', 'master_distributors.id')
                    ->where('entity_type', 'distributor')
                    ->where('user_id', $authUser->id)
                    ->whereNotNull('checkout_date')
                    ->orderByDesc('checkout_date')
                    ->orderByDesc('checkout_time')
                    ->limit(1),

                'has_checked_out_today' => \App\Models\CheckIn::selectRaw('IF(COUNT(*) > 0, 1, 0)')
                    ->whereColumn('entity_id', 'master_distributors.id')
                    ->where('entity_type', 'distributor')
                    ->where('user_id', $authUser->id)
                    ->whereDate('checkout_date', $today),

                'last_checkin_id' => \App\Models\CheckIn::select('id')
                    ->whereColumn('entity_id', 'master_distributors.id')
                    ->where('entity_type', 'distributor')
                    ->where('user_id', $authUser->id)
                    ->orderByDesc('checkin_date')
                    ->orderByDesc('checkin_time')
                    ->limit(1),
            ]);
            // Pagination
            $perPage = $request->query('per_page', 10);
            $distributors = $query->paginate($perPage);
            
            $distributors->getCollection()->transform(function ($item) {

                $item->billing_city_name = $item->billing_city_export_name;
            
                $item->billing_pincode_name = $item->billing_pincode_export_name;
            
                return $item;
            });

            // Clean response
            $cleanData = [
                'current_page' => $distributors->currentPage(),
                'data'         => $distributors->items(),
                'from'         => $distributors->firstItem(),
                'to'           => $distributors->lastItem(),
                'per_page'     => $distributors->perPage(),
                'total'        => $distributors->total(),
                'last_page'    => $distributors->lastPage(),
            ];

            return response()->json([
                'status'  => true,
                'message' => 'Master distributors retrieved successfully',
                'data'    => $cleanData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch master distributors',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $authUser = request()->user();

            if (!$authUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // Load the distributor + real relationship (supervisor)
            $distributor = MasterDistributor::with(['supervisor'])->find($id);

            if (!$distributor) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Distributor not found',
                ], 404);
            }

            // Manually load sales executives (because it's not a real relation)
            $distributor->sales_executives = $distributor->salesExecutives();
            
            $distributor->billing_city_name = $distributor->billing_city_export_name;

            $distributor->billing_pincode_name = $distributor->billing_pincode_export_name;

            // ────────────────────────────────────────────────
            //   Add check-in / check-out data for this user
            // ────────────────────────────────────────────────
            $today = now()->startOfDay()->toDateString();

            $checkInQuery = \App\Models\CheckIn::where('entity_type', 'distributor')
                ->where('entity_id', $id)
                ->where('user_id', $authUser->id);

            // Latest check-in (regardless of checkout status)
            $lastCheckIn = (clone $checkInQuery)
                ->orderByDesc('checkin_date')
                ->orderByDesc('checkin_time')
                ->first(['id', 'checkin_date', 'checkin_time', 'checkin_address', 'checkout_date', 'checkout_time', 'checkout_address', 'time_interval']);

            // Latest check-out (only records that actually have checkout)
            $lastCheckOut = (clone $checkInQuery)
                ->whereNotNull('checkout_date')
                ->orderByDesc('checkout_date')
                ->orderByDesc('checkout_time')
                ->first(['checkout_date', 'checkout_time', 'checkout_address']);

            // Today's status
            $hasCheckedInToday = (clone $checkInQuery)
                ->whereDate('checkin_date', $today)
                ->exists();

            $hasCheckedOutToday = (clone $checkInQuery)
                ->whereDate('checkout_date', $today)
                ->exists();

            // Prepare clean check-in/check-out data
            $checkData = [
                'last_checkin' => $lastCheckIn ? [
                    'checkin_id'       => $lastCheckIn->id,
                    'checkin_datetime' => $lastCheckIn->checkin_date . ' ' . $lastCheckIn->checkin_time,
                    'checkin_address'  => $lastCheckIn->checkin_address,
                    'checkout_datetime'=> $lastCheckIn->checkout_date 
                        ? $lastCheckIn->checkout_date . ' ' . $lastCheckIn->checkout_time 
                        : null,
                    'checkout_address' => $lastCheckIn->checkout_address,
                    'duration'         => $lastCheckIn->time_interval,
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

            return response()->json([
                'status'      => true,
                'message'     => 'Distributor retrieved successfully',
                'data'        => $distributor,
                'check_status'=> $checkData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to retrieve distributor',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
    \Log::info('MasterDistributor store called', [
        'all_input' => $request->all(),
        'files' => array_keys($request->allFiles()),
        'memory_start' => memory_get_usage(true) / 1024 / 1024 . ' MB',
    ]);

    try {
            $validated = $this->validateData($request);
    
            \Log::info('Validation passed', ['validated' => $validated]);
    
            DB::beginTransaction();
    
            $data = $validated;
    
            $data['same_as_billing'] = $request->boolean('same_as_billing');
    
            if ($data['same_as_billing']) {
                $data['shipping_address'] = $data['billing_address'] ?? null;
                // $data['shipping_city'] = $data['billing_city'] ?? null;
                // $data['shipping_district'] = $data['billing_district'] ?? null;
                // $data['shipping_state'] = $data['billing_state'] ?? null;
                // $data['shipping_country'] = $data['billing_country'] ?? null;
                // $data['shipping_pincode'] = $data['billing_pincode'] ?? null;
            }
    
            $data['sales_executive_id'] = json_encode($request->input('sales_executive_id', []));
    
            // File uploads with logging
            if ($request->hasFile('shop_image')) {
                \Log::info('Uploading shop_image');
                $data['shop_image'] = $request->file('shop_image')->store('distributors/shop_images', 'public');
            }
            if ($request->hasFile('profile_image')) {
                \Log::info('Uploading profile_image');
                $data['profile_image'] = $request->file('profile_image')->store('distributors/profile_images', 'public');
            }
            if ($request->hasFile('cancelled_cheque')) {
                \Log::info('Uploading cancelled_cheque');
                $data['cancelled_cheque'] = $request->file('cancelled_cheque')->store('distributors/cheques', 'public');
            }
            if ($request->hasFile('mou_file')) {
                \Log::info('Uploading mou_file');
                $data['mou_file'] = $request->file('mou_file')->store('distributors/mou', 'public');
            }
            if ($request->hasFile('documents')) {
                \Log::info('Uploading documents', ['count' => count($request->file('documents'))]);
                $paths = [];
                foreach ($request->file('documents') as $file) {
                    $paths[] = $file->store('distributors/documents', 'public');
                }
                $data['documents'] = json_encode($paths);
            }
    
            \Log::info('Creating record', ['data' => $data]);
    
            $distributor = MasterDistributor::create($data);
            
            
            
            // -----------------
            
            
            $newData = [
                'name'   => $distributor->legal_name ?? null,
                'mobile' => $distributor->mobile ?? null,
                'email'  => $distributor->email ?? null,
                'code'   => $distributor->distributor_code ?? null,
                'type'   => 'MASTER_DISTRIBUTOR'
            ];
            
            \Log::info('Before logActivity');

            logActivity(
                'master_distributor',
                $distributor->id,
                'created',
                'api',
                null,
                $distributor,
                'MASTER_DISTRIBUTOR'
            );
            
            \Log::info('After logActivity');
            
            
            // -----------------
    
            DB::commit();
    
            \Log::info('MasterDistributor created successfully', ['id' => $distributor->id]);
    
            return response()->json([
                'status'  => true,
                'message' => 'Master distributor created successfully',
                'data'    => $distributor,  // ← no ->load()
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('MasterDistributor store failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'memory_peak' => memory_get_peak_usage(true) / 1024 / 1024 . ' MB'
            ]);
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create master distributor',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $distributor = MasterDistributor::findOrFail($id);

        $validated = $this->validateData($request, $id);

        DB::beginTransaction();
        try {
            $data = $validated;

            // Handle same as billing
            $data['same_as_billing'] = $request->boolean('same_as_billing');
            if ($data['same_as_billing']) {
                $data['shipping_address'] = $data['billing_address'];
                // $data['shipping_city'] = $data['billing_city'];
                // $data['shipping_district'] = $data['billing_district'];
                // $data['shipping_state'] = $data['billing_state'];
                // $data['shipping_country'] = $data['billing_country'];
                // $data['shipping_pincode'] = $data['billing_pincode'];
            }

            // Handle sales executive IDs
            $data['sales_executive_id'] = json_encode($request->input('sales_executive_id', []));

            // Handle files
            if ($request->hasFile('shop_image')) {
                if ($distributor->shop_image) Storage::disk('public')->delete($distributor->shop_image);
                $data['shop_image'] = $request->file('shop_image')->store('distributors/shop_images', 'public');
            }
            if ($request->hasFile('profile_image')) {
                if ($distributor->profile_image) Storage::disk('public')->delete($distributor->profile_image);
                $data['profile_image'] = $request->file('profile_image')->store('distributors/profile_images', 'public');
            }
            if ($request->hasFile('cancelled_cheque')) {
                if ($distributor->cancelled_cheque) Storage::disk('public')->delete($distributor->cancelled_cheque);
                $data['cancelled_cheque'] = $request->file('cancelled_cheque')->store('distributors/cheques', 'public');
            }
            if ($request->hasFile('mou_file')) {
                if ($distributor->mou_file) Storage::disk('public')->delete($distributor->mou_file);
                $data['mou_file'] = $request->file('mou_file')->store('distributors/mou', 'public');
            }
            if ($request->hasFile('documents')) {
                $paths = json_decode($distributor->documents, true) ?? [];
                foreach ($request->file('documents') as $file) {
                    $paths[] = $file->store('distributors/documents', 'public');
                }
                $data['documents'] = json_encode($paths);
            }
                //--------------------
                
                
                $oldData = $distributor->getOriginal();
                
                
                
                //--------------------
            $distributor->update($data);
            
            
            
            //====================
            
            
            
            $newData = $distributor->fresh()->toArray();

            // 🔥 DIFF
            $changesOld = [];
            $changesNew = [];
            
            foreach ($newData as $field => $newValue) {

                // Skip unnecessary fields
                if (in_array($field, [
                    'updated_at',
                    'created_at'
                ])) {
                    continue;
                }
            
                $oldValue = $oldData[$field] ?? null;
            
                $oldEncoded = is_array($oldValue)
                    ? json_encode($oldValue)
                    : (string) $oldValue;
            
                $newEncoded = is_array($newValue)
                    ? json_encode($newValue)
                    : (string) $newValue;
            
                if ($oldEncoded !== $newEncoded) {
            
                    $changesOld[$field] = $oldValue;
                    $changesNew[$field] = $newValue;
                }
            }
            
            // 🔥 LOG
            if (!empty($changesNew)) {
            
                logActivity(
                    'master_distributor',
                    $distributor->id,
                    'updated',
                    'api',
                    $changesOld,
                    $changesNew,
                    'MASTER_DISTRIBUTOR'
                );
            }
            
            
            
            //------------------------------

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Master distributor updated successfully',
                'data'    => $distributor->fresh(),  // ← no ->load()
                'difd' => $changesNew
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
                'message' => 'Failed to update master distributor',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $distributor = MasterDistributor::find($id);

            if (!$distributor) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Distributor not found',
                ], 404);
            }

            // Delete files
            foreach (['shop_image', 'profile_image', 'cancelled_cheque', 'mou_file'] as $file) {
                if ($distributor->$file && Storage::exists($distributor->$file)) {
                    Storage::delete($distributor->$file);
                }
            }
            if ($distributor->documents) {
                $docs = json_decode($distributor->documents, true) ?? [];
                foreach ($docs as $doc) {
                    if (Storage::exists($doc)) Storage::delete($doc);
                }
            }

            $distributor->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Distributor deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to delete distributor',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function validateData(Request $request, $id = null)
    {
        $rules = [
            'legal_name'         => 'required|string|max:255',
            'trade_name'         => 'nullable|string|max:255',
            'distributor_code'   => [
                'required',
                'string',
                'max:100',
                Rule::unique('master_distributors', 'distributor_code')->ignore($id),
            ],
            'category'           => 'required|string',
            'business_status'    => 'required|in:Active,Inactive,On Hold',
            'business_start_date'=> 'required|date',
            'contact_person'     => 'required|string|max:255',
            'designation'        => 'nullable|string|max:255',
            'mobile'             => [
                'required',
                'digits:10',
                Rule::unique('master_distributors', 'mobile')->ignore($id),
            ],
            'alternate_mobile'   => 'nullable|digits:10',
            'email'              => [
                'nullable',
                'email',
                Rule::unique('master_distributors', 'email')->ignore($id),
            ],
            'secondary_email'    => 'nullable|email',
            'gps_location'    => 'nullable|string|max:255',

            'billing_address'    => 'required|string|max:500',
            'billing_city'       => 'required|string',
            'billing_district'   => 'required|string',
            'billing_state'      => 'required|string',
            'billing_country'    => 'required|string',
            'billing_pincode'    => 'required|string',

            'same_as_billing'    => 'boolean',

            'shipping_address'   => 'required_if:same_as_billing,false|string|max:500',
            // 'shipping_city'      => 'required_if:same_as_billing,false|string',
            // 'shipping_district'  => 'required_if:same_as_billing,false|string',
            // 'shipping_state'     => 'required_if:same_as_billing,false|string',
            // 'shipping_country'   => 'required_if:same_as_billing,false|string',
            // 'shipping_pincode'   => 'required_if:same_as_billing,false|string',

            'sales_zone'         => 'required|string',
            'area_territory'     => 'required|string',
            'beat_route'         => 'required|string',
            'market_classification' => 'required|string',
            'competitor_brands'  => 'required|string',

            'gst_number'         => 'required|string',
            'pan_number'         => 'required|string',
            'registration_type'  => 'required|string',

            'bank_name'          => 'required|string',
            'account_holder'     => 'required|string',
            'account_number'     => 'required|string',
            'ifsc'               => 'required|string',
            'branch_name'        => 'required|string',
            'credit_limit'       => 'required|numeric|min:0',
            'credit_days'        => 'required|integer|min:0',
            'avg_monthly_purchase' => 'required|numeric|min:0',
            'outstanding_balance'  => 'required|numeric',
            'preferred_payment_method' => 'required|string',

            // Updated fields as per your request
            'monthly_sales'      => 'required|numeric|min:0',
            'product_categories' => 'required|string',
            'secondary_sales_required' => 'required|in:Yes,No', // ← only Yes or No
            'last_12_months_sales' => 'required|numeric|min:0',
            'sales_executive_id' => 'required|array|min:1',
            'sales_executive_id.*' => 'exists:users,id',
            'supervisor_id'      => 'required|exists:users,id',
            'customer_segment'   => 'required|string',

            'weekly_tai_alert'   => 'required|in:A,B', // ← only A or B
            'target_vs_achievement' => 'required|string|max:255', // ← open input (can be %, text, etc.)
            'schemes_updates'    => 'required|in:A,B', // ← only A or B
            'new_launch_update'  => 'required|in:A,B', // ← only A or B
            'payment_alert'      => 'required|in:A,B', // ← only A or B
            'pending_orders'     => 'required|in:A,B', // ← only A or B
            'inventory_status'   => 'required|in:A,B', // ← only A or B

            'turnover'           => 'required|numeric|min:0',
            'staff_strength'     => 'required|integer|min:0',
            'vehicles_capacity'  => 'required|string',
            'area_coverage'      => 'required|string',
            'other_brands_handled' => 'required|string',
            'warehouse_size'     => 'required|string',

            'shop_image'         => 'nullable|file|mimes:jpeg,png,jpg|max:3072',
            'profile_image'      => 'nullable|file|mimes:jpeg,png,jpg|max:3072',
            'cancelled_cheque'   => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'mou_file'           => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'documents.*'        => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ];

        $messages = [
            'sales_executive_id.required'          => 'At least one sales executive is required.',
            'sales_executive_id.*.exists'          => 'One or more selected sales executives are invalid.',
            'secondary_sales_required.in'          => 'Secondary sales required must be Yes or No.',
            'weekly_tai_alert.in'                  => 'Weekly TAI alert must be A or B.',
            'schemes_updates.in'                   => 'Schemes updates must be A or B.',
            'new_launch_update.in'                 => 'New launch update must be A or B.',
            'payment_alert.in'                     => 'Payment alert must be A or B.',
            'pending_orders.in'                    => 'Pending orders must be A or B.',
            'inventory_status.in'                  => 'Inventory status must be A or B.',
        ];

        return $request->validate($rules, $messages);
    }

    public function getStates($country_id)
    {
        $states = \App\Models\State::where('country_id', $country_id)
            ->orderBy('state_name')
            ->get(['id', 'state_name']);

        return response()->json($states);
    }

    public function getDistricts($state_id)
    {
        $districts = \App\Models\District::where('state_id', $state_id)
            ->orderBy('district_name')
            ->get(['id', 'district_name']);

        return response()->json($districts);
    }

    public function getCities($district_id)
    {
        $cities = \App\Models\City::where('district_id', $district_id)
            ->orderBy('city_name')
            ->get(['id', 'city_name']);

        return response()->json($cities);
    }

    public function getPincodes($city_id)
    {
        $pincodes = \App\Models\Pincode::where('city_id', $city_id)
            ->orderBy('pincode')
            ->get(['id', 'pincode']);

        return response()->json($pincodes);
    }

    public function downloadExcel(Request $request)
    {
        $filename = 'master_distributors_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new MasterDistributorsExport($request->all()), $filename);
    }

    public function downloadTemplate(Request $request)
    {
        $filename = 'template_master_distributors_upload.xlsx';
        return Excel::download(new MasterDistributorsTemplateExport(), $filename);
    }

    public function getSupervisors(Request $request)
    {
        $supervisors = User::query()
            ->where('active', 'Y')
            ->whereNotNull('name')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($u) => [
                'id'          => $u->id,
                'name'        => $u->name,
            ]);

        return response()->json([
            'status'  => true,
            'message' => 'Supervisors fetched',
            'data'    => $supervisors,
            'count'   => $supervisors->count(),
        ]);
    }
    
    /**
     * Get list of ACTIVE master distributors with only ID, trade_name, legal_name
     * 
     * GET /api/master-distributors/active-simple
     * 
     * Optional query params:
     * ?search=       → global search on trade_name or legal_name
     * ?per_page=20   → default 100
     */
    public function getDistributorList(Request $request)
    {
        try {
            $authUser = $request->user();
            if (!$authUser) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
    
            $query = MasterDistributor::query()
                ->where('business_status', 'Active')
                ->select([
                    'id',
                    'trade_name',
                    'legal_name',
                    // you can add more minimal fields if needed later, e.g.:
                    // 'distributor_code',
                    // 'mobile',
                ]);
    
            // Optional global search on trade_name OR legal_name
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('trade_name', 'like', "%{$search}%")
                      ->orWhere('legal_name', 'like', "%{$search}%");
                });
            }
    
            // Sorting - most recently created first (or change to ->orderBy('trade_name'))
            $query->orderBy('created_at', 'desc');
    
            // Pagination (generous default since it's a selector-type list)
            $perPage = $request->integer('per_page', 100);
            $perPage = min(max($perPage, 10), 500); // reasonable limits
    
            $distributors = $query->paginate($perPage);
    
            // Clean/minimal pagination response
            return response()->json([
                'status'  => true,
                'message' => 'Active distributors fetched successfully',
                'data'    => [
                    'current_page' => $distributors->currentPage(),
                    'data'         => $distributors->items(),
                    'from'         => $distributors->firstItem(),
                    'to'           => $distributors->lastItem(),
                    'per_page'     => $distributors->perPage(),
                    'total'        => $distributors->total(),
                    'last_page'    => $distributors->lastPage(),
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to fetch active distributors',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}