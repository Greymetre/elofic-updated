<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class CustomerApiValidation
{
    public const OPPORTUNITY_STATUSES = ['COLD', 'WARM', 'HOT', 'LOST', 'EXISTING'];

    public const VEHICLE_SEGMENTS = [
        '2W',
        '3W',
        'AGRICULTURE – Tractor',
        'COOLANT',
        'EARTH MOVING EQUIPMENT',
        'HCV',
        'LCV',
        'LUBRICANT',
        'PASSENGER VEHICLE (PV)',
    ];

    public static function optionalMobileRules(): array
    {
        return ['nullable', 'regex:/^[6-9][0-9]{9}$/'];
    }

    public static function locationRules(): array
    {
        return [
            'gps_location' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'is_current_location' => ['required', 'boolean'],
        ];
    }

    public static function opportunityStatusRules(): array
    {
        return ['required', Rule::in(self::OPPORTUNITY_STATUSES)];
    }

    public static function vehicleSegmentRules(): array
    {
        return [
            'vehicle_segment' => ['nullable', 'array'],
            'vehicle_segment.*' => ['string', Rule::in(self::VEHICLE_SEGMENTS)],
        ];
    }
}
