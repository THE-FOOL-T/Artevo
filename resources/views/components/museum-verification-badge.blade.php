@props(['museum'])

@if ($museum->isVerified())
    <x-tag variant="success" class="av-tag--pill">Verified Institution</x-tag>
@elseif ($museum->isRejected())
    <x-tag variant="danger" class="av-tag--pill">Verification Rejected</x-tag>
@else
    <x-tag variant="muted" class="av-tag--pill">Verification Pending</x-tag>
@endif
