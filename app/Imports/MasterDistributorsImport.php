<?php

namespace App\Imports;

use App\Models\MasterDistributor;
use App\Models\Beat;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class MasterDistributorsImport implements
    ToModel,
    WithHeadingRow,

    SkipsOnError

{
    use SkipsErrors, SkipsFailures;

    protected $currentRow = [];
    protected $importedCount = 0;
    protected $errors = [];

    // ======================================================
    // GETTERS
    // ======================================================

    public function getImportedRowCount(): int
    {
        return $this->importedCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    // ======================================================
    // HANDLE ERROR
    // ======================================================

    public function onError(\Throwable $e)
    {
        $this->errors[] = $e->getMessage();

        \Log::error('Import Error', [
            'message' => $e->getMessage(),
        ]);
    }

    // ======================================================
    // SANITIZE EXCEL NUMBER
    // ======================================================

    private function cleanExcelNumber($value)
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        if (is_numeric($value)) {
            // Force integer string for mobile, account numbers etc.
            return (string) (int) round((float) $value);
        }

        return trim((string) $value);
    }

    private function findDistributorByMobile($mobile): ?MasterDistributor
    {
        $mobile = substr(preg_replace('/\D/', '', (string) $mobile), -10);

        if ($mobile === '') {
            return null;
        }

        return MasterDistributor::query()
            ->get(['id', 'mobile'])
            ->first(function (MasterDistributor $distributor) use ($mobile) {
                $storedMobile = substr(
                    preg_replace('/\D/', '', (string) $distributor->mobile),
                    -10
                );

                return $storedMobile === $mobile;
            });
    }

    // ======================================================
    // LOCATION RESOLVERS
    // ======================================================

    private function resolveCountry($value)
    {
        if (empty($value)) {
            return null;
        }

        if (Country::find($value)) {
            return $value;
        }

        return Country::whereRaw(
            'LOWER(country_name) = ?',
            [strtolower(trim($value))]
        )->value('id');
    }

    private function resolveState($value)
    {
        if (empty($value)) {
            return null;
        }

        if (State::find($value)) {
            return $value;
        }

        return State::whereRaw(
            'LOWER(state_name) = ?',
            [strtolower(trim($value))]
        )->value('id');
    }

    private function resolveDistrict($value)
    {
        if (empty($value)) {
            return null;
        }

        if (District::find($value)) {
            return $value;
        }

        return District::whereRaw(
            'LOWER(district_name) = ?',
            [strtolower(trim($value))]
        )->value('id');
    }

    private function resolveCity($value)
    {
        if (empty($value)) {
            return null;
        }

        if (City::find($value)) {
            return $value;
        }

        return City::whereRaw(
            'LOWER(city_name) = ?',
            [strtolower(trim($value))]
        )->value('id');
    }

    private function resolvePincode($value)
    {
        if (empty($value)) {
            return null;
        }

        if (Pincode::find($value)) {
            return $value;
        }

        return Pincode::where(
            'pincode',
            trim($value)
        )->value('id');
    }

    // ======================================================
    // LOCATION HIERARCHY VALIDATION
    // ======================================================

    private function validateLocationHierarchy(
        $countryId,
        $stateId,
        $districtId,
        $cityId,
        $pincodeId
    ) {

        if ($stateId && $countryId) {

            $stateExists = State::where('id', $stateId)
                ->where('country_id', $countryId)
                ->exists();

            if (!$stateExists) {
                throw new \Exception(
                    "State ID {$stateId} does not belong to Country ID {$countryId}"
                );
            }
        }

        if ($districtId && $stateId) {

            $districtExists = District::where('id', $districtId)
                ->where('state_id', $stateId)
                ->exists();

            if (!$districtExists) {
                throw new \Exception(
                    "District ID {$districtId} does not belong to State ID {$stateId}"
                );
            }
        }

        if ($cityId && $districtId) {

            $cityExists = City::where('id', $cityId)
                ->where('district_id', $districtId)
                ->exists();

            if (!$cityExists) {
                throw new \Exception(
                    "City ID {$cityId} does not belong to District ID {$districtId}"
                );
            }
        }

        if ($pincodeId && $cityId) {

            $pincodeExists = Pincode::where('id', $pincodeId)
                ->where('city_id', $cityId)
                ->exists();

            if (!$pincodeExists) {
                throw new \Exception(
                    "Pincode ID {$pincodeId} does not belong to City ID {$cityId}"
                );
            }
        }
    }

    // ======================================================
    // MODEL
    // ======================================================

    public function model(array $row)
    {
        
        try {
// dd($row);
            // ==================================================
            // REMOVE dd($row)
            // ==================================================

            // dd($row);

            $id = !empty($row['id'])
                ? (int) trim($row['id'])
                : null;

            // ==================================================
            // DATE FORMAT
            // ==================================================

            $businessStartDate = now()->format('Y-m-d');

            $rawDate = $row['business_start_date'] ?? null;

            if (!empty($rawDate)) {

                try {

                    if (is_numeric($rawDate)) {

                        $businessStartDate =
                            Date::excelToDateTimeObject($rawDate)
                            ->format('Y-m-d');

                    } else {

                        $businessStartDate =
                            Carbon::parse($rawDate)
                            ->format('Y-m-d');
                    }

                } catch (\Exception $e) {

                    \Log::warning(
                        "Failed to parse business_start_date"
                    );
                }
            }

            // ==================================================
            // BEAT
            // ==================================================

            $beatId = !empty($row['beat_id'])
                ? (int) $row['beat_id']
                : null;

            $beatRoute = $row['beat_route'] ?? null;

            if ($beatId) {

                $beat = Beat::find($beatId);

                if ($beat) {

                    $beatRoute =
                        $beat->name ??
                        $beat->beat_name ??
                        $beat->route_name ??
                        $beatRoute;

                } else {

                    throw new \Exception(
                        "Invalid Beat ID: {$beatId}"
                    );
                }
            }

            // ==================================================
            // SALES EXECUTIVES
            // ==================================================

            $salesExecutiveIds = [];

            if (!empty($row['sales_executive_id_json'])) {

                $value = trim($row['sales_executive_id_json']);

                if (Str::startsWith($value, '[')) {

                    $salesExecutiveIds =
                        json_decode($value, true) ?? [];

                } else {

                    $salesExecutiveIds = array_map(
                        'intval',
                        array_map('trim', explode(',', $value))
                    );
                }
            }

            // ==================================================
            // LOCATION IDS
            // ==================================================

            $billingCountryId = $this->resolveCountry(
                $row['billing_country_id']
                    ?? $row['billing_country']
                    ?? null
            );

            $billingStateId = $this->resolveState(
                $row['billing_state_id']
                    ?? $row['billing_state']
                    ?? null
            );

            $billingDistrictId = $this->resolveDistrict(
                $row['billing_district_id']
                    ?? $row['billing_district']
                    ?? null
            );

            $billingCityId = $this->resolveCity(
                $row['billing_city_id']
                    ?? $row['billing_city']
                    ?? null
            );

            $billingPincodeId = $this->resolvePincode(
                $this->cleanExcelNumber(
                    $row['billing_pincode_id']
                        ?? $row['billing_pincode']
                        ?? null
                )
            );

            // ==================================================
            // VALIDATE HIERARCHY
            // ==================================================

            $this->validateLocationHierarchy(
                $billingCountryId,
                $billingStateId,
                $billingDistrictId,
                $billingCityId,
                $billingPincodeId
            );


            $shippingAddresses = [];

            foreach ($row as $key => $value) {

                // Match shipping_address_1, shipping_address_2 etc.
                if (preg_match('/^shipping_address_\d+$/', $key)) {

                    if (!empty(trim($value))) {

                        $shippingAddresses[] = trim($value);
                    }
                }
            }

            // ==================================================
            // DATA
            // ==================================================

            $data = [

                'distributor_code' => isset($row['distributor_code']) && $row['distributor_code'] !== ''
                    ? trim((string) $row['distributor_code'])
                    : 'MD-' . Str::random(8),

                'plant' => isset($row['plant']) && $row['plant'] !== ''
                    ? trim((string) $row['plant'])
                    : null,

                'legal_name' => $row['legal_name']
                    ?? '',

                'trade_name' => $row['trade_name']
                    ?? null,

                'category' => $row['category']
                    ?? '',

                'business_status' => $row['business_status']
                    ?? 'Active',

                'business_start_date' => $businessStartDate ?? '',

                'contact_person' => $row['contact_person']
                    ?? '',

                'designation' => $row['designation'] ?? null,

                // IMPORTANT FIX
                'mobile' => $this->cleanExcelNumber(
                    $row['mobile'] ?? ''
                ),

                'alternate_mobile' => $this->cleanExcelNumber(
                    $row['alternate_mobile'] ?? null
                ),

                'email' => $row['email'] ?? '',

                'secondary_email' =>
                    $row['secondary_email'] ?? null,

                'billing_address' =>
                    $row['billing_address'] ?? '',

                'billing_city' => $billingCityId ?? '',

                'billing_district' => $billingDistrictId ?? '',

                'billing_state' => $billingStateId ?? '',

                'billing_country' => $billingCountryId ?? '',

                'billing_pincode' => $billingPincodeId ?? '',

                // 'shipping_address' =>
                //     $row['shipping_address'] ?? '',
                'shipping_address' => $shippingAddresses,

                'sales_zone' =>
                    $row['sales_zone'] ?? '',

                'area_territory' =>
                    $row['area_territory'] ?? '',

                'beat_id' => $beatId ?? null,

                'beat_route' => $beatRoute ?? '',

                'market_classification' =>
                    $row['market_classification'] ?? '',

                'competitor_brands' =>
                    $row['competitor_brands'] ?? null,

                'gst_number' =>
                    strtoupper($row['gst_number'] ?? ''),

                'pan_number' =>
                    strtoupper($row['pan_number'] ?? ''),

                'registration_type' =>
                    $row['registration_type'] ?? '',

                'bank_name' =>
                    $row['bank_name'] ?? '',

                'account_holder' =>
                    $row['account_holder'] ?? '',

                'account_number' =>
                    $this->cleanExcelNumber(
                        $row['account_number'] ?? ''
                    ),

                'ifsc' =>
                    strtoupper($row['ifsc'] ?? ''),

                'branch_name' =>
                    $row['branch_name'] ?? null,

                'credit_limit' =>
                    $row['credit_limit'] ?? 0,

                'credit_days' =>
                    (int) ($row['credit_days'] ?? 7),

                'avg_monthly_purchase' =>
                    $row['avg_monthly_purchase'] ?? 0,

                'outstanding_balance' =>
                    $row['outstanding_balance'] ?? 0,

                'preferred_payment_method' =>
                    $row['preferred_payment_method'] ?? null,

                'monthly_sales' =>
                    $row['monthly_sales'] ?? 0,

                'product_categories' =>
                    $row['product_categories'] ?? '',

                'secondary_sales_required' =>
                    $row['secondary_sales_required'] ?? null,

                'last_12_months_sales' =>
                    $row['last_12_months_sales'] ?? null,

                'sales_executive_id' =>
                    $salesExecutiveIds ?? null,

                'supervisor_id' =>
                    !empty($row['supervisor_id'])
                        ? (int) $row['supervisor_id']
                        : '',

                'customer_segment' =>
                    $row['customer_segment'] ?? '',

                'weekly_tai_alert' =>
                    $row['weekly_tai_alert'] ?? '',

                'target_vs_achievement' =>
                    $row['target_vs_achievement'] ?? '',

                'schemes_updates' =>
                    $row['schemes_updates'] ?? '',

                'new_launch_update' =>
                    $row['new_launch_update'] ?? '',

                'payment_alert' =>
                    $row['payment_alert'] ?? '',

                'pending_orders' =>
                    $row['pending_orders'] ?? '',

                'inventory_status' =>
                    $row['inventory_status'] ?? '',

                'turnover' =>
                    $row['turnover'] ?? 0,

                'staff_strength' =>
                    $row['staff_strength'] ?? '',

                'vehicles_capacity' =>
                    $row['vehicles_capacity'] ?? '',

                'area_coverage' =>
                    $row['area_coverage'] ?? '',

                'other_brands_handled' =>
                    $row['other_brands_handled'] ?? '',

                'warehouse_size' =>
                    $row['warehouse_size'] ?? '',
            ];

            // ==================================================
            // RESOLVE UPDATE TARGET
            // ==================================================

            $distributor = $id ? MasterDistributor::find($id) : null;

            if ($id && !$distributor) {
                throw new \Exception("Distributor with ID {$id} not found.");
            }

            $mobileDistributor = $this->findDistributorByMobile($data['mobile']);

            if (!$distributor && $mobileDistributor) {
                $distributor = $mobileDistributor;
                $id = $distributor->id;
            } elseif (
                $distributor &&
                $mobileDistributor &&
                $mobileDistributor->id !== $distributor->id
            ) {
                throw new \Exception(
                    "Mobile Number '{$data['mobile']}' belongs to another distributor."
                );
            }

            Validator::make($data, [
                'distributor_code' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('master_distributors', 'distributor_code')->ignore($id),
                ],
                'plant' => ['nullable', 'string', 'max:255'],
                'mobile' => ['nullable', 'digits:10'],
                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('master_distributors', 'email')->ignore($id),
                ],
                'gst_number' => [
                    'nullable',
                    'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                ],
                'pan_number' => [
                    'nullable',
                    'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                ],
            ], $this->customValidationMessages())->validate();

            // ==================================================
            // CHECK DUPLICATE DISTRIBUTOR CODE
            // ==================================================

            $duplicateDistributor = MasterDistributor::where(
                'distributor_code',
                $data['distributor_code']
            );

            if ($id) {
                $duplicateDistributor->where('id', '!=', $id);
            }

            $duplicateDistributor = $duplicateDistributor->first();

            if ($duplicateDistributor) {

                throw new \Exception(
                    "Distributor Code '{$data['distributor_code']}' already exists in database."
                );
            }

            // ==================================================
            // CHECK DUPLICATE MOBILE
            // ==================================================

            if (!empty($data['mobile'])) {

                $mobile = trim(
                    preg_replace('/\D/', '', $data['mobile'])
                );

                $mobileQuery = MasterDistributor::query();

                // Ignore same ID while updating
                if (!empty($id)) {
                    $mobileQuery->where('id', '!=', (int) $id);
                }

                $allMobiles = $mobileQuery->pluck('mobile', 'id');

                foreach ($allMobiles as $dbId => $dbMobile) {

                    $dbMobile = trim(
                        preg_replace('/\D/', '', $dbMobile)
                    );

                    // Compare last 10 digits
                    $dbMobile = substr($dbMobile, -10);
                    $mobileCompare = substr($mobile, -10);

                    if ($dbMobile === $mobileCompare) {

                        throw new \Exception(
                            "Mobile Number '{$mobile}' already exists in database."
                        );
                    }
                }
            }

            // ==================================================
            // UPDATE / CREATE
            // ==================================================

            if ($distributor) {
                $distributor->update($data);
            } else {
                $distributor = MasterDistributor::create($data);
            }

            // ==================================================
            // CHECK DUPLICATE MOBILE
            // ==================================================

            

            $this->importedCount++;

            return $distributor;

        } catch (\Throwable $e) {

            $this->errors[] = [
                'row' => $row['distributor_code']
                    ?? 'Unknown',

                'message' => $e->getMessage(),
            ];

            \Log::error(
                'Master Distributor Import Error',
                [
                    'row' => $row,
                    'error' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    // ======================================================
    // RULES
    // ======================================================

    public function rules(): array
    {
        $id = $this->currentRow['id'] ?? null;

        return [

            'id' => [
                'nullable',
                'integer',
                'exists:master_distributors,id'
            ],

            'distributor_code' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'master_distributors',
                    'distributor_code'
                )->ignore($id),
            ],

            'mobile' => [
                'nullable',
                'digits:10',

                Rule::unique(
                    'master_distributors',
                    'mobile'
                )->ignore($id),
            ],

            'email' => [
                'nullable',
                'email',

                Rule::unique(
                    'master_distributors',
                    'email'
                )->ignore($id),
            ],

            'gst_number' => [
                'nullable',

                'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'
            ],

            'pan_number' => [
                'nullable',

                'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'
            ],
        ];
    }

    // ======================================================
    // CUSTOM MESSAGES
    // ======================================================

    public function customValidationMessages()
    {
        return [

            'distributor_code.required' =>
                'Distributor Code is required.',

            'distributor_code.unique' =>
                'Distributor Code already exists.',

            'mobile.digits' =>
                'Mobile must be 10 digits.',

            'email.email' =>
                'Please enter valid email.',

            'gst_number.regex' =>
                'Invalid GST Number format.',

            'pan_number.regex' =>
                'Invalid PAN Number format.',
        ];
    }

    // ======================================================
    // PREPARE VALIDATION
    // ======================================================

    public function prepareForValidation($data, $index)
    {
        $this->currentRow = $data;

        return $data;
    }
}
