<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Wraps the Nominatim (OpenStreetMap) free geocoding API.
 * https://nominatim.org/release-docs/latest/api/Search/
 *
 * Rules:
 *  - Must set a meaningful User-Agent (required by Nominatim ToS).
 *  - Max 1 request/second — we cache all results aggressively.
 *  - No commercial bulk use (fine for this project).
 */
class GeocodingService
{
    protected const BASE = 'https://nominatim.openstreetmap.org/search';

    /**
     * Geocode a free-text address and return [lat, lng] or null.
     */
    public function geocode(string $address): ?array
    {
        if (blank($address)) return null;

        $cacheKey = 'geocode_' . md5(strtolower($address));

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($address) {
            $response = Http::timeout(10)
                ->withHeaders([
                    // Nominatim ToS requires a meaningful User-Agent identifying
                    // your application and a contact e-mail or URL.
                    'User-Agent' => 'Artevo/1.0 (art-museum-platform; contact@artevo.local)',
                    'Accept-Language' => 'en',
                ])
                ->get(self::BASE, [
                    'q'              => $address,
                    'format'         => 'json',
                    'limit'          => 1,
                    'addressdetails' => 0,
                ]);

            if (! $response->successful()) return null;

            $results = $response->json();

            if (empty($results)) return null;

            $first = $results[0];

            return [
                'lat'          => (float) $first['lat'],
                'lng'          => (float) $first['lon'],
                'display_name' => $first['display_name'] ?? $address,
                'type'         => $first['type'] ?? '',
            ];
        });
    }

    /**
     * Reverse-geocode coordinates into a human-readable address.
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        $cacheKey = 'rev_geocode_' . md5("{$lat},{$lng}");

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($lat, $lng) {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent'      => 'Artevo/1.0 (art-museum-platform; contact@artevo.local)',
                    'Accept-Language' => 'en',
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat'    => $lat,
                    'lon'    => $lng,
                    'format' => 'json',
                ]);

            if (! $response->successful()) return null;

            $data = $response->json();

            return [
                'display_name' => $data['display_name'] ?? '',
                'city'         => $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['village'] ?? '',
                'country'      => $data['address']['country'] ?? '',
                'postcode'     => $data['address']['postcode'] ?? '',
            ];
        });
    }
}
