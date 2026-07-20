<?php

namespace Tests\Unit;

use App\Models\SecondaryCustomer;
use PHPUnit\Framework\TestCase;

class VehicleSegmentArrayTest extends TestCase
{
    public function test_multiple_vehicle_segments_are_stored_and_returned_as_an_array(): void
    {
        $customer = new SecondaryCustomer();
        $customer->vehicle_segment = ['2W', 'HCV'];

        $this->assertSame(['2W', 'HCV'], $customer->vehicle_segment);
        $this->assertSame('["2W","HCV"]', $customer->getAttributes()['vehicle_segment']);
    }

    public function test_legacy_single_vehicle_segment_is_returned_as_an_array(): void
    {
        $customer = new SecondaryCustomer();
        $customer->setRawAttributes(['vehicle_segment' => '2W']);

        $this->assertSame(['2W'], $customer->vehicle_segment);
    }

    public function test_legacy_comma_separated_segments_are_returned_as_an_array(): void
    {
        $customer = new SecondaryCustomer();
        $customer->setRawAttributes(['vehicle_segment' => '2W, HCV']);

        $this->assertSame(['2W', 'HCV'], $customer->vehicle_segment);
    }

    public function test_location_values_are_cast_for_api_responses(): void
    {
        $customer = new SecondaryCustomer();
        $customer->setRawAttributes([
            'latitude' => '22.7195680',
            'longitude' => '75.8577270',
            'is_current_location' => 1,
        ]);

        $this->assertSame(22.719568, $customer->latitude);
        $this->assertSame(75.857727, $customer->longitude);
        $this->assertTrue($customer->is_current_location);
    }
}
