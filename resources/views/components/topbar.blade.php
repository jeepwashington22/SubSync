@props([
    'subtitle' => 'Workspace',
    'title' => '',
])

@php
    // Notifications are computed here so the bell works on every page.
    $user = Auth::user();
    $flaggedCategories = ['accidental', 'cancelled', 'canceled'];
    $bellSubscriptions = $user->subcriptions()
        ->whereNotIn('category', $flaggedCategories)
        ->whereBetween('billing_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
        ->orderBy('billing_date')
        ->get();
@endphp

<header class="sticky top-0 z-30 border-b border-white/[0.08] bg-[#111214]/70 backdrop-blur-xl">
    <div class="flex items-center justify-between px-5 py-4 sm:px-8">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="rounded-lg border border-white/10 p-2 text-gray-400 hover:bg-white/10 lg:hidden" aria-label="Open navigation">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            @if ($title !== '')
                <div>
                    <p class="text-xs text-[#68685f]">{{ $subtitle }}</p>
                    <h1 class="font-display text-xl font-semibold text-[#f4f4f3] tracking-tight">{{ $title }}</h1>
                </div>
            @endif
            {{ $slot }}
        </div>

        <div class="flex items-center gap-2">
            @isset($actions)
                {{ $actions }}
            @endisset

            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="relative rounded-xl border border-white/10 p-2.5 text-gray-400 transition hover:bg-white/[0.06] hover:text-white" aria-label="Notifications">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
                    @if ($bellSubscriptions->isNotEmpty())
                        <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-orange-500 ring-2 ring-[#111214]"></span>
                    @endif
                </button>

                <div x-show="open" x-cloak @click.outside="open = false" x-transition
                     class="absolute right-0 top-full z-40 mt-2 w-80 overflow-hidden rounded-xl border border-white/[0.08] bg-[#111214] shadow-2xl">
                    <p class="border-b border-white/[0.08] px-4 py-3 text-xs font-bold uppercase tracking-widest text-[#68685f]">Notifications</p>
                    <div class="max-h-72 overflow-y-auto">
                        @forelse ($bellSubscriptions as $renewal)
                            <div class="flex items-start gap-3 border-b border-white/[0.05] px-4 py-3">
                                <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-orange-500/15 text-orange-300">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm text-[#f4f4f3]">{{ $renewal->name }} renews soon</p>
                                    <p class="text-xs text-[#68685f]">{{ $renewal->billing_date->format('M j, Y') }} · ₱{{ number_format((float) $renewal->price, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="px-4 py-6 text-center text-sm text-[#68685f]">You're all caught up. 🎉</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="flex items-center rounded-full transition hover:ring-2 hover:ring-orange-500/40" aria-label="Account menu">
                    @if ($user->profile_photo_path !== null)
                        <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->name }}" class="h-9 w-9 rounded-full object-cover"/>
                    @else
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-amber-600 text-sm font-bold text-[#1a0d02]">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    @endif
                </button>

                <div x-show="open" x-cloak @click.outside="open = false" x-transition
                     class="absolute right-0 top-full z-40 mt-2 w-56 overflow-hidden rounded-xl border border-white/[0.08] bg-[#111214] shadow-2xl">
                    <div class="border-b border-white/[0.08] px-4 py-3">
                        <p class="truncate text-sm font-semibold text-[#f4f4f3]">{{ $user->name }}</p>
                        <p class="truncate text-xs text-[#68685f]">{{ $user->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 transition hover:bg-white/[0.06] hover:text-white">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"/></svg>
                        Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-gray-300 transition hover:bg-red-400/10 hover:text-red-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="border-t border-red-400/20 bg-red-400/10 px-5 py-2.5 text-sm text-red-300 sm:px-8">
            {{ session('error') }}
        </div>
    @endif
</header>
