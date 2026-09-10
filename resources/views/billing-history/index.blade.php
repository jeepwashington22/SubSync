<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SubLeak') }} - Billing History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-display { font-family: 'Space Grotesk', ui-sans-serif, sans-serif; }
        .figure { font-family: 'Space Grotesk', ui-sans-serif, sans-serif; font-variant-numeric: tabular-nums; }
    </style>
</head>
<body x-data="{ sidebarOpen: false }" class="min-h-screen bg-[#08090a] text-[#f4f4f3] antialiased font-sans" style="background-image:radial-gradient(80rem 40rem at 15% -10%, rgba(249,115,22,.07), transparent 60%);">
<div class="min-h-screen">
    <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/60 lg:hidden" aria-hidden="true"></div>
    <x-navigation/>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-white/[0.08] bg-[#111214]/70 backdrop-blur-xl">
            <div class="flex items-center justify-between px-5 py-4 sm:px-8">
                <button @click="sidebarOpen = true" class="rounded-lg border border-white/10 p-2 text-gray-400 hover:bg-white/10 lg:hidden" aria-label="Open navigation">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="hidden lg:block">
                    <p class="text-xs text-[#68685f]">Workspace</p>
                    <h1 class="font-display text-xl font-semibold tracking-tight text-[#f4f4f3]">Billing History</h1>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-5 py-8 sm:px-8">
            <div class="mb-8 lg:hidden">
                <p class="text-xs text-[#68685f]">Workspace</p>
                <h1 class="font-display text-2xl font-semibold text-[#f4f4f3]">Billing History</h1>
            </div>

            <section class="rounded-2xl border border-white/[0.08] bg-white/[0.02] backdrop-blur-xl">
                <div class="border-b border-white/[0.07] px-5 py-5 sm:px-6">
                    <h2 class="font-display font-semibold text-[#f4f4f3]">Recorded charges</h2>
                    <p class="mt-1 text-xs text-[#68685f]">Every billing event captured for your tracked subscriptions</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left">
                        <thead class="border-b border-white/[0.07] text-[11px] uppercase tracking-widest text-[#68685f]">
                        <tr>
                            <th class="px-5 py-3 font-semibold sm:px-6">Subscription</th>
                            <th class="px-5 py-3 font-semibold">Billed on</th>
                            <th class="px-5 py-3 font-semibold">Source</th>
                            <th class="px-5 py-3 text-right font-semibold sm:px-6">Amount</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.05]">
                        @forelse ($billingHistories as $history)
                            <tr class="transition hover:bg-white/[0.025]">
                                <td class="px-5 py-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[10px] bg-teal-400/10 font-display font-semibold text-teal-300">{{ strtoupper(substr($history->subcription->name, 0, 1)) }}</span>
                                        <span class="text-sm font-semibold text-[#f4f4f3]">{{ $history->subcription->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-[#c9c9c6]">{{ $history->billed_at->format('M j, Y') }}</td>
                                <td class="px-5 py-4"><span class="rounded-lg bg-white/[0.06] px-2.5 py-1 text-xs font-medium capitalize text-[#9a9a97]">{{ $history->source }}</span></td>
                                <td class="figure px-5 py-4 text-right text-sm font-semibold text-orange-300 sm:px-6">{{ $history->currency }} {{ number_format((float) $history->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <p class="text-sm text-[#c9c9c6]">No billing history yet.</p>
                                    <p class="mt-1 text-xs text-[#68685f]">Recorded charges will appear here as subscriptions are billed.</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($billingHistories->hasPages())
                    <div class="border-t border-white/[0.07] px-5 py-4 sm:px-6">
                        {{ $billingHistories->links() }}
                    </div>
                @endif
            </section>
        </main>
    </div>
</div>
<x-subscription-modal />
</body>
</html>