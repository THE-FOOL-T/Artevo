@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('title', 'List Artifact for Auction — ' . $artifact->name . ' — Artevo')

@section('content')

<div class="container" style="max-width: 680px; padding-top: var(--space-10); padding-bottom: var(--space-16);">

    {{-- Back link --}}
    @if($museum)
        <a href="{{ route('curator.museums.artifacts.edit', [$museum, $artifact]) }}"
           style="display:inline-flex; align-items:center; gap:var(--space-2); font-size:.875rem; color:var(--color-muted); text-decoration:none; margin-bottom:var(--space-6);">
            ← Back to artifact
        </a>
    @else
        <a href="{{ route('collector.artifacts.edit', $artifact) }}"
           style="display:inline-flex; align-items:center; gap:var(--space-2); font-size:.875rem; color:var(--color-muted); text-decoration:none; margin-bottom:var(--space-6);">
            ← Back to artifact
        </a>
    @endif

    <h1 style="font-size:1.75rem; margin-bottom:var(--space-2);">List for Auction</h1>
    <p style="color:var(--color-muted); margin-bottom:var(--space-8);">
        You are listing <strong style="color:var(--color-heading);">{{ $artifact->name }}</strong> for a timed auction.
        The auction will be saved as a draft — you can publish it when you're ready.
    </p>

    @if($errors->any())
    <div style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); border-radius:var(--radius-sm); padding:var(--space-4); margin-bottom:var(--space-6);">
        <p style="font-size:.875rem; font-weight:600; color:#ef4444; margin:0 0 var(--space-2);">Please fix the following errors:</p>
        <ul style="margin:0; padding-left:var(--space-4); font-size:.85rem; color:#ef4444;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST"
          action="{{ $museum
              ? route('curator.artifact-auction.store', [$museum, $artifact])
              : route('collector.artifact-auction.store', $artifact) }}">
        @csrf

        {{-- Title --}}
        <div class="av-field" style="margin-bottom:var(--space-5);">
            <label for="title" class="av-label">Auction Title <span style="color:var(--color-muted); font-weight:400;">(optional — defaults to artifact name)</span></label>
            <input type="text" name="title" id="title"
                   class="av-input @error('title') av-input--error @enderror"
                   value="{{ old('title', $artifact->name) }}"
                   placeholder="{{ $artifact->name }}"
                   maxlength="220">
            @error('title')<p class="av-field__error">{{ $message }}</p>@enderror
        </div>

        {{-- Description --}}
        <div class="av-field" style="margin-bottom:var(--space-5);">
            <label for="auction_description" class="av-label">Lot Description <span style="color:var(--color-muted); font-weight:400;">(optional)</span></label>
            <textarea name="description" id="auction_description"
                      class="av-input @error('description') av-input--error @enderror"
                      rows="4"
                      placeholder="Add any additional context for bidders…">{{ old('description') }}</textarea>
            @error('description')<p class="av-field__error">{{ $message }}</p>@enderror
        </div>

        {{-- Pricing row --}}
        <div style="display:grid; grid-template-columns:1fr 1fr 120px; gap:var(--space-4); margin-bottom:var(--space-5);">
            <div class="av-field">
                <label for="reserve_price" class="av-label">Reserve / Starting Bid <span style="color:#ef4444;">*</span></label>
                <input type="number" name="reserve_price" id="reserve_price"
                       class="av-input @error('reserve_price') av-input--error @enderror"
                       value="{{ old('reserve_price', '100.00') }}"
                       step="0.01" min="1" required>
                @error('reserve_price')<p class="av-field__error">{{ $message }}</p>@enderror
            </div>

            <div class="av-field">
                <label for="bid_increment" class="av-label">Bid Increment <span style="color:#ef4444;">*</span></label>
                <input type="number" name="bid_increment" id="bid_increment"
                       class="av-input @error('bid_increment') av-input--error @enderror"
                       value="{{ old('bid_increment', '10.00') }}"
                       step="0.01" min="0.01" required>
                @error('bid_increment')<p class="av-field__error">{{ $message }}</p>@enderror
            </div>

            <div class="av-field">
                <label for="currency" class="av-label">Currency</label>
                <select name="currency" id="currency" class="av-input">
                    @foreach(['USD','EUR','GBP','AED','JPY','AUD','CAD'] as $cur)
                        <option value="{{ $cur }}" {{ old('currency', 'USD') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Date/time row --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-8);">
            <div class="av-field">
                <label for="starts_at" class="av-label">Opens at <span style="color:#ef4444;">*</span></label>
                <input type="datetime-local" name="starts_at" id="starts_at"
                       class="av-input @error('starts_at') av-input--error @enderror"
                       value="{{ old('starts_at') }}"
                       required>
                @error('starts_at')<p class="av-field__error">{{ $message }}</p>@enderror
            </div>

            <div class="av-field">
                <label for="ends_at" class="av-label">Closes at <span style="color:#ef4444;">*</span></label>
                <input type="datetime-local" name="ends_at" id="ends_at"
                       class="av-input @error('ends_at') av-input--error @enderror"
                       value="{{ old('ends_at') }}"
                       required>
                @error('ends_at')<p class="av-field__error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Sniper guard note --}}
        <div style="background:rgba(212,175,55,.08); border:1px solid rgba(212,175,55,.2); border-radius:var(--radius-sm); padding:var(--space-4); margin-bottom:var(--space-8); font-size:.85rem; color:var(--color-muted); line-height:1.6;">
            <strong style="color:var(--color-gold);">ℹ️ Sniper protection enabled</strong><br>
            If a bid is placed within the last 5 minutes of the auction, the closing time will automatically extend by 5 minutes to give all bidders a fair chance.
        </div>

        {{-- Submit --}}
        <div class="flex" style="gap:var(--space-3);">
            <button type="submit" class="av-btn av-btn--primary">Save as Draft</button>
            <a href="{{ $museum
                    ? route('curator.museums.artifacts.edit', [$museum, $artifact])
                    : route('collector.artifacts.edit', $artifact) }}"
               class="av-btn av-btn--ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
