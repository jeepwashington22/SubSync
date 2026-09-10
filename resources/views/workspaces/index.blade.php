<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SubLeak') }} - Workspaces</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--sl-border:rgba(255,255,255,.08);--sl-border-accent:rgba(249,115,22,.4);--sl-glass:rgba(255,255,255,.035);}
        .font-display{font-family:'Space Grotesk',ui-sans-serif,sans-serif;}
        .figure{font-family:'Space Grotesk',ui-sans-serif,sans-serif;font-variant-numeric:tabular-nums;}
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="min-h-screen bg-[#08090a] text-[#f4f4f3] antialiased font-sans" style="background-image:radial-gradient(80rem 40rem at 15% -10%, rgba(249,115,22,.07), transparent 60%);">
<div class="min-h-screen">
    <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/60 lg:hidden" aria-hidden="true"></div>
    <x-navigation/>
    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-white/[0.08] bg-[#111214]/70 backdrop-blur-xl">
            <div class="flex items-center justify-between px-5 py-4 sm:px-8">
                <button @click="sidebarOpen = true" class="rounded-lg border border-white/10 p-2 text-gray-400 hover:bg-white/10 lg:hidden" aria-label="Open navigation"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <div class="hidden lg:block">
                    <p class="text-xs text-[#68685f]">Collaboration</p>
                    <h1 class="font-display text-xl font-semibold text-[#f4f4f3] tracking-tight">Workspaces</h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('workspaces.store') }}" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-[#1a0d02] shadow-lg shadow-orange-950/40 transition hover:bg-orange-400"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>New workspace</a>
                </div>
            </div>
        </header>
        <main class="mx-auto max-w-7xl px-5 py-8 sm:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-green-400/30 bg-green-400/10 px-4 py-3 text-sm font-medium text-green-300">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-6 rounded-xl border border-red-400/30 bg-red-400/10 px-4 py-3 text-sm font-medium text-red-300">{{ session('error') }}</div>
            @endif
            <div class="grid gap-5 lg:grid-cols-[1fr_380px]">
                <section>
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-[#68685f]">Active memberships</h2>
                    @forelse($memberships as $membership)
                        <div class="group rounded-2xl border border-white/[0.08] bg-white/[0.02] p-5 transition hover:border-orange-400/30 hover:bg-orange-400/[0.04]">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="truncate text-base font-semibold text-[#f4f4f3]">{{ $membership->workspace->name }}</p>
                                    <p class="mt-1 flex items-center gap-2 text-xs text-[#68685f]"><span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-orange-500/15 text-orange-300 text-[10px] font-bold">{{ strtoupper(substr($membership->workspace->name,0,1)) }}</span>Owner: {{ $membership->workspace->owner?->name ?? '�' }} <span class="text-[#68685f]/60">�</span> <span class="capitalize">{{ $membership->role }}</span></p>
                                </div>
                                <div class="flex gap-2 opacity-0 transition group-hover:opacity-100">
                                    <button type="button" @click="$dispatch('open-subscription-modal', { workspaceId: {{ $membership->workspace->id }}, workspaceName: @js($membership->workspace->name) })" class="rounded-lg border border-white/[0.08] px-3 py-1.5 text-xs font-medium text-[#e4e4e2] transition hover:border-orange-400/40 hover:bg-white/[0.04]">Add subscription</button>
                                    @if($membership->role === 'owner')
                                        <form action="{{ route('workspaces.members.remove', [$membership->workspace, $membership->workspace->owner]) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="rounded-lg border border-red-400/25 px-3 py-1.5 text-xs font-medium text-red-300 transition hover:bg-red-400/10">Leave</button></form>
                                    @endif
                                </div>
                            </div>
                            @php $splits = \App\Models\SubscriptionSplit::whereIn('subcription_id', $membership->workspace->subcriptions()->pluck('id'))->where('user_id', \Illuminate\Support\Facades\Auth::id())->get(); @endphp
                            @if($splits->isNotEmpty())<div class="mt-3 flex flex-wrap gap-1.5">@foreach($splits as $split)<span class="rounded-lg bg-teal-400/10 px-2 py-0.5 text-[11px] font-medium text-teal-300">{{ $split->percent_share }}% � {{ ucfirst($split->subcription->category ?? 'other') }}</span>@endforeach</div>@endif
                        </div>
                    @empty
                        <div class="rounded-2xl border border-white/[0.08] bg-white/[0.02] p-6 text-center"><p class="text-sm text-[#68685f]">You're not part of any workspace yet.</p><p class="mt-1 text-xs text-[#68685f]">Create one to share subscriptions with friends, family, or teammates.</p></div>
                    @endforelse
                </section>
                <aside>
                    @if($pendingInvitations->isNotEmpty())
                        <div class="mb-5 rounded-2xl border border-amber-400/30 bg-amber-400/[0.07] p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-200">Pending invitations</p>
                            <ul class="mt-3 space-y-2">
                                @foreach($pendingInvitations as $invitation)
                                    <li class="flex items-center justify-between gap-2 rounded-lg bg-white/[0.04] p-2.5"><div><p class="truncate text-sm font-medium text-[#f4f4f3]">{{ $invitation->workspace->name }}</p><p class="text-xs text-[#68685f]">Expires {{ $invitation->expires_at->format('M j, g:i A') }}</p></div>
                                        <a href="{{ route('workspaces.accept', ['token' => $invitation->token]) }}" class="rounded-lg bg-orange-500 px-3 py-1.5 text-xs font-semibold text-[#1a0d02] shadow transition hover:bg-orange-400">Accept</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="rounded-2xl border border-white/[0.08] bg-white/[0.02] p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#68685f]">Quick actions</p>
                        <ul class="mt-4 space-y-2">
                            <li><a href="{{ route('bank-accounts.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-[#e4e4e2] transition hover:border-orange-400/30 hover:bg-orange-400/[0.06]"><svg class="h-[16px] w-[16px] text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5"/><line x1="2" x2="22" y1="10" y2="10"/></svg>Bank sync</a></li>
                            <li><a href="{{ route('gmail.redirect') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-[#e4e4e2] transition hover:border-orange-400/30 hover:bg-orange-400/[0.06]"><svg class="h-[16px] w-[16px] text-orange-400" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>Gmail scan</a></li>
                            <li><a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-[#e4e4e2] transition hover:border-orange-400/30 hover:bg-orange-400/[0.06]"><svg class="h-[16px] w-[16px] text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9l-5 4-4-4-3 5"/></svg>Dashboard</a></li>
                        </ul>
                    </div>
                </aside>
            </div>
        </main>
    </div>
</div>
<x-subscription-modal />
</body>
</html>
