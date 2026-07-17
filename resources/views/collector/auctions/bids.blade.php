@extends('layouts.app')

@section('title', 'My Bid History — Artevo')

@section('content')
    <section class="av-section" style="padding-top: var(--space-10);">
        <div class="container">
            <a href="{{ route('dashboard') }}" style="display:inline-block; margin-bottom:var(--space-6); color:var(--ink-500); font-size:var(--text-sm); font-weight:500;">
                &larr; Back to Dashboard
            </a>

            <div style="margin-bottom: var(--space-8);">
                <x-tag>Collector</x-tag>
                <h1 style="margin-top: var(--space-4);">My Bid History</h1>
                <p>A log of all bids you have placed on Artevo.</p>
            </div>

            @if ($bids->isEmpty())
                <p style="color: var(--ink-500);">You haven't placed any bids yet.</p>
            @else
                <div style="background: var(--white); border: 1px solid var(--color-border); border-radius: var(--radius-md); overflow: hidden;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--color-bg-alt); border-bottom: 1px solid var(--color-border);">
                                <th style="padding: var(--space-3) var(--space-4); font-weight: 600;">Auction</th>
                                <th style="padding: var(--space-3) var(--space-4); font-weight: 600;">Amount</th>
                                <th style="padding: var(--space-3) var(--space-4); font-weight: 600;">Date</th>
                                <th style="padding: var(--space-3) var(--space-4); font-weight: 600;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bids as $bid)
                                <tr style="border-bottom: 1px solid var(--color-border);">
                                    <td style="padding: var(--space-3) var(--space-4);">
                                        <a href="{{ route('auctions.show', $bid->auction) }}" style="color: var(--color-heading); font-weight: 500;">
                                            {{ $bid->auction->title }}
                                        </a>
                                    </td>
                                    <td style="padding: var(--space-3) var(--space-4);">
                                        {{ $bid->auction->currency }} {{ number_format($bid->amount, 2) }}
                                    </td>
                                    <td style="padding: var(--space-3) var(--space-4); color: var(--ink-500);">
                                        {{ $bid->created_at->format('M j, Y H:i') }}
                                    </td>
                                    <td style="padding: var(--space-3) var(--space-4);">
                                        @if($bid->is_winning)
                                            <span class="av-tag av-tag--success">Winning</span>
                                        @else
                                            <span class="av-tag av-tag--muted">Outbid</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
@endsection
