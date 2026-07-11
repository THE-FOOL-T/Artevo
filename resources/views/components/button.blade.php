@props([
    'variant' => 'primary', // primary | dark | ghost | outline-dark
    'href' => null,
    'type' => 'button',
    'block' => false,
])

@php
    $classes = 'av-btn av-btn--' . $variant . ($block ? ' av-btn--block' : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
