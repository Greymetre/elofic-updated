<?php

namespace App\Http\Controllers;

use App\Models\SecondaryCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use DataTables;
use App\Models\Beat;
use App\Models\ActivityLog;
use App\Models\MasterDistributor;
use App\Models\City;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SecondaryCustomersExport;
use App\Exports\SecondaryCustomersTemplateExport;
use App\Imports\SecondaryCustomersImport;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetails;
use Carbon\Carbon;


use Illuminate\Support\Facades\Auth;





class SecondaryCustomerController extends Controller
{
    


    public function index(Request $request)
{
    
    $type = $this->getTypeFromRoute();

    // $ownerNames = SecondaryCustomer::where('type', $type)
    //     ->distinct()
    //     ->orderBy('owner_name')
    //     ->pluck('owner_name')
    //     ->filter()
    //     ->values();

    //     $ownerNamesArray = $ownerNames->mapWithKeys(fn($item) => [$item => $item])->toArray();
    // $shopNames = SecondaryCustomer::where('type', $type)
    //     ->distinct()
    //     ->orderBy('shop_name')
    //     ->pluck('shop_name')
    //     ->filter()
    //     ->values();

    //     $shopNamesArray = $shopNames->mapWithKeys(fn($item) => [$item => $item])->toArray();

    // $mobiles = SecondaryCustomer::where('type', $type)
    //     ->distinct()
    //     ->orderBy('mobile_number')
    //     ->pluck('mobile_number')
    //     ->filter()
    //     ->values();

    //     $mobilesArray = $mobiles->mapWithKeys(fn($item) => [$item => $item])->toArray();
                

        $beats = \App\Models\Beat::where('active', 'Y')
        ->orderBy('beat_name')
        ->get(['id', 'beat_name']);

    $beatsArray = $beats->pluck('beat_name', 'id')->toArray();
        
    $states = \App\Models\State::orderBy('state_name')->get(['id', 'state_name']);
    $totalRecords = SecondaryCustomer::where('type', $type)->count();


    $query = SecondaryCustomer::with(['state', 'district', 'city', 'pincode', 'beat', 'country'])
        ->select('secondary_customers.*'); // Important: select table with alias or all

    $query->where('type', $type);


    if ($request->ajax()) {
        $query = SecondaryCustomer::select(
            'id',
            'owner_name',
            'shop_name',
            'mobile_number',
            'type',
            'state_id',
            'city_id',
            'opportunity_status',
            'saathi_awareness_status',
            'nistha_awareness_status',
            'created_at',
            'beat_id',
            'active',
        );
        
       
        $query->where('type', $type);

        // Global Search
    if ($request->filled('global_search')) {
        $search = $request->global_search;
        $query->where(function ($q) use ($search) {
            $q->where('owner_name', 'like', "%{$search}%")
              ->orWhere('shop_name', 'like', "%{$search}%")
              ->orWhere('mobile_number', 'like', "%{$search}%");
        });
    }

    // Individual Filters
    if ($request->filled('owner_name')) {
    $query->where('id', '=', $request->owner_name);
}

    if ($request->filled('shop_name')) {
        $query->where('id', '=', $request->shop_name);
    }

    if ($request->filled('mobile')) {
        $query->where('id', '=', $request->mobile);
    }

    if ($request->filled('beat_id') && $request->beat_id != '') {
        $query->where('beat_id', $request->beat_id);
    }

    if ($request->filled('state_id') && $request->state_id != '') {
        $query->where('state_id', $request->state_id);
    }

    if ($request->filled('city_id') && $request->city_id != '') {
        $query->where('city_id', $request->city_id);
    }

    if ($request->filled('opportunity_status') && $request->opportunity_status != '') {
        $query->where('opportunity_status', $request->opportunity_status);
    }
    if ($request->filled('active') && $request->active != '') {
    $query->where('active', $request->active);
    }

    if ($request->filled('start_date') && $request->filled('end_date')) {

        $startDate = Carbon::parse($request->start_date)
            ->format('Y-m-d');

        $endDate = Carbon::parse($request->end_date)
            ->format('Y-m-d');

        $query->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

    } elseif ($request->filled('start_date')) {

        $startDate = Carbon::parse($request->start_date)
            ->format('Y-m-d');

        $query->whereDate('created_at', '>=', $startDate);

    } elseif ($request->filled('end_date')) {

        $endDate = Carbon::parse($request->end_date)
            ->format('Y-m-d');

        $query->whereDate('created_at', '<=', $endDate);
    }

    // Awareness Status Filter - Dynamic based on type
    if ($request->filled('awareness_status') && $request->awareness_status != '') {
        $status = $request->awareness_status === 'Done' ? 'Done' : 'Not Done';
        if (in_array($type, ['RETAILER', 'WORKSHOP'])) {
            $query->where('nistha_awareness_status', $status);
        } else {
            $query->where('saathi_awareness_status', $status);
        }
    }

    return DataTables::of($query)
        ->addColumn('action', function ($row) use ($type) {
            $routePrefix = strtolower($type) . 's';
            $encryptedId = encrypt($row->id);

            $btn = '<a href="' . route($routePrefix . '.edit', $encryptedId) . '" class="btn btn-info btn-just-icon btn-sm" title="Edit">
                        <i class="material-icons">edit</i>
                    </a>';
            $btn .= '<a href="' . route($routePrefix . '.show', $encryptedId) . '" class="btn btn-theme btn-just-icon btn-sm" title="View">
                        <i class="material-icons">visibility</i>
                    </a>';
            // DELETE
            $btn .= '<button data-url="'.route($routePrefix.'.destroy',$row->id).'" 
            class="btn btn-danger btn-just-icon btn-sm deleteCustomer">
            <i class="material-icons">delete</i></button>';            // ACTIVE / INACTIVE
            $checked = $row->active == 'Y' ? 'checked' : '';

            $btn .= '<div class="togglebutton">
                            <label>
                                <input type="checkbox" ' . $checked . ' 
                                    id="distributor_' . $row->id . '" 
                                    class="distributor-status-toggle" 
                                    data-id="' . $row->id . '">
                                <span class="toggle"></span>
                            </label>
                        </div>';

            return '<div class="btn-group">' . $btn . '</div>';
        }) 
        ->addColumn('active', function ($row) {

            if ($row->active == 'Y') {
                return '<span class="badge badge-success">ACTIVE</span>';
            }

            return '<span class="badge badge-danger">INACTIVE</span>';
        })

        ->addColumn('awareness_status', function ($row) use ($type) {
            if (in_array($type, ['RETAILER', 'WORKSHOP'])) {
                $status = $row->nistha_awareness_status ?? 'Not Done';
                $label = 'NISTHA';
            } else {
                $status = $row->saathi_awareness_status ?? 'Not Done';
                $label = 'SAATHI';
            }

            $badge = $status === 'Done' ? 'badge-success' : 'badge-danger';
            $text = $status === 'Done' ? 'DONE' : 'NOT DONE';

            return '<span class="badge ' . $badge . '">' . $label . ': ' . $text . '</span>';
        })

        ->editColumn('opportunity_status', function ($row) {
            $status = $row->opportunity_status ?? '-';
            $badge = match ($status) {
                'HOT'   => 'badge-danger',
                'WARM'  => 'badge-warning',
                'COLD'  => 'badge-info',
                'LOST'  => 'badge-secondary',
                default => 'badge-dark',
            };
            return '<span class="badge ' . $badge . '">' . $status . '</span>';
        })

        ->editColumn('beat_id', function ($row) {
            return $row->beat?->beat_name ?? '-';
        })

        ->editColumn('state_id', function ($row) {
            return $row->state?->state_name ?? '-';
        })

        ->editColumn('city_id', function ($row) {
            return $row->city?->city_name ?? '-';
        })

        ->editColumn('created_at', function ($row) {
            return showdatetimeformat($row->created_at);
        })

        

        ->rawColumns(['action', 'awareness_status', 'opportunity_status','active'])
        ->make(true);
    }

    
    $folder = strtolower($type) . 's'; // MECHANIC → mechanics
    $typeTitle = $this->getTypeTitle($type);

    $downloadRoute = route(strtolower($type) . 's.download');
    $templateRoute = route(strtolower($type) . 's.template');

   
    return view($folder . '.index', compact('type',
        'typeTitle',
        // 'ownerNames',
        // 'shopNames',
        // 'mobiles',
        'beats',
        // 'ownerNamesArray',
        // 'shopNamesArray',
        // 'mobilesArray',
        'beatsArray',
        'states',
        'downloadRoute',
        'templateRoute',
        'totalRecords'));
    }

private function getTypeFromRoute()
{
    $routeName = request()->route()->getName();

    if (str_contains($routeName, 'retailers')) return 'RETAILER';
    if (str_contains($routeName, 'mechanics')) return 'MECHANIC';
    if (str_contains($routeName, 'workshops')) return 'WORKSHOP';
    if (str_contains($routeName, 'garages')) return 'GARAGE';

    // Fallback
    $segment = request()->segment(1);
    return strtoupper($segment) === 'RETAILERS' ? 'RETAILER' : 'MECHANIC';
}

private function getTypeTitle($type)
{
    return match($type) {
        'MECHANIC' => 'Mechanics List',
        'GARAGE' => 'Garages List',
        'RETAILER' => 'Retailers List',
        'WORKSHOP' => 'Workshops List',
        default => 'Customers List'
    };
}
    



public function create(Request $request)
{
    
    $type = $this->getTypeFromRoute(); // MECHANIC, GARAGE, etc.

    $customer = new SecondaryCustomer();
    $customer->type = $type; // Pre-fill type

    $beats = Beat::where('active', 'Y')
                  ->orderBy('beat_name')
                  ->pluck('beat_name', 'id');

                                   $distributors = MasterDistributor::orderBy('legal_name')
    ->get(['id', 'distributor_code', 'legal_name']);

$distributorOptions = ['' => 'Select Distributor'];
$users = User::pluck('name', 'id');

foreach ($distributors as $dist) {
    $distributorOptions[$dist->id] = $dist->distributor_code . ' - ' . $dist->legal_name;
}

    $folder = strtolower($type) . 's'; // MECHANIC → mechanics

    
    return view("{$folder}.create", compact('customer', 'beats', 'type', 'distributorOptions','users'));
}

public function edit($id)
{
    
    $customer = SecondaryCustomer::findOrFail(decrypt($id));

    
    $type = $customer->type;

    $beats = Beat::where('active', 'Y')
                  ->orderBy('beat_name')
                  ->pluck('beat_name', 'id');
                  $distributors = MasterDistributor::orderBy('legal_name')
    ->get(['id', 'distributor_code', 'legal_name']);

$distributorOptions = ['' => 'Select Distributor'];
$users = User::pluck('name', 'id');

foreach ($distributors as $dist) {
    $distributorOptions[$dist->id] = $dist->distributor_code . ' - ' . $dist->legal_name;
}
    

    $folder = strtolower($type) . 's'; 

    return view("{$folder}.edit", compact('customer', 'beats', 'type', 'distributorOptions','users'));
}

    /* ================= STORE ================= */
    // public function store(Request $request)
    // {
    //     $validated = $this->validateData($request);

    //     DB::beginTransaction();
    //     try {

    //         foreach (['owner_photo', 'shop_photo'] as $file) {
    //             $validated[$file] = $this->uploadFile($request, $file);
    //         }

    //         SecondaryCustomer::create($validated);

    //         DB::commit();
    //         return redirect()
    //             ->route('secondary-customers.index')
    //             ->with('success', 'Secondary Customer created successfully');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->withErrors($e->getMessage())->withInput();
    //     }
    // }
    public function store(Request $request)
{
    // dd($request);
    $type = $this->getTypeFromRoute(); // MECHANIC, GARAGE etc.

    $validated = $this->validateData($request);
    $validated['created_by'] = Auth::id();
    // $customer = SecondaryCustomer::create($request->all());

    // if ($request->assigned_users) {
    //     $customer->users()->sync($request->assigned_users);
    // }
    if ($request->has('assigned_users')) {
    $validated['employee_id'] = implode(',', $request->assigned_users);
}

if (!empty($validated['gps_location'])) {

    $coords = explode(',', $validated['gps_location']);

    if (count($coords) == 2) {
        $lat = trim($coords[0]);
        $lng = trim($coords[1]);

        $validated['gmap'] = getLatLongToAddress($lat, $lng);
    }
}

// foreach ($existingMobiles as $mobileString) {
//     $numbers = explode(',', $mobileString);
//     foreach ($numbers as $num) {
//         $dbMobiles[] = trim($num);
//     }
// }


    DB::beginTransaction();
    try {
        foreach (['owner_photo', 'shop_photo'] as $file) {
            if ($request->hasFile($file)) {
                $validated[$file] = $this->uploadFile($request, $file);
            }
        }

        $customer = SecondaryCustomer::create($validated);

        logActivity(
            'secondary_customer',    
            $customer->id,           
            'created',               
            'manual',                 
            null,                     
            $customer->toArray(),     
            $type                     
        );


        DB::commit();

        $routePrefix = strtolower($type) . 's'; // mechanics, garages etc.

        return redirect()
            ->route($routePrefix . '.index')
            ->with('success', 'Customer created successfully');

    } catch (\Exception $e) {
        DB::rollBack();

        
        return back()
            ->withErrors(['error' => $e->getMessage()])
            ->withInput();
    }
}

    /* ================= EDIT ================= */
    // public function edit($id)
    // {
    //     $customer = SecondaryCustomer::findOrFail(decrypt($id));
    //     return view('secondary_customers.create_edit', compact('customer'));
    // }

    /* ================= UPDATE ================= */
   public function update(Request $request, $id)
{
    
    $customer = SecondaryCustomer::findOrFail($id);

    // Actual type customer ke record se lo (safe)
    $type = $customer->type;
    $routePrefix = strtolower($type) . 's'; // MECHANIC → mechanics, GARAGE → garages etc.

    $validated = $this->validateData($request, $id);
    // $customer = SecondaryCustomer::create($request->all());

    // if ($request->assigned_users) {
    //     $customer->users()->sync($request->assigned_users);
    // }

if ($request->has('assigned_users')) {
    $validated['employee_id'] = implode(',', $request->assigned_users);
}
if (!empty($validated['gps_location'])) {

    $coords = explode(',', $validated['gps_location']);

    if (count($coords) == 2) {
        $lat = trim($coords[0]);
        $lng = trim($coords[1]);

        $validated['gmap'] = getLatLongToAddress($lat, $lng);
    }
}

    DB::beginTransaction();
    try {

    $oldData = $customer->getOriginal();

        foreach (['owner_photo', 'shop_photo'] as $file) {
            if ($request->hasFile($file)) {
                if ($customer->$file) {
                    Storage::delete($customer->$file);
                }
                $validated[$file] = $this->uploadFile($request, $file);
            }
        }

        
        $customer->update($validated);

        
        $newData = $customer->fresh()->toArray();

        
        $changesOld = [];
        $changesNew = [];

        foreach ($validated as $key => $value) {

            $oldValue = $oldData[$key] ?? null;
            $newValue = $newData[$key] ?? null;

            // compare (string cast to avoid false mismatch)
            if ((string)$oldValue !== (string)$newValue) {
                $changesOld[$key] = $oldValue;
                $changesNew[$key] = $newValue;
            }
        }

        
        if (!empty($changesNew)) {
            logActivity(
                'secondary_customer',
                $customer->id,
                'updated',
                'field_update',
                $changesOld,
                $changesNew,
                $type
            );
        }

        DB::commit();

        return redirect()
            ->route($routePrefix . '.index')  // ← YEH CHANGE KARO
            ->with('success', 'Customer updated successfully');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors($e->getMessage())->withInput();
    }
}

    /* ================= SHOW ================= */
// public function show($id)
// {
//     $customer = SecondaryCustomer::with([
//         'country', 'state', 'district', 'city', 'pincode', 'beat'
//     ])->findOrFail(decrypt($id));

//     $type = $customer->type;
//     $folder = strtolower($type) . 's';

//     return view("{$folder}.show", compact('customer'));
// }



public function show($id)
{
    $customer = SecondaryCustomer::with([
        'country', 'state', 'district', 'city', 'pincode', 'beat','orders.orderdetails.products',
        'orders.orderdetails.productdetails',
        'orders.statusname',
        'orders.executive',
    ])->findOrFail(decrypt($id));

    $totalOrderValue = $customer->orders->sum('grand_total');

    $totalOrderQty = $customer->orders->sum(function ($order) {
        return $order->orderdetails->sum('quantity');
    });

    $lastOrderDate = $customer->orders->max('order_date');
    
    // 👇 Activity Logs Fetch
    $activities = ActivityLog::where('module_id', $customer->id)
    ->where(function ($q) use ($customer) {
        $q->where('customer_type', $customer->type) // MECHANIC
          ->orWhere('customer_type', 'SECONDARY_CUSTOMER'); // order logs
    })
    ->latest()
    // ->limit(20)
    ->get();
    // dd($activities);
    $type = $customer->type;
    $folder = strtolower($type) . 's';

    return view("{$folder}.show", compact('customer',
        'activities',
        'totalOrderValue',
        'totalOrderQty',
        'lastOrderDate'));
}

/* ================= DELETE ================= */
//     public function destroy($id)
// {
//     $customer = SecondaryCustomer::findOrFail($id);
//     $type = $customer->type;
//     $routePrefix = strtolower($type) . 's';

//     // delete photos...

//     $customer->delete();

//     return redirect()
//         ->route($routePrefix . '.index')
//         ->with('success', 'Customer deleted successfully');
// }

public function destroy($id)
{
    $customer = SecondaryCustomer::findOrFail($id);
    $customer->delete();

    if (request()->ajax()) {
        return response()->json([
            'success' => true
        ]);
    }

    return redirect()
        ->back()
        ->with('success', 'Customer deleted successfully');
}

    /* ================= VALIDATION ================= */
    private function validateData(Request $request, $id = null)
{
    $rules = [
        'type' => 'required|string|in:RETAILER,WORKSHOP,MECHANIC,GARAGE',
        'sub_type' => 'nullable|string|max:255', // Mechanic ke liye required hai, baaki ke liye optional
        'owner_name' => 'required|string|max:255',
        'shop_name' => 'required|string|max:255',
        'mobile_number' => 'required|digits:10|unique:secondary_customers,mobile_number,' . $id,
        'whatsapp_number' => 'nullable|digits:10',
        'owner_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'shop_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'vehicle_segment' => 'nullable|string|max:255',
        'address_line' => 'required|string',
        'belt_area_market_name' => 'nullable|string|max:255',
        'saathi_awareness_status' => 'nullable|in:Done,Not Done',
        'distributor_name' => 'nullable|exists:master_distributors,id',
        'opportunity_status' => 'required|in:HOT,WARM,COLD,LOST',
        'gps_location' => 'nullable|string|max:255',

        // ====== YE RULES ADD KARO ======
        'country_id' => 'required|exists:countries,id',
        'state_id' => 'required|exists:states,id',
        'district_id' => 'required|exists:districts,id',
        'city_id' => 'required|exists:cities,id',
        'pincode_id' => 'required|exists:pincodes,id',
        'beat_id' => 'nullable|exists:beats,id',
        'sales_exception_assignment' => 'nullable|string|max:255'
        // ================================
    ];

    // Agar Mechanic hai to sub_type required
    if ($request->type === 'MECHANIC') {
        $rules['sub_type'] = 'required|string|max:255';
    }

    // Retailer & Workshop ke liye distributor (agar abhi bhi hai)
    if (in_array($request->type, ['RETAILER', 'WORKSHOP'])) {
        $rules['distributor_name'] = 'required|string';
    }
    if (in_array($request->type, ['RETAILER', 'WORKSHOP'])) {
        $rules['distributor_name'] = 'required|exists:master_distributors,id';
        $rules['nistha_awareness_status'] = 'required|in:Done,Not Done'; // NAYA REQUIRED
        // saathi_awareness_status optional ho gaya
    } else {
        // Mechanic & Garage ke liye saathi required rahe
        $rules['saathi_awareness_status'] = 'required|in:Done,Not Done';
    }

    return $request->validate($rules);
}

    /* ================= FILE UPLOADER ================= */
    private function uploadFile(Request $request, $field)
    {
        if ($request->hasFile($field)) {
            return $request->file($field)->store('secondary_customers', 'public');
        }
        return null;
    }

    public function country()
{
    return $this->belongsTo(\App\Models\Country::class);
}

public function state()
{
    return $this->belongsTo(\App\Models\State::class);
}

public function district()
{
    return $this->belongsTo(\App\Models\District::class);
}
public function city()
{
    return $this->belongsTo(\App\Models\City::class);
}
public function getCities(Request $request)
{
    $state_id = $request->state_id;

    if (!$state_id) {
        return response()->json([]);
    }

    $cities = \App\Models\City::where('state_id', $state_id)
        ->orderBy('city_name')
        ->get(['id', 'city_name']);

    return response()->json($cities);
}

public function downloadExcel(Request $request)
{
    // dd($request);
    $type = $this->getTypeFromRoute();

   
    $filename = strtolower($type) . 's_' . now()->format('Y-m-d') . '.xlsx';
    // Example: retailers_2026-01-05.xlsx

    return Excel::download(
        new SecondaryCustomersExport($request->all(), $type),
        $filename
    );
}
public function downloadTemplate(Request $request)
{
    $type = $this->getTypeFromRoute(); // MECHANIC, GARAGE etc.

    $filename = 'template_' . strtolower($type) . 's_upload.xlsx';

    return Excel::download(new SecondaryCustomersTemplateExport($type), $filename);
}
// public function import(Request $request)
// {
//     // dd($request);
//     $request->validate([
//         'import_file' => 'required|mimes:xls,xlsx'
//     ]);

//     $type = $this->getTypeFromRoute();

//     $import = new SecondaryCustomersImport($type);

//     Excel::import($import, $request->file('import_file'));

//     if (!empty($import->errors)) {

//         return redirect()->back()->with('importErrors', $import->errors);
//     }

//     return redirect()->back()->with('success', 'Data Imported Successfully');
// }

public function import(Request $request)
{
    $request->validate([
        'import_file' => 'required|mimes:xls,xlsx'
    ]);

    $type = $this->getTypeFromRoute();

    // ✅ Single instance with both params
    $import = new SecondaryCustomersImport($type, auth()->id());

    Excel::import($import, $request->file('import_file'));

    // ✅ Error handling
    if (!empty($import->getErrors())) {
        return redirect()->back()->with([
            'importErrors' => $import->getErrors()
        ]);
    }

    return redirect()->back()->with('success', 'Data Imported Successfully');
}


public function toggleActive(Request $request)
{
    \Log::info('toggleActive method STARTED', [
        'ip'     => $request->ip(),
        'all'    => $request->all(),
        'active' => $request->input('active'),
        'id'     => $request->input('id'),
    ]);

    try {
        $validated = $request->validate([
            'id'     => 'required|integer|exists:secondary_customers,id',
            'active' => 'required|in:Y,N',
        ]);

        \Log::info('Validation passed', $validated);

        $customer = SecondaryCustomer::findOrFail($request->id);
        $old = $customer->active;

        $customer->active = $validated['active'];
        $saved = $customer->save();

        \Log::info('Toggle executed', [
            'customer_id' => $customer->id,
            'was'         => $old,
            'now_set_to'  => $validated['active'],
            'save_returned' => $saved,
            'after_fresh' => $customer->fresh()->active,
        ]);

        return response()->json([
            'success' => true,
            'was'     => $old,
            'now'     => $customer->fresh()->active,
        ]);
    }
    catch (\Exception $e) {
        \Log::error('toggleActive FAILED', [
            'error'   => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
            'request' => $request->all(),
        ]);

        return response()->json([
            'success' => false,
            'error'   => $e->getMessage()
        ], 422);
    }
}

public function filterOptions(Request $request)
{
    $search = $request->search;
    $type = $request->type;

    $query = SecondaryCustomer::query();

    switch ($type) {

        case 'owner':
            $results = $query
                ->where('owner_name', 'like', "%{$search}%")
                ->whereNotNull('owner_name')
                ->distinct()
                ->orderBy('owner_name')
                ->limit(20)
                ->pluck('owner_name');
            break;

        case 'shop':
            $results = $query
                ->where('shop_name', 'like', "%{$search}%")
                ->whereNotNull('shop_name')
                ->distinct()
                ->orderBy('shop_name')
                ->limit(20)
                ->pluck('shop_name');
            break;

        case 'mobile':
            $results = $query
                ->where('mobile_number', 'like', "%{$search}%")
                ->whereNotNull('mobile_number')
                ->distinct()
                ->orderBy('mobile_number')
                ->limit(20)
                ->pluck('mobile_number');
            break;

        default:
            return response()->json([]);
    }

    return response()->json(
        $results->map(function ($item) {
            return [
                'id' => $item,
                'text' => $item
            ];
        })->values()
    );
}




// public function searchShop(Request $request)
// {
//     $search = $request->search;

//     $shops = Customer::where('shop_name', 'LIKE', "%{$search}%")
//         ->distinct()
//         ->limit(20)
//         ->pluck('shop_name');

//     return response()->json(
//         $shops->map(function ($shop) {
//             return [
//                 'id' => $shop,
//                 'text' => $shop
//             ];
//         })
//     );
// }
}
