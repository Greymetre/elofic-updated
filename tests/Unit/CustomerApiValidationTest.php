<?php

namespace Tests\Unit;

use App\Support\CustomerApiValidation;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CustomerApiValidationTest extends TestCase
{
    public function test_optional_mobile_fields_accept_missing_and_empty_values(): void
    {
        $rules = ['mobile' => CustomerApiValidation::optionalMobileRules()];

        $this->assertTrue(Validator::make([], $rules)->passes());
        $this->assertTrue(Validator::make(['mobile' => ''], $rules)->passes());
        $this->assertTrue(Validator::make(['mobile' => '9876543210'], $rules)->passes());
        $this->assertFalse(Validator::make(['mobile' => '12345'], $rules)->passes());
    }

    public function test_location_validation_accepts_valid_coordinates_and_rejects_invalid_ranges(): void
    {
        $valid = [
            'gps_location' => '22.719568,75.857727',
            'latitude' => 22.719568,
            'longitude' => 75.857727,
            'is_current_location' => 1,
        ];

        $this->assertTrue(Validator::make($valid, CustomerApiValidation::locationRules())->passes());
        $this->assertFalse(Validator::make(array_merge($valid, ['latitude' => 91]), CustomerApiValidation::locationRules())->passes());
        $this->assertFalse(Validator::make(array_merge($valid, ['longitude' => -181]), CustomerApiValidation::locationRules())->passes());
    }

    public function test_existing_opportunity_status_is_valid_and_unknown_status_is_rejected(): void
    {
        $rules = ['opportunity_status' => CustomerApiValidation::opportunityStatusRules()];

        $this->assertTrue(Validator::make(['opportunity_status' => 'EXISTING'], $rules)->passes());
        $this->assertFalse(Validator::make(['opportunity_status' => 'INVALID'], $rules)->passes());
    }

    public function test_multiple_vehicle_segments_are_validated(): void
    {
        $this->assertTrue(Validator::make(
            ['vehicle_segment' => ['2W', 'HCV']],
            CustomerApiValidation::vehicleSegmentRules()
        )->passes());

        $this->assertFalse(Validator::make(
            ['vehicle_segment' => ['2W', 'UNKNOWN']],
            CustomerApiValidation::vehicleSegmentRules()
        )->passes());
    }
}
