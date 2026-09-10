@props(['connected' => false])

<a
    {{ $attributes->merge(['href' => $connected ? '#' : route('gmail.redirect')]) }}
    @if ($connected) onclick="event.preventDefault(); document.getElementById('gmail-scan-form').submit();" @endif
    class="group relative inline-flex items-center gap-3 overflow-hidden rounded-xl border border-neutral-800 bg-neutral-950 px-6 py-3.5 text-sm font-semibold tracking-wide text-neutral-100 shadow-[0_0_20px_-5px_rgba(249,115,22,0.4)] transition-all duration-300 hover:border-orange-500/60 hover:bg-black hover:shadow-[0_0_35px_-5px_rgba(249,115,22,0.7)] focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 focus-visible:ring-offset-neutral-950"
>
    <span
        class="pointer-events-none absolute inset-0 bg-gradient-to-r from-transparent via-orange-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
        aria-hidden="true"
    ></span>

    <span class="relative flex h-9 w-9 items-center justify-center rounded-lg bg-neutral-900 ring-1 ring-neutral-800 transition group-hover:ring-orange-500/50">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M22 6.5v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-11" stroke="#f97316" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M2.2 6.3 12 13l9.8-6.7" stroke="#f97316" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2 6.2a2 2 0 0 1 2-1.7h16a2 2 0 0 1 2 1.7" stroke="#737373" stroke-width="1.2" stroke-linecap="round" opacity="0.6"/>
        </svg>
    </span>

    
        <button class="text-neutral-100">{{ $connected ? 'Scan My Inbox' : 'Connect Gmail' }}</button>
    

    <svg
        class="relative ml-1 h-4 w-4 text-orange-500 transition-transform duration-300 group-hover:translate-x-1"
        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
    >
        <path d="M5 12h14M13 6l6 6-6 6"/>
    </svg>
</a>

@if ($connected)
    <form id="gmail-scan-form" action="{{ route('gmail.scan') }}" method="POST" class="hidden">
        @csrf
    </form>

    <form action="{{ route('gmail.disconnect') }}" method="POST" class="mt-2">
        @csrf
        <button type="submit" class="text-[11px] font-medium text-neutral-600 transition-colors hover:text-red-400 focus:outline-none">
            Disconnect Gmail
        </button>
    </form>
@endif