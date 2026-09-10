<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SubLeak') }} - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{
            --sl-border:rgba(255,255,255,.08);
            --sl-border-accent:rgba(249,115,22,.4);
            --sl-glass:rgba(255,255,255,.035);
        }
        .font-display{ font-family:'Space Grotesk', ui-sans-serif, sans-serif; }
        .figure{ font-family:'Space Grotesk', ui-sans-serif, sans-serif; font-variant-numeric:tabular-nums; }
        .card-hero{
            background:linear-gradient(155deg, rgba(249,115,22,.10), var(--sl-glass) 55%);
            border-color:var(--sl-border-accent) !important;
            position:relative;
        }
        .card-hero::before{
            content:'';position:absolute;inset:0 0 auto 0;height:2px;border-radius:1rem 1rem 0 0;
            background:linear-gradient(90deg, #f97316, transparent 70%);
        }
    </style>
</head>
<body
    x-data="{ sidebarOpen: false }"
    class="min-h-screen bg-[#08090a] text-[#f4f4f3] antialiased font-sans"
    style="background-image:radial-gradient(80rem 40rem at 15% -10%, rgba(249,115,22,.07), transparent 60%);"
>
<div class="min-h-screen">
    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/60 lg:hidden"
        aria-hidden="true"
    ></div>
    <x-navigation/>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-white/[0.08] bg-[#111214]/70 backdrop-blur-xl">
            <div class="flex items-center justify-between px-5 py-4 sm:px-8">
                <button @click="sidebarOpen = true" class="rounded-lg border border-white/10 p-2 text-gray-400 hover:bg-white/10 lg:hidden" aria-label="Open navigation">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="hidden lg:block">
                    <p class="text-xs text-[#68685f]">Workspace</p>
                    <h1 class="font-display text-xl font-semibold text-[#f4f4f3] tracking-tight">Good day, {{ Str::before(Auth::user()->name, ' ') }}</h1>
                    @if(! empty($priceHikes))
                        <x-price-hike-alert :hike="$priceHikes[0]" class="ml-3"/>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <x-connect-gmail-button :connected="$isGmailConnected"/>
                    <button type="button" @click="$dispatch('open-subscription-modal', { workspaceId: null, workspaceName: null })" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-semibold text-[#1a0d02] shadow-lg shadow-orange-950/40 transition hover:bg-orange-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                        <span class="hidden sm:inline">Add subscription</span>
                    </button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-5 py-8 sm:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-green-400/30 bg-green-400/10 px-4 py-3 text-sm font-medium text-green-300">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-xl border border-red-400/30 bg-red-400/10 px-4 py-3 text-sm font-medium text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-8 lg:hidden">
                <p class="text-xs text-[#68685f]">Workspace</p>
                <h1 class="font-display text-2xl font-semibold text-[#f4f4f3]">Good day, {{ Str::before(Auth::user()->name, ' ') }}</h1>
            </div>

            {{-- Metrics: the first card carries visual weight as the hero figure --}}
            <section class="grid gap-3.5 sm:grid-cols-2 xl:grid-cols-[1.4fr_1fr_1fr_1fr]" aria-label="Subscription metrics">

                <article class="card-hero rounded-2xl border border-white/[0.08] bg-white/[0.035] p-5 backdrop-blur-xl">
                    <div class="flex items-start justify-between">
                        <p class="text-[13px] text-[#9a9a97]">Monthly spending</p>
                        <span class="flex h-9 w-9 items-center justify-center rounded-[10px] bg-orange-500/15 text-orange-300">
                                <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18M17 7.5c0-1.9-2.2-3.5-5-3.5s-5 1.4-5 3.2c0 4 10 1.8 10 5.8 0 1.9-2.2 3.5-5 3.5s-5-1.6-5-3.5"/></svg>
                            </span>
                    </div>
                    <p class="figure mt-4 text-[34px] font-semibold tracking-tight text-[#f4f4f3]">₱{{ number_format((float) $totalMonthlySpending, 2) }}</p>
                    <p class="mt-1 text-xs text-[#68685f]">/ month</p>
                </article>

                <article class="rounded-2xl border border-white/[0.08] bg-white/[0.035] p-5 backdrop-blur-xl">
                    <div class="flex items-start justify-between">
                        <p class="text-[13px] text-[#9a9a97]">Yearly spending</p>
                        <span class="flex h-9 w-9 items-center justify-center rounded-[10px] bg-white/[0.06] text-[#9a9a97]">
                                <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17 17 7M17 7H9M17 7v8"/></svg>
                            </span>
                    </div>
                    <p class="figure mt-4 text-[26px] font-semibold tracking-tight text-[#f4f4f3]">₱{{ number_format((float) $totalYearlySpending, 2) }}</p>
                    <p class="mt-1 text-xs text-[#68685f]">/ year</p>
                </article>

                <article class="rounded-2xl border border-white/[0.08] bg-white/[0.035] p-5 backdrop-blur-xl">
                    <div class="flex items-start justify-between">
                        <p class="text-[13px] text-[#9a9a97]">Active subscriptions</p>
                        <span class="flex h-9 w-9 items-center justify-center rounded-[10px] bg-teal-400/15 text-teal-300">
                                <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M5 13l4 4L19 7"/></svg>
                            </span>
                    </div>
                    <p class="figure mt-4 text-[26px] font-semibold tracking-tight text-[#f4f4f3]">{{ number_format((int) $activeSubscriptionsCount) }}</p>
                    <p class="mt-1 text-xs text-[#68685f]">tracked services</p>
                </article>

                <article class="rounded-2xl border border-white/[0.08] bg-white/[0.035] p-5 backdrop-blur-xl">
                    <div class="flex items-start justify-between">
                        <p class="text-[13px] text-[#9a9a97]">Upcoming renewals</p>
                        <span class="flex h-9 w-9 items-center justify-center rounded-[10px] bg-white/[0.06] text-[#9a9a97]">
                                <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            </span>
                    </div>
                    <p class="figure mt-4 text-[26px] font-semibold tracking-tight text-[#f4f4f3]">{{ number_format($upcomingRenewals->count()) }}</p>
                    <p class="mt-1 text-xs text-[#68685f]">next 7–30 days</p>
                </article>
            </section>

            <div class="mt-4 grid gap-4 xl:grid-cols-[1.5fr_1fr]">
                <section class="rounded-2xl border border-white/[0.08] bg-white/[0.02] backdrop-blur-xl">
                    <div class="flex items-center justify-between border-b border-white/[0.07] px-5 py-5 sm:px-6">
                        <div>
                            <h2 class="font-display font-semibold text-[#f4f4f3]">Upcoming bills this week</h2>
                            <p class="mt-1 text-xs text-[#68685f]">Renewals scheduled between today and the next 7 days</p>
                        </div>
                    </div>
                    <div class="divide-y divide-white/[0.05]">
                        @forelse ($weeklyBills as $subscription)
                            <div class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-white/[0.025] sm:px-6">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded-[11px] bg-teal-400/10 font-display font-semibold text-teal-300">{{ strtoupper(substr($subscription->name, 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-[#f4f4f3]">{{ $subscription->name }}</p>
                                        <p class="mt-0.5 text-xs text-[#68685f]">{{ $subscription->billing_date->format('D, M j') }} · {{ ucfirst($subscription->billing_cycle) }}</p>
                                    </div>
                                </div>
                                <p class="figure shrink-0 text-sm font-semibold text-orange-300">₱{{ number_format((float) $subscription->price, 2) }}</p>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <p class="text-sm text-[#c9c9c6]">Your week is clear.</p>
                                <p class="mt-1 text-xs text-[#68685f]">No renewals are due in the next 7 days.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section id="reports" class="rounded-2xl border border-white/[0.08] bg-white/[0.02] p-5 backdrop-blur-xl sm:p-6">
                    <div>
                        <h2 class="font-display font-semibold text-[#f4f4f3]">Quick actions</h2>
                        <p class="mt-1 text-xs text-[#68685f]">Keep your ledger current</p>
                    </div>
                    <div class="mt-5 space-y-2.5">
                        <button type="button" @click="$dispatch('open-subscription-modal', { workspaceId: null, workspaceName: null })" class="flex w-full items-center justify-between rounded-xl border border-white/[0.08] bg-white/[0.02] p-4 text-left transition hover:border-orange-400/30 hover:bg-orange-400/[0.06]">
                                <span class="flex items-center gap-3 text-sm text-[#e4e4e2]">
                                    <svg class="h-[17px] w-[17px] text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                    Add a subscription
                                </span>
                            <svg class="h-[15px] w-[15px] text-[#68685f]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
                        </button>
                    </div>
                    <div class="mt-5 rounded-2xl border border-red-400/[0.22] bg-red-400/[0.08] p-4">
                        <p class="text-xs font-semibold text-red-300">Flagged subscriptions</p>
                        <p class="figure mt-2 text-2xl font-semibold text-[#f4f4f3]">{{ number_format((int) $cancelledSubscriptionsCount) }}</p>
                        <p class="mt-1 text-xs text-[#68685f]">Accidental or cancelled records excluded from active totals</p>
                    </div>
                </section>
            </div>

            <section class="mt-4 rounded-2xl border border-white/[0.08] bg-white/[0.02] p-5 backdrop-blur-xl sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-display font-semibold text-[#f4f4f3]">Renewals ahead</h2>
                        <p class="mt-1 text-xs text-[#68685f]">Your next 7–30 days at a glance</p>
                    </div>
                    <span class="text-xs text-[#68685f]">{{ $upcomingRenewals->count() }} scheduled</span>
                </div>
                <div class="mt-4 flex gap-3 overflow-x-auto pb-1">
                    @forelse ($upcomingRenewals as $subscription)
                        <div class="min-w-[170px] rounded-2xl border border-white/[0.08] bg-white/[0.03] p-4">
                            <p class="truncate text-sm font-semibold text-[#f4f4f3]">{{ $subscription->name }}</p>
                            <p class="mt-2 text-xs text-[#68685f]">{{ $subscription->billing_date->format('M j, Y') }}</p>
                            <p class="figure mt-3 text-[15px] font-semibold text-[#f4f4f3]">₱{{ number_format((float) $subscription->price, 2) }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-[#68685f]">No renewals scheduled in the next 30 days.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </div>
</div>
<x-subscription-modal />
</body>
</html>
