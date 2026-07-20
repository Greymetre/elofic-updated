<?php

// namespace App\Imports;

// use App\Models\SecondaryCustomer;
// use App\Models\Country;
// use App\Models\State;
// use App\Models\District;
// use App\Models\City;
// use App\Models\Pincode;
// use App\Models\Beat;
// use Illuminate\Support\Collection;
// use Maatwebsite\Excel\Concerns\ToCollection;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;

// class SecondaryCustomersImport implements ToCollection, WithHeadingRow
// {
//     protected $type;

//     public function __construct($type)
//     {
//         $this->type = $type ;
//     }

//     public function collection(Collection $rows)
//     {
//         foreach ($rows as $index => $row) {

//             $rowNumber = $index + 2; // because heading row

//             try {

//                 // Country
//                 $countryValue = trim($row['country']);

//                 if (is_numeric($countryValue)) {
//                     $country = Country::find($countryValue);
//                 } else {
//                     $country = Country::where('country_name', $countryValue)->first();
//                 }

//                 if (!$country) {
//                     throw new \Exception("Invalid country");
//                 }

//                 // State
//                 $stateValue = trim($row['state']);

//                 $state = is_numeric($stateValue)
//                     ? State::find($stateValue)
//                     : State::where('state_name', $stateValue)
//                         ->where('country_id', $country->id)
//                         ->first();

//                 if (!$state) {
//                     throw new \Exception("Invalid state");
//                 }

//                 // District
//                 $districtValue = trim($row['district']);

//                 $district = is_numeric($districtValue)
//                     ? District::find($districtValue)
//                     : District::where('district_name', $districtValue)
//                         ->where('state_id', $state->id)
//                         ->first();

//                 if (!$district) {
//                     throw new \Exception("Invalid district");
//                 }

//                 // City
//                 $cityValue = trim($row['city']);

//                 $city = is_numeric($cityValue)
//                     ? City::find($cityValue)
//                     : City::where('city_name', $cityValue)
//                         ->where('district_id', $district->id)
//                         ->first();

//                 if (!$city) {
//                     throw new \Exception("Invalid city");
//                 }

//                 // Duplicate mobile check
//                 if (SecondaryCustomer::where('mobile_number', $row['mobile_number'])->exists()) {
//                     throw new \Exception("Duplicate mobile");
//                 }

//                 // Pincode
//                 $pincodeValue = trim($row['pincode']);

//                 $pincode = is_numeric($pincodeValue)
//                     ? Pincode::find($pincodeValue)
//                     : Pincode::where('pincode', $pincodeValue)
//                         ->where('city_id', $city->id)
//                         ->first();

//                 if (!$pincode) {
//                     throw new \Exception("Invalid pincode");
//                 }

//                 $beat = Beat::find(trim($row['beat']));

// if (!$beat) {
//     throw new \Exception('Invalid beat id: ' . $row['beat']);
// }
//                 $data = [
//                     'type' => $this->type,
//                     'sub_type' => $row['sub_type'] ?? null,
//                     'owner_name' => $row['owner_name'],
//                     'shop_name' => $row['shop_name'],
//                     'mobile_number' => $row['mobile_number'],
//                     'whatsapp_number' => $row['whatsapp_number'] ?? null,
//                     'vehicle_segment' => $row['vehicle_segment'] ?? null,
//                     'address_line' => $row['address_line'],
//                     'belt_area_market_name' => $row['belt_area_market_name'] ?? null,
//                     'gps_location' => $row['gps_location'] ?? null,
//                     'country_id' => $country?->id,
//                     'state_id' => $state?->id,
//                     'district_id' => $district?->id,
//                     'city_id' => $city?->id,
//                     'pincode_id' => $pincode?->id,
//                     'beat_id' => $beat?->id,
//                     'opportunity_status' => strtoupper($row['opportunity_status']),
//                 ];

//                 if (in_array($this->type, ['RETAILER', 'WORKSHOP'])) {
//                     $data['nistha_awareness_status'] = $row['awareness_status'];
//                 } else {
//                     $data['saathi_awareness_status'] = $row['awareness_status'];
//                 }

//                 SecondaryCustomer::updateOrCreate($data);

//             } catch (\Exception $e) {

//                 $this->errors[] = "Row $rowNumber → ".$e->getMessage();
//             }
//         }
//     }
// }




namespace App\Imports;

use App\Models\SecondaryCustomer;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;
use App\Models\Beat;
use App\Models\User;
use App\Models\MasterDistributor;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;


class SecondaryCustomersImport implements ToCollection, WithHeadingRow,WithChunkReading
{
    protected $type;
    protected $errors = [];
    protected $createdBy;

    public function __construct($type,$createdBy)
    {
        $this->type = strtoupper(trim($type));
        $this->createdBy = $createdBy;
    }
    public function chunkSize(): int
{
    return 500;
}

    public function collection(Collection $rows)
    {

        $countries = Country::all()->keyBy('id');

        $states = State::all()->keyBy('id');

        $districts = District::all()->keyBy('id');

        $cities = City::all()->keyBy('id');

        $pincodes = Pincode::all()->keyBy('id');

        $beats = Beat::all()->keyBy('id');

        $distributors = MasterDistributor::all()->keyBy('id');

        $users = User::pluck('id', 'employee_codes');

        $insertData = [];


        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                /** ================== REQUIRED FIELDS ================== */
                $requiredFields = [
                    'type', 'sub_type', 'owner_name', 'shop_name', 'mobile_number',
                    'vehicle_segment', 'opportunity_status', 'employee_code'
                ];

                foreach ($requiredFields as $field) {
                    if (empty($row[$field]) || trim($row[$field]) == '') {
                        throw new \Exception(ucwords(str_replace('_', ' ', $field)) . " is required");
                    }
                }

                /** ================== STRICT TYPE VALIDATION ================== */
                $allowedTypes = ['MECHANIC', 'GARAGE', 'RETAILER', 'WORKSHOP'];
                $type = trim($row['type']);

                if (!in_array($type, $allowedTypes)) {
                    throw new \Exception("Invalid type. Must be exactly: MECHANIC, GARAGE, RETAILER or WORKSHOP (case-sensitive)");
                }

                /** ================== STRICT SUB-TYPE VALIDATION ================== */
                $subType = trim($row['sub_type']);

                $allowedSubTypes = [
                    'MECHANIC' => [
                        'Two-Wheeler Mechanic', 'Car / 4W Mechanic', 'HCV-LCV Mechanic',
                        'Tractor / Agri Machine', 'Diesel/FIP Mechanic'
                    ],
                    'GARAGE' => [
                        'ROADSIDE GARAGE', 'MULTI EMPLOYEE GARAGE', 'ONE-MAN GARAGE'
                    ],
                    'RETAILER' => [
                        'AUTO SPARE PARTS RETAILER', 'LUBRICANT RETAILER', 'TWO WHEELER PARTS SHOP',
                        'CAR ACCESSORIES & PARTS SHOP', 'TRACTOR PARTS SHOP', 'HCV-LCV SHOP'
                    ],
                    'WORKSHOP' => [
                        'Lube & Filter Change Workshop', 'Two-Wheeler Service Workshop',
                        'Car Service Workshop', 'HCV - LCV Workshop'
                    ]
                ];

                if (!in_array($subType, $allowedSubTypes[$type])) {
                    throw new \Exception("Invalid sub_type for {$type}. Allowed: " . implode(', ', $allowedSubTypes[$type]));
                }

                /** ================== STRICT VEHICLE SEGMENT VALIDATION ================== */
                $vehicleSegment = trim($row['vehicle_segment']);

                $allowedVehicleSegments = [
                    '2W',
                    '3W',
                    'AGRICULTURE – Tractor',
                    'COOLANT',
                    'EARTH MOVING EQUIPMENT',
                    'HCV',
                    'LCV',
                    'LUBRICANT',
                    'PASSENGER VEHICLE (PV)'
                ];

                if (!in_array($vehicleSegment, $allowedVehicleSegments)) {
                    throw new \Exception(
                        "Invalid vehicle_segment. Allowed options are: " . 
                        implode(', ', $allowedVehicleSegments)
                    );
                }

                /** ================== STRICT OPPORTUNITY STATUS VALIDATION ================== */
                $opportunityStatus = trim($row['opportunity_status']);

                $allowedOpportunityStatuses = [
                    'COLD',
                    'WARM',
                    'HOT',
                    'LOST',
                    'EXISTING'
                ];

                if (!in_array($opportunityStatus, $allowedOpportunityStatuses, true)) {
                    throw new \Exception(
                        "Invalid opportunity_status. Allowed values (case-sensitive): " .
                        implode(', ', $allowedOpportunityStatuses)
                    );
                }

                /** ================== MOBILE NUMBER VALIDATION ================== */
                if (!preg_match('/^[0-9]{10}$/', $row['mobile_number'])) {
                    throw new \Exception("Mobile number must be exactly 10 digits");
                }

                /** ================== EMPLOYEE CODE ================== */
                $employeeCodes = array_map('trim', explode(',', $row['employee_code']));
                $employees = User::whereIn('employee_codes', $employeeCodes)->pluck('id');

                // if ($employees->count() != count($employeeCodes)) {
                //     throw new \Exception("One or more employee codes are invalid");
                // }

                // ================== Location Validations (Country, State, District, City, Pincode, Beat) ==================
                // Your existing location logic here (kept same as before)

                $country = null;

                if (!empty($row['country_id'])) {
                    $country = $countries[$row['country_id']] ?? null;

                    // if (!$country) {
                    //     throw new \Exception("Invalid country_id");
                    // }
                }

                $state = null;

                if (!empty($row['state_id'])) {
                    $state = $states[$row['state_id']] ?? null;

                    // if (!$state) {
                    //     throw new \Exception("Invalid state_id");
                    // }

                    // if ($country && $state->country_id != $country->id) {
                    //     throw new \Exception("state_id does not belong to country_id");
                    // }
                }

                $district = null;
                
                // dd(!empty($row['district_id']), $row['district_id']);

                if (!empty($row['district_id'])) {
                    $district = $districts[$row['district_id']] ?? null;

                    // if (!$district) {
                    //     throw new \Exception("Invalid district_id");
                    // }

                    // if ($state && $district->state_id != $state->id) {
                    //     throw new \Exception("district_id does not belong to state_id");
                    // }
                }

                $city = null;

                if (!empty($row['city_id'])) {
                    $city = $cities[$row['city_id']] ?? null;

                    // if (!$city) {
                    //     throw new \Exception("Invalid city_id");
                    // }

                    // if ($district && $city->district_id != $district->id) {
                    //     throw new \Exception("city_id does not belong to district_id");
                    // }
                }

                $pincode = null;

                if (!empty($row['pincode_id'])) {
                    $pincode = $pincodes[$row['pincode_id']] ?? null;

                    // if (!$pincode) {
                    //     throw new \Exception("Invalid pincode_id");
                    // }

                    // if ($city && $pincode->city_id != $city->id) {
                    //     throw new \Exception("pincode_id does not belong to city_id");
                    // }
                }

                $beat = null;

                if (!empty($row['beat_id'])) {
                    $beat = $beats[$row['beat_id']] ?? null;

                    // if (!$beat) {
                    //     throw new \Exception("Invalid beat_id");
                    // }

                    // ✅ OPTIONAL: mapping check (recommended)
                    // if ($city && $beat->city_id != $city->id) {
                    //     throw new \Exception("beat_id does not belong to city_id");
                    // }
                }

                // Distributor Logic (unchanged)
                // $distributor = null;
                // if (!empty($row['ditributor_id'])) {
                //     $distributor = MasterDistributor::find($row['ditributor_id']);
                // } elseif (!empty($row['distributor_name'])) {
                //     $distributor = MasterDistributor::where('trade_name', $row['distributor_name'])
                //         ->orWhere('legal_name', $row['distributor_name'])->first();
                // }

                $distributor = null;

                if (!empty($row['distributor_id'])) {

                    $distributor = MasterDistributor::find(trim($row['distributor_id']));

                    if (!$distributor) {
                        throw new \Exception("Invalid distributor_id: " . $row['distributor_id']);
                    }
                }

                $gmapAddress = null;

                if (!empty($row['gps_location'])) {

                    $coords = explode(',', $row['gps_location']);

                    if (count($coords) == 2) {

                        $latitude = trim($coords[0]);
                        $longitude = trim($coords[1]);

                        // if (is_numeric($latitude) && is_numeric($longitude)) {

                        //     $gmapAddress = getLatLongToAddress($latitude, $longitude);
                        // }
                    }
                }

                /** ================== AWARENESS STATUS ================== */
                $awareness = trim(str_replace(['Saathi:', 'Nistha:'], '', $row['awareness_status'] ?? ''));
                $createdAt = !empty($row['created_at']) 
                    ? Carbon::parse($row['created_at']) 
                    : now();

                $updatedAt = !empty($row['updated_at']) 
                    ? Carbon::parse($row['updated_at']) 
                    : now();
                /** ================== FINAL DATA ARRAY ================== */
                $data = [
                    'type'                    => $this->type,
                    'sub_type'                => $subType ?? null,
                    'owner_name'              => $row['owner_name'] ?? '',
                    'shop_name'               => $row['shop_name'] ?? '',
                    'distributor_name' => $distributor ? $distributor->id : '',
                    'mobile_number'           => $row['mobile_number'] ?? '',
                    'whatsapp_number'         => $row['whatsapp_number'] ?? null,
                    'vehicle_segment'         => $vehicleSegment ?? null,
                    'sales_exception_assignment' => $row['sales_exception_assignment'] ?? null,
                    'address_line'            => $row['address_line'] ?? '',
                    'belt_area_market_name'   => $row['belt_area_market_name'] ?? null,
                    'gps_location'            => $row['gps_location'] ?? null,
                    'country_id'  => $country?->id ?? '',
                    'gmap' => $gmapAddress ?? null,
                    'state_id'    => $state?->id ?? '',
                    'district_id' => $district ? $district->id : '0',
                    'city_id'     => $city ? $city->id : '0',
                    'pincode_id'  => $pincode ? $pincode->id : '0',
                    'beat_id'     => $beat?->id ?? null,
                    'distributor_name'        => $distributor?->id ?? null,
                    'employee_id'             => $employees->implode(','),
                    'opportunity_status' => $opportunityStatus ?? 'COLD',
                    'created_by' => $this->createdBy,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ];

                if (in_array($this->type, ['RETAILER', 'WORKSHOP'])) {
                    $data['nistha_awareness_status'] = $awareness ?? null;
                } else {
                    $data['saathi_awareness_status'] = $awareness ?? "Not Done";
                }

                /** ================== UPSERT RECORD ================== */
                if (!empty($row['id'])) {

                    $customer = SecondaryCustomer::find($row['id']);

                    if (!$customer) {
                        throw new \Exception("Invalid ID: record not found");
                    }

                    $customer->update($data);

                } else {

                    // SecondaryCustomer::create($data);
                    $insertData[] = $data;
                }

            } catch (\Exception $e) {
                $this->errors[] = "Row $rowNumber → " . $e->getMessage();
            }
        }
        SecondaryCustomer::upsert(
        $insertData,
        ['mobile_number'],
        [
            'type',
            'sub_type',
            'owner_name',
            'shop_name',
            'whatsapp_number',
            'vehicle_segment',
            'sales_exception_assignment',
            'address_line',
            'belt_area_market_name',
            'gps_location',
            'country_id',
            'gmap',
            'state_id',
            'district_id',
            'city_id',
            'pincode_id',
            'beat_id',
            'distributor_name',
            'employee_id',
            'opportunity_status',
            'created_by',
            'updated_at',
            'nistha_awareness_status',
            'saathi_awareness_status'
        ]
    );
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
