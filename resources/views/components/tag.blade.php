@props(['variant' => null]) {{-- null | muted | success | danger --}}

<span {{ $attributes->merge(['class' => 'av-tag' . ($variant ? ' av-tag--' . $variant : '')]) }}>
    {{ $slot }}
</span>
