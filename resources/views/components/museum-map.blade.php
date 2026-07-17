@props([
    'museum' => null,        // single Museum model — for detail page
    'museums' => null,       // JSON array string for directory page
    'height' => '360px',
    'zoom'   => 13,
])

@php
    $mapId    = 'museum-map-' . uniqid();
    $isSingle = $museum !== null;

    if ($isSingle) {
        $lat  = $museum->latitude;
        $lng  = $museum->longitude;
        $name = $museum->name;
        $url  = route('museums.show', $museum);
        $city = trim(($museum->city ?? '') . ', ' . ($museum->country ?? ''), ', ');

        // Build a JS-safe marker config
        $markersJs = json_encode([[
            'lat'  => $lat,
            'lng'  => $lng,
            'name' => $name,
            'url'  => $url,
            'city' => $city,
        ]]);
        $centerLat = $lat;
        $centerLng = $lng;
    } else {
        // Directory page — pre-encoded from MuseumController
        $markersJs = $museums;
        $centerLat = 20;  // approximate world center
        $centerLng = 0;
        $zoom      = 2;
    }

    // Don't render if no coordinates
    $hasData = $isSingle ? ($museum->latitude && $museum->longitude) : (! empty($museums) && $museums !== '[]');
@endphp

@if($hasData)
<div id="{{ $mapId }}" style="width:100%; height:{{ $height }}; border-radius:var(--radius-md); overflow:hidden; border:1px solid var(--color-border); position:relative; background:var(--color-surface-2);">
    <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:var(--color-muted); font-size:.85rem;" id="{{ $mapId }}-loader">
        Loading map…
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
    const markers = {!! $markersJs !!};
    const zoom    = {{ $zoom }};
    const lat     = {{ $centerLat ?? 20 }};
    const lng     = {{ $centerLng ?? 0 }};

    function initMap () {
        const el = document.getElementById(mapId);
        if (! el) return;

        // Remove loader
        const loader = document.getElementById(mapId + '-loader');
        if (loader) loader.remove();

        const map = L.map(mapId, {
            center: [lat, lng],
            zoom: zoom,
            scrollWheelZoom: false,
            zoomControl: true,
        });

        // Tile layer: Stadia Maps AlidadeSmooth — always English labels worldwide.
        // Unlike CartoDB/OSM defaults which use local-script names (Arabic, Chinese etc),
        // Stadia uses OSM's name:en field so labels are always in English.
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png?language=en', {
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a> &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 20,
        }).addTo(map);

        // Custom golden marker icon
        const museumIcon = L.divIcon({
            html: '<div style="width:28px;height:28px;background:linear-gradient(135deg,#d4af37,#b8943a);border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.4);"></div>',
            className: '',
            iconSize: [28, 28],
            iconAnchor: [14, 28],
            popupAnchor: [0, -32],
        });

        let bounds = [];

        markers.forEach(function (m) {
            if (! m.lat || ! m.lng) return;

            const popup = L.popup({ maxWidth: 240 }).setContent(
                '<div style="font-family:inherit;">'
                + '<p style="font-weight:700;font-size:.9rem;margin:0 0 4px;">' + m.name + '</p>'
                + (m.city ? '<p style="font-size:.78rem;color:#666;margin:0 0 8px;">' + m.city + '</p>' : '')
                + (m.url ? '<a href="' + m.url + '" style="font-size:.8rem;font-weight:600;color:#b8943a;text-decoration:none;">View profile →</a>' : '')
                + '</div>'
            );

            L.marker([m.lat, m.lng], { icon: museumIcon })
                .bindPopup(popup)
                .addTo(map);

            bounds.push([m.lat, m.lng]);
        });

        // Fit map to all markers if showing directory
        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }
    }

    // Init when Leaflet is ready
    if (typeof L !== 'undefined') {
        initMap();
    } else {
        // Poll briefly for Leaflet to load from CDN
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
