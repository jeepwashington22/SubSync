<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SubLeak') }} - Bank Accounts</title>
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
                    <p class="text-xs text-[#68685f]">Automated sync</p>
                    <h1 class="font-display text-xl font-semibold text-[#f4f4f3] tracking-tight">Bank accounts</h1>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('bank-accounts.link') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-[#1a0d02] shadow-lg shadow-orange-950/40 transition hover:bg-orange-400"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>Link account</button>
                    </form>
                    <a href="{{ route('dashboard') }}" class="rounded-lg border border-white/10 p-2 text-gray-400 hover:bg-white/10 lg:hidden"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg></a>
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
            @if($accounts->isEmpty())
                <div class="rounded-2xl border border-white/[0.08] bg-white/[0.02] p-8 text-center">
                    <svg class="mx-auto mb-3 h-10 w-10 text-[#68685f]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect width="20" height="14" x="2" y="5"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    <p class="text-sm text-[#68685f]">No accounts linked yet.</p>
                    <p class="mt-1 text-xs text-[#68685f]">Connect a bank account or e-wallet (Plaid, Maya, GCash) so we can scan for recurring subscription charges automatically.</p>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($accounts as $account)
                        <div class="group rounded-2xl border border-white/[0.08] bg-white/[0.02] p-5 transition hover:border-orange-400/30">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-[#f4f4f3]">{{ $account->display_name }}</p>
                                    <p class="mt-0.5 text-xs text-[#68685f]">{{ $account->institution ?? strtoupper($account->provider) }} <span class="text-[#68685f]/60">·</span> {{ $account->provider_account_id }}</p>
                                </div>
                                <span class="rounded-lg px-2 py-0.5 text-[11px] font-medium {{ $account->is_active ? 'bg-green-400/10 text-green-300' : 'bg-[#68685f]/15 text-[#68685f]' }}">{{ $account->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <form action="{{ route('bank-accounts.unlink', $account) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="mt-4 w-full rounded-lg border border-white/[0.08] py-2 text-xs font-medium text-[#68685f] transition hover:border-red-400/30 hover:text-red-300">Unlink account</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</div>
</body>
</html>
