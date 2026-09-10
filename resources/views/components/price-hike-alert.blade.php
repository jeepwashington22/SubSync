@props(['hike'])

{{-- Compact price-hike alert pill rendered inside the dashboard header.
     Example usage:
         @if (! empty($priceHikes))
             <x-price-hike-alert :hike="$priceHikes[0]" />
         @endif --}}

<div class="flex items-center gap-2 rounded-xl border border-amber-400/40 bg-amber-400/[0.08] px-3 py-1.5 transition hover:bg-amber-400/[0.12]">
    <svg class="h-4 w-4 shrink-0 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        <line x1="12" x2="12" y1="9" y2="13"/>
        <line x1="12" x2="12.01" y1="17" y2="17"/>
    </svg>
    <span class="text-xs font-semibold text-amber-200">
        {{ $hike['subcription']->name }} went up
    </span>
    <span class="text-xs font-mono text-amber-100">
        ₱{{ number_format($hike['increase'], 2) }}
        <span class="text-[#68685f]">({{ number_format($hike['percent_change'], 1) }}%)</span>
    </span>
    <span class="ml-auto text-[11px] text-[#68685f]">
        {{ '₱' . number_format($hike['previous_amount'], 2) }} → {{ '₱' . number_format($hike['current_amount'], 2) }}
    </span>
</div>
