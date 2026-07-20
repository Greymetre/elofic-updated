<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class VehicleSegmentArray implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return array_values($value);
        }

        $decoded = json_decode((string) $value, true);
        $segments = is_array($decoded) ? $decoded : explode(',', (string) $value);

        return $this->normalize($segments);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return json_encode([]);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : explode(',', $value);
        }

        return json_encode($this->normalize(is_array($value) ? $value : [$value]));
    }

    private function normalize(array $segments): array
    {
        return array_values(array_unique(array_filter(array_map(
            static fn ($segment) => trim((string) $segment),
            $segments
        ), static fn ($segment) => $segment !== '')));
    }
}
