
<aside
    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-white/[0.08] bg-[#0c0d0e]/95 px-5 py-6 shadow-2xl backdrop-blur-xl transition-transform duration-300 lg:translate-x-0"
>
    <div class="flex items-center gap-3 px-2">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-orange-500/30 bg-orange-500/10 text-lg font-bold text-orange-400 shadow-lg shadow-orange-950/30">S</div>
        <div>
            <p class="text-lg font-bold tracking-wide text-white">SubLeak</p>
            <p class="text-[10px] uppercase tracking-[0.24em] text-orange-500/70">Expense clarity</p>
        </div>
    </div>

    <div class="mt-10 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.04] p-3">
        @if (Auth::user()->profile_photo_path !== null)
            <img
                src="{{ Auth::user()->profilePhotoUrl() }}"
                alt="{{ Auth::user()->name }}"
                class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 ring-orange-500/40"
            />
        @else
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-amber-600 font-bold text-[#1a0d02]">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        @endif
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
            <p class="truncate text-xs text-gray-400">{{ Auth::user()->email }}</p>
        </div>
    </div>

    <nav class="mt-8 flex-1 space-y-1" aria-label="Main navigation">
        @php
            $navItems = [
                ['route' => 'dashboard', 'match' => 'dashboard', 'icon' => '⌂', 'label' => 'Overview'],
                ['route' => 'workspaces.index', 'match' => 'workspaces.*', 'icon' => '⊞', 'label' => 'Workspaces'],
                ['route' => 'billing-history.index', 'match' => 'billing-history.*', 'icon' => '▤', 'label' => 'Billing History'],
                ['route' => 'profile.edit', 'match' => 'profile.*', 'icon' => '⚙', 'label' => 'Settings'],
            ];
        @endphp
        <p class="px-3 pb-3 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500">Workspace</p>

        <button type="button" @click="$dispatch('open-subscription-modal', { workspaceId: null, workspaceName: null })" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm text-gray-300 transition hover:bg-white/[0.06] hover:text-white">
            <span class="text-base">＋</span> Add Subscription
        </button>

        @foreach ($navItems as $item)
            @php
                $isActive = request()->routeIs($item['match']);
                $activeClasses = 'bg-orange-500/10 px-3 py-3 text-sm font-semibold text-orange-400 ring-1 ring-inset ring-orange-500/20';
                $inactiveClasses = 'px-3 py-3 text-sm text-gray-300 transition hover:bg-white/[0.06] hover:text-white';
            @endphp
            <a href="{{ route($item['route']) }}" @if ($isActive) aria-current="page" @endif
               class="flex items-center gap-3 rounded-xl {{ $isActive ? $activeClasses : $inactiveClasses }}">
                <span class="text-base">{{ $item['icon'] }}</span> {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="border-t border-white/10 pt-4">
        @csrf
        <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm text-gray-400 transition hover:bg-red-400/10 hover:text-red-300">
            <span class="text-base">↪</span> Logout
        </button>
    </form>
</aside>
