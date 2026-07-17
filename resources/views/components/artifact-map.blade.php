@props([
    'artifact',
    'height' => '320px',
])

@php
    $hasOrigin    = $artifact->hasOriginCoordinates();
    $hasDiscovery = $artifact->hasDiscoveryCoordinates();
    $hasAny       = $hasOrigin || $hasDiscovery;

    $mapId = 'artifact-map-' . $artifact->id;

    // Build marker config for JS
    $markers = [];

    if ($hasOrigin) {
        $markers[] = [
            'lat'    => $artifact->origin_latitude,
            'lng'    => $artifact->origin_longitude,
            'label'  => 'Origin',
            'detail' => $artifact->country_of_origin ?? 'Country of origin',
            'color'  => '#d4af37',   // gold
            'type'   => 'origin',
        ];
    }

    if ($hasDiscovery) {
        $markers[] = [
            'lat'    => $artifact->discovery_latitude,
            'lng'    => $artifact->discovery_longitude,
            'label'  => 'Discovery Site',
            'detail' => $artifact->discovery_location ?? 'Discovery location',
            'color'  => '#10b981',   // teal
            'type'   => 'discovery',
        ];
    }

    $markersJson = json_encode($markers);

    // Map center: prefer origin, fall back to discovery
    $centerLat = $hasOrigin ? $artifact->origin_latitude  : $artifact->discovery_latitude;
    $centerLng = $hasOrigin ? $artifact->origin_longitude : $artifact->discovery_longitude;
    $zoom      = $hasOrigin && $hasDiscovery ? 4 : 8;
@endphp

@if($hasAny)
<div class="mt-6">
    <span class="av-card__eyebrow" style="display:block; margin-bottom:var(--space-2);">
        Geographic Context
    </span>

    {{-- Legend --}}
    <div class="flex" style="gap:var(--space-4); margin-bottom:var(--space-3); flex-wrap:wrap;">
        @if($hasOrigin)
            <span style="font-size:.78rem; color:var(--color-muted); display:flex; align-items:center; gap:6px;">
                <span style="width:12px;height:12px;border-radius:50%;background:#d4af37;display:inline-block;flex-shrink:0;"></span>
                Origin{{ $artifact->country_of_origin ? ': ' . $artifact->country_of_origin : '' }}
            </span>
        @endif
        @if($hasDiscovery)
            <span style="font-size:.78rem; color:var(--color-muted); display:flex; align-items:center; gap:6px;">
                <span style="width:12px;height:12px;border-radius:50%;background:#10b981;display:inline-block;flex-shrink:0;"></span>
                Discovery{{ $artifact->discovery_location ? ': ' . $artifact->discovery_location : '' }}
            </span>
        @endif
    </div>

    <div id="{{ $mapId }}"
         style="width:100%; height:{{ $height }}; border-radius:var(--radius-md); overflow:hidden; border:1px solid var(--color-border); background:var(--color-surface-2); position:relative;">
        <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:var(--color-muted); font-size:.85rem;" id="{{ $mapId }}-loader">
            Loading map…
        </div>
    </div>
</div>

@once
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@endonce

@once
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endonce

<script>
(function () {
    'use strict';

    const mapId   = {{ Js::from($mapId) }};
    const markers = {!! $markersJson !!};
    const defaultZoom = {{ $zoom }};

    function makeIcon(color) {
        return L.divIcon({
            html: '<div style="width:24px;height:24px;background:' + color + ';border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.4);"></div>',
            className: '',
            iconSize: [24, 24],
            iconAnchor: [12, 24],
            popupAnchor: [0, -28],
        });
    }

    function initMap() {
        const el = document.getElementById(mapId);
        if (!el || typeof L === 'undefined') return;

        const loader = document.getElementById(mapId + '-loader');
        if (loader) loader.remove();

        const firstMarker = markers[0];
        const map = L.map(mapId, {
            center: [firstMarker.lat, firstMarker.lng],
            zoom: defaultZoom,
            scrollWheelZoom: false,
        });

        // Stadia Maps — always English labels regardless of geographic region
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png?language=en', {
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 20,
        }).addTo(map);

        const bounds = [];

        markers.forEach(function (m) {
            const popup = L.popup({ maxWidth: 200 }).setContent(
                '<div style="font-family:inherit;">'
                + '<p style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:' + m.color + ';margin:0 0 4px;">' + m.label + '</p>'
                + '<p style="font-size:.88rem;font-weight:600;margin:0;">' + m.detail + '</p>'
                + '</div>'
            );

            L.marker([m.lat, m.lng], { icon: makeIcon(m.color) })
                .bindPopup(popup)
                .addTo(map);

            bounds.push([m.lat, m.lng]);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    if (typeof L !== 'undefined') {
        initMap();
    } else {
        let attempts = 0;
        const interval = setInterval(function () {
            if (typeof L !== 'undefined' || ++attempts > 50) {
                clearInterval(interval);
                if (typeof L !== 'undefined') initMap();
            }
        }, 200);
    }
})();
</script>
@endif
