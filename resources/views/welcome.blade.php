<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SubSync') }} - Smart Subscription & Asset Tracker</title>

    <!-- Fonts & Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen bg-black text-white font-sans antialiased selection:bg-orange-500 selection:text-black">

<div class="relative z-10">

<!-- Sticky Navigation Bar -->
<header class="sticky top-0 z-50 border-b border-white/10 bg-black/95">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <!-- Logo -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-orange-500/15 flex items-center justify-center border border-orange-500/50 text-orange-400 font-bold text-lg shadow-lg shadow-orange-950/40">
                S
            </div>
            <span class="text-xl font-bold tracking-wide text-white">SubLeak</span>
        </div>

        <!-- Nav Links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-white/70">
            <a href="#home" class="hover:text-orange-400 transition">Home</a>
            <a href="#features" class="hover:text-orange-400 transition">Features</a>
            <a href="#about" class="hover:text-orange-400 transition">About Us</a>
            <a href="#contact" class="hover:text-orange-400 transition">Contact</a>
        </nav>

        <!-- Auth Actions -->
        <div class="flex items-center gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/subscriptions') }}" class="bg-orange-500 hover:bg-orange-400 text-black text-sm font-medium px-5 py-2 rounded-lg shadow-lg shadow-orange-950/40 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-white/70 hover:text-orange-400 transition px-3 py-2">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="border border-white/30 hover:border-orange-400 bg-white/5 hover:bg-orange-500/15 text-sm font-medium px-5 py-2 rounded-lg transition shadow-sm">
                            Get Started
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</header>

<!-- Hero Section -->
<section id="home" class="relative isolate overflow-hidden bg-[radial-gradient(ellipse_at_bottom,_#262626_0%,_#000_100%)]">
    <canvas id="stars-background" class="stars-canvas" aria-hidden="true"></canvas>

    <div class="relative z-10 max-w-6xl mx-auto px-6 py-24 md:py-32 flex flex-col items-center text-center">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-500/10 border border-orange-500/30 text-orange-300 text-xs font-semibold tracking-wide uppercase mb-6 shadow-inner animate-pulse">
        <span>⚡ Never Miss a Renewal Again</span>
    </div>

            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white mb-6 leading-tight max-w-4xl">
        Take Control of Your <span class="text-white">Subscriptions</span>
    </h1>

    <p class="text-gray-300 text-lg sm:text-xl max-w-2xl mb-12 leading-relaxed">
        Stop losing money to forgotten free trials, accidental renewals, and unmonitored digital overhead. SubLeak keeps your recurring finances pristine.
    </p>

    <div class="flex flex-col sm:flex-row items-center gap-4 w-full justify-center">
        @auth
            <a href="{{ url('/subscriptions') }}" class="w-full sm:w-auto bg-orange-500 hover:bg-orange-400 text-black font-semibold px-8 py-4 rounded-xl shadow-xl shadow-orange-950/40 transition text-center text-base">
                Go to My Dashboard
            </a>
        @else
            <a href="{{ route('register') }}" class="w-full sm:w-auto bg-orange-500 hover:bg-orange-400 text-black font-semibold px-8 py-4 rounded-xl shadow-xl shadow-orange-950/40 transition text-center text-base">
                Start Tracking Free
            </a>
            <a href="{{ route('login') }}" class="w-full sm:w-auto bg-black hover:bg-orange-500/10 border border-white/30 hover:border-orange-500/60 text-white font-medium px-8 py-4 rounded-xl transition text-center text-base">
                Log In to Account
            </a>
        @endauth
    </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="bg-black px-6 py-24 border-t border-gray-800/60">
    <div class="mx-auto max-w-5xl">
        <div class="mx-auto mb-12 max-w-3xl text-center">
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.28em] text-orange-400">Built for clarity</p>
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Engineered for Expense Clarity</h2>
            <p class="mt-4 text-gray-400">Everything you need to monitor software licenses, streaming services, and utility bills under one unified dashboard.</p>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <article class="group relative col-span-full overflow-hidden rounded-xl border border-white/10 bg-white/[0.03] p-6 transition hover:border-orange-500/50 lg:col-span-2">
                <div class="relative mx-auto flex h-28 max-w-xs items-center justify-center">
                    <svg class="absolute inset-0 h-full w-full text-orange-500/15" viewBox="0 0 260 110" fill="none" aria-hidden="true">
                        <path d="M10 80C50 12 94 102 132 42C168 -14 200 95 250 22" stroke="currentColor" stroke-width="18" stroke-linecap="round" />
                    </svg>
                    <span class="relative text-5xl font-semibold text-white">100%</span>
                </div>
                <h3 class="mt-5 text-center text-2xl font-semibold text-white">Customizable</h3>
                <p class="mt-2 text-center text-sm leading-relaxed text-gray-400">Shape your subscription view around the expenses that matter most.</p>
            </article>

            <article class="group col-span-full overflow-hidden rounded-xl border border-white/10 bg-white/[0.03] p-6 transition hover:border-orange-500/50 sm:col-span-1 lg:col-span-2">
                <div class="mx-auto flex size-32 items-center justify-center rounded-full border border-white/10 before:absolute before:size-36 before:rounded-full before:border before:border-orange-500/15">
                    <svg class="size-20 text-orange-400" viewBox="0 0 100 100" fill="none" aria-hidden="true">
                        <circle cx="50" cy="50" r="34" stroke="currentColor" stroke-width="5" stroke-dasharray="7 7" />
                        <path d="M50 30V52L64 62" stroke="currentColor" stroke-width="5" stroke-linecap="round" />
                    </svg>
                </div>
                <div class="mt-6 space-y-2 text-center">
                    <h3 class="text-lg font-medium text-white">Never miss a renewal</h3>
                    <p class="text-sm leading-relaxed text-gray-400">Upcoming charges stay visible before they become surprises.</p>
                </div>
            </article>

            <article class="group col-span-full overflow-hidden rounded-xl border border-white/10 bg-white/[0.03] p-6 transition hover:border-orange-500/50 sm:col-span-1 lg:col-span-2">
                <div class="relative pt-3">
                    <div class="flex items-center justify-between rounded-lg border border-white/10 bg-black px-3 py-3 text-xs text-gray-300"><span>Streaming</span><span class="text-orange-400">$14.99</span></div>
                    <div class="mt-2 flex items-center justify-between rounded-lg border border-white/10 bg-black px-3 py-3 text-xs text-gray-300"><span>Cloud tools</span><span class="text-orange-400">$29.00</span></div>
                    <div class="mt-2 flex items-center justify-between rounded-lg border border-white/10 bg-black px-3 py-3 text-xs text-gray-300"><span>Utilities</span><span class="text-orange-400">$62.40</span></div>
                </div>
                <div class="mt-6 space-y-2 text-center">
                    <h3 class="text-lg font-medium text-white">Faster than spreadsheets</h3>
                    <p class="text-sm leading-relaxed text-gray-400">One clean dashboard for every recurring payment.</p>
                </div>
            </article>

            <article class="group col-span-full overflow-hidden rounded-xl border border-white/10 bg-white/[0.03] p-6 transition hover:border-orange-500/50 lg:col-span-3">
                <div class="grid gap-8 sm:grid-cols-2 sm:items-end">
                    <div class="flex flex-col justify-between gap-10">
                        <span class="flex size-12 items-center justify-center rounded-full border border-orange-500/30 text-orange-400">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3 4.5 6v5.5c0 4.7 3.1 7.9 7.5 9.5 4.4-1.6 7.5-4.8 7.5-9.5V6L12 3Z"/><path d="m8.5 12 2.2 2.2 4.8-5"/></svg>
                        </span>
                        <div><h3 class="text-lg font-medium text-white">Secure by default</h3><p class="mt-2 text-sm leading-relaxed text-gray-400">Private account data, isolated workspaces, and secure authentication.</p></div>
                    </div>
                    <div class="rounded-tl-xl border-l border-t border-white/10 p-5">
                        <div class="mb-5 flex gap-1"><span class="size-2 rounded-full bg-orange-500/70"></span><span class="size-2 rounded-full bg-white/15"></span><span class="size-2 rounded-full bg-white/15"></span></div>
                        <div class="h-24 border-b border-orange-500/40 bg-[linear-gradient(165deg,transparent_48%,rgba(249,115,22,.35)_49%,transparent_51%)]"></div>
                    </div>
                </div>
            </article>

            <article class="group col-span-full overflow-hidden rounded-xl border border-white/10 bg-white/[0.03] p-6 transition hover:border-orange-500/50 lg:col-span-3">
                <div class="grid gap-8 sm:grid-cols-2 sm:items-end">
                    <div class="flex flex-col justify-between gap-10">
                        <span class="flex size-12 items-center justify-center rounded-full border border-orange-500/30 text-orange-400">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </span>
                        <div><h3 class="text-lg font-medium text-white">Shared visibility</h3><p class="mt-2 text-sm leading-relaxed text-gray-400">Keep household and team subscriptions clear for everyone who needs access.</p></div>
                    </div>
                    <div class="relative flex flex-col justify-center gap-4 border-l border-orange-500/20 pl-6">
                        <span class="w-fit rounded border border-white/10 bg-black px-2 py-1 text-xs text-gray-300">Workspace</span>
                        <span class="ml-8 w-fit rounded border border-orange-500/30 bg-orange-500/10 px-2 py-1 text-xs text-orange-300">Shared ledger</span>
                        <span class="w-fit rounded border border-white/10 bg-black px-2 py-1 text-xs text-gray-300">Members</span>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about" class="max-w-6xl mx-auto px-6 py-24 border-t border-gray-800/60">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="text-orange-400 text-xs font-bold tracking-widest uppercase mb-2 block">About SubLeak</span>
            <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-6">Built to eliminate digital subscription fatigue.</h2>
            <p class="text-gray-300 text-base leading-relaxed mb-6">
                In today's ecosystem, modern consumers and professionals juggle dozens of micro-transactions—from cloud compute servers to entertainment streaming. SubLeak was engineered to provide absolute transparency over recurring expenses without bloated interfaces.
            </p>
            <div class="flex items-center gap-6 text-sm text-gray-300 font-medium">
                <div class="flex items-center gap-2">
                    <span class="text-orange-400 font-bold">✓</span> Industry Grade UI
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-orange-400 font-bold">✓</span> Lightning Fast CRUD
                </div>
            </div>
        </div>
        <div class="bg-white/[0.03] border border-white/10 p-8 rounded-3xl shadow-2xl">
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-sm font-medium">Active Asset Tracking</span>
                    <span class="text-orange-400 font-bold">100% Secure</span>
                </div>
                <div class="flex justify-between items-center p-4 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-sm font-medium">Tech Stack Stack</span>
                    <span class="text-gray-300 text-xs">Laravel + PostgreSQL + Tailwind</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" data-animated-footer class="relative isolate min-h-[28rem] max-w-full mx-auto overflow-hidden border-t border-gray-800/60 px-6 py-24 text-center">
    <div class="pointer-events-none absolute inset-0 flex items-end justify-between opacity-90">
        <canvas data-footer-canvas ></canvas>
        <canvas data-footer-canvas ></canvas>
    </div>

    <div class="relative z-10 flex min-h-[20rem] flex-col items-center justify-center">
        <h2 class="mb-4 text-3xl font-bold tracking-tight text-white sm:text-4xl">Have Questions or Feedback?</h2>
        <p class="mb-8 max-w-xl text-base text-gray-400">We'd love to hear how SubLeak is helping you manage your digital overhead. Reach out to our engineering support team.</p>
        <a href="mailto:support@subleak.test" class="inline-block rounded-xl bg-orange-500 px-8 py-3.5 font-semibold text-black shadow-lg shadow-orange-950/40 transition hover:bg-orange-400">
            Contact Support
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="w-full border-t border-white/10 bg-black py-8">
    <div class="flex flex-col gap-5 px-6 text-sm text-white/60 sm:flex-row sm:items-center sm:justify-between sm:px-10">
        <div class="flex flex-col gap-5 text-sm text-white/60 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-lg font-semibold tracking-wide text-white">SubLeak</p>
                <p class="mt-1 max-w-xs text-xs leading-relaxed text-white/45">Clarity for every recurring charge.</p>
            </div>
            <div class="flex gap-6">
                <a href="#home" class="transition hover:text-orange-400">Privacy Policy</a>
                <a href="#home" class="transition hover:text-orange-400">Terms of Service</a>
            </div>
        </div>

        <div class="flex flex-col gap-3 text-xs text-white/45 sm:items-end">
            <p>&copy; {{ date('Y') }} SubLeak Engine. All rights reserved.</p>
            <a href="mailto:support@subleak.test" class="transition hover:text-orange-400">support@subleak.test</a>
        </div>
    </div>
</footer>
</div>
</body>
</html>
