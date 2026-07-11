@props(['eyebrow' => null])

<div {{ $attributes->merge(['class' => 'av-section-heading']) }}>
    @if ($eyebrow)
        <span class="av-section-heading__eyebrow">{{ $eyebrow }}</span>
    @endif
    {{ $slot }}
</div>
