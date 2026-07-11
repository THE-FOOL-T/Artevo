@props(['type' => 'success'])

<div {{ $attributes->merge(['class' => 'av-alert av-alert--' . $type]) }} role="alert">
    @if ($type === 'success')
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
    @else
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    @endif
    <span>{{ $slot }}</span>
</div>
