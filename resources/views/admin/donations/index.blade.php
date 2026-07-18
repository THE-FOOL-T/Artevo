@extends('layouts.app')

@section('title', 'Donation Requests — Admin — Artevo')
@section('meta_description', 'Review and manage all artifact donation requests on the Artevo platform.')

@section('content')

<section class="av-section av-section--white" style="padding-top: var(--space-10);">
    <div class="container">
        <x-tag>Administrator</x-tag>
        <h1 style="margin-top: var(--space-4);">Donation Requests</h1>
        <p style="color:var(--color-muted); max-width:540px;">
            Review and manage artifact donation requests. Approve, reject, or transfer ownership.
        </p>

        @if(session('success'))
            <x-alert type="success" class="mt-4">{{ session('success') }}</x-alert>
        @endif
        @if(session('error'))
            <x-alert type="error" class="mt-4">{{ session('error') }}</x-alert>
        @endif

        {{-- ── Status tabs ─────────────────────────────────────────────────── --}}
        @php
            $tabs = [
                'pending'     => ['label' => 'Pending',     'color' => '#d97706'],
                'approved'    => ['label' => 'Approved',    'color' => '#818cf8'],
                'transferred' => ['label' => 'Transferred', 'color' => '#10b981'],
                'rejected'    => ['label' => 'Rejected',    'color' => '#ef4444'],
                'all'         => ['label' => 'All',         'color' => 'var(--color-muted)'],
            ];
        @endphp
        <div class="flex" style="gap:var(--space-2); flex-wrap:wrap; margin-top:var(--space-6); border-bottom:1px solid var(--color-border); padding-bottom:var(--space-2);">
            @foreach($tabs as $key => $tab)
                <a href="{{ route('admin.donations.index', ['status' => $key]) }}"
                   style="padding:6px 16px; border-radius:var(--radius-sm); font-size:.85rem; font-weight:600; text-decoration:none;
                          {{ $status === $key
                             ? 'background:rgba(212,175,55,.15); color:var(--brass-700); border:1px solid rgba(212,175,55,.3);'
                             : 'color:var(--color-muted); border:1px solid transparent;' }}">
                    {{ $tab['label'] }}
                    @if($key !== 'all' && isset($counts[$key]))
                        <span style="font-size:.72rem; font-weight:700; padding:1px 7px; border-radius:999px; background:rgba(212,175,55,.12); color:var(--brass-700); margin-left:4px;">
                            {{ $counts[$key] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- ── Donation table ───────────────────────────────────────────────── --}}
        @php
            $badgeColors = [
                'pending'     => ['bg'=>'rgba(245,158,11,.12)', 'color'=>'#d97706', 'border'=>'rgba(245,158,11,.3)'],
                'approved'    => ['bg'=>'rgba(99,102,241,.12)', 'color'=>'#818cf8', 'border'=>'rgba(99,102,241,.3)'],
                'transferred' => ['bg'=>'rgba(16,185,129,.12)', 'color'=>'#10b981', 'border'=>'rgba(16,185,129,.3)'],
                'rejected'    => ['bg'=>'rgba(239,68,68,.1)',   'color'=>'#ef4444', 'border'=>'rgba(239,68,68,.25)'],
            ];
        @endphp

        @if($donations->isEmpty())
            <div style="text-align:center; padding:var(--space-16) 0; color:var(--color-muted);">
                <div style="font-size:3rem; margin-bottom:var(--space-4);">🎁</div>
                <h3 style="font-size:1.1rem; margin-bottom:var(--space-2);">No {{ $status === 'all' ? '' : $status }} donation requests</h3>
                <p>Nothing to review right now.</p>
            </div>
        @else
            <div style="overflow-x:auto; margin-top:var(--space-4);">
                <table style="width:100%; border-collapse:collapse; font-size:.88rem;">
                    <thead>
                        <tr style="border-bottom:2px solid var(--color-border); text-align:left;">
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Artifact</th>
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Donor</th>
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Museum</th>
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Status</th>
                            <th style="padding:var(--space-3) var(--space-4); color:var(--color-muted); font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">Submitted</th>
                            <th style="padding:var(--space-3) var(--space-4);"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($donations as $donation)
                        @php $bc = $badgeColors[$donation->status] ?? ['bg'=>'var(--color-surface-2)','color'=>'var(--color-muted)','border'=>'var(--color-border)']; @endphp
                        <tr style="border-bottom:1px solid var(--color-border);">
                            <td style="padding:var(--space-4);">
                                <div class="flex" style="align-items:center; gap:var(--space-3);">
                                    @if($donation->artifact?->primaryImage())
                                        <img src="{{ $donation->artifact->primaryImage()->url() }}"
                                             alt="{{ $donation->artifact->name }}"
                                             style="width:40px; height:40px; object-fit:cover; border-radius:var(--radius-sm); flex-shrink:0;">
                                    @else
                                        <div style="width:40px; height:40px; background:var(--color-surface-2); border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center;">🏺</div>
                                    @endif
                                    <div>
                                        <p style="font-weight:600; margin:0; font-size:.88rem;">{{ $donation->artifact?->name ?? '(deleted)' }}</p>
                                        <p style="font-size:.72rem; color:var(--color-muted); margin:0;">{{ $donation->artifact?->artifact_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:var(--space-4);">
                                <p style="font-weight:600; margin:0;">{{ $donation->donor?->name }}</p>
                                <p style="font-size:.75rem; color:var(--color-muted); margin:0;">{{ $donation->donor?->email }}</p>
                            </td>
                            <td style="padding:var(--space-4);">
                                <p style="font-weight:600; margin:0;">{{ $donation->museum?->name }}</p>
                                <p style="font-size:.75rem; color:var(--color-muted); margin:0;">{{ $donation->museum?->city }}</p>
                            </td>
                            <td style="padding:var(--space-4);">
                                <span style="display:inline-flex; padding:3px 10px; border-radius:999px; font-size:.75rem; font-weight:600; background:{{ $bc['bg'] }}; color:{{ $bc['color'] }}; border:1px solid {{ $bc['border'] }};">
                                    {{ $donation->statusLabel() }}
                                </span>
                                @if($donation->isTransferred() && $donation->certificate_number)
                                    <p style="font-size:.68rem; color:var(--color-muted); margin:3px 0 0; font-family:var(--font-mono);">{{ $donation->certificate_number }}</p>
                                @endif
                            </td>
                            <td style="padding:var(--space-4); color:var(--color-muted); font-size:.82rem; white-space:nowrap;">
                                {{ $donation->created_at->format('M j, Y') }}
                            </td>
                            <td style="padding:var(--space-4); white-space:nowrap;">
                                <a href="{{ route('admin.donations.show', $donation) }}"
                                   style="color:var(--brass-700); font-weight:600; font-size:.82rem;">
                                    {{ $donation->isPending() ? 'Review →' : 'View →' }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:var(--space-8);">
                {{ $donations->appends(['status' => $status])->links() }}
            </div>
        @endif
    </div>
</section>

@endsection
