<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeocodingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Internal JSON API for Nominatim geocoding.
 * Used by the museum create/edit form's "Auto-detect coordinates" button.
 * Requires authentication — curators and collectors only.
 */
class GeocodingController extends Controller
{
    public function __construct(protected GeocodingService $geo) {}

    /**
     * GET /api/geocode?address=Tahrir+Square,+Cairo,+Egypt
     * Returns { lat, lng, display_name }
     */
    public function geocode(Request $request): JsonResponse
    {
        $request->validate(['address' => 'required|string|min:3|max:500']);

        $result = $this->geo->geocode($request->string('address'));

        if (! $result) {
            return response()->json([
                'error' => 'Address not found. Try adding the city and country for better results.',
            ], 404);
        }

        return response()->json($result);
    }

    /**
     * GET /api/reverse-geocode?lat=30.0444&lng=31.2357
     */
    public function reverse(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $result = $this->geo->reverseGeocode(
            (float) $request->input('lat'),
            (float) $request->input('lng')
        );

        if (! $result) {
            return response()->json(['error' => 'Could not reverse geocode those coordinates.'], 404);
        }

        return response()->json($result);
    }
}
