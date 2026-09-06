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
<body class="bg-gradient-to-br from-[#1a2625] via-[#2d4240] to-[#121c1b] min-h-screen text-white font-sans antialiased selection:bg-teal-500 selection:text-white">

<!-- Sticky Navigation Bar -->
<header class="sticky top-0 z-50 backdrop-blur-md bg-[#1a2625]/80 border-b border-gray-700/40">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <!-- Logo -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-teal-500/20 flex items-center justify-center border border-teal-500/30 text-teal-300 font-bold text-lg shadow-lg">
                S
            </div>
            <span class="text-xl font-bold tracking-wide text-white">SubLeak</span>
        </div>

        <!-- Nav Links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-300">
            <a href="#home" class="hover:text-teal-400 transition">Home</a>
            <a href="#features" class="hover:text-teal-400 transition">Features</a>
            <a href="#about" class="hover:text-teal-400 transition">About Us</a>
            <a href="#contact" class="hover:text-teal-400 transition">Contact</a>
        </nav>

        <!-- Auth Actions -->
        <div class="flex items-center gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/subscriptions') }}" class="bg-teal-600 hover:bg-teal-500 text-white text-sm font-medium px-5 py-2 rounded-lg shadow-lg shadow-teal-900/20 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-300 hover:text-white transition px-3 py-2">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="border border-gray-500/50 hover:border-teal-400 bg-black/20 hover:bg-teal-600/10 text-sm font-medium px-5 py-2 rounded-lg transition shadow-sm">
                            Get Started
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</header>

<!-- Hero Section -->
<section id="home" class="max-w-6xl mx-auto px-6 py-24 md:py-32 flex flex-col items-center text-center">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-teal-500/10 border border-teal-500/20 text-teal-300 text-xs font-semibold tracking-wide uppercase mb-6 shadow-inner animate-pulse">
        <span>⚡ Never Miss a Renewal Again</span>
    </div>

    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white mb-6 leading-tight max-w-4xl">
        Take Control of Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-300 via-emerald-400 to-teal-500">Subscriptions</span>
    </h1>

    <p class="text-gray-300 text-lg sm:text-xl max-w-2xl mb-12 leading-relaxed">
        Stop losing money to forgotten free trials, accidental renewals, and unmonitored digital overhead. SubLeak keeps your recurring finances pristine.
    </p>

    <div class="flex flex-col sm:flex-row items-center gap-4 w-full justify-center">
        @auth
            <a href="{{ url('/subscriptions') }}" class="w-full sm:w-auto bg-teal-600 hover:bg-teal-500 text-white font-semibold px-8 py-4 rounded-xl shadow-xl shadow-teal-900/40 transition text-center text-base">
                Go to My Dashboard
            </a>
        @else
            <a href="{{ route('register') }}" class="w-full sm:w-auto bg-teal-600 hover:bg-teal-500 text-white font-semibold px-8 py-4 rounded-xl shadow-xl shadow-teal-900/40 transition text-center text-base">
                Start Tracking Free
            </a>
            <a href="{{ route('login') }}" class="w-full sm:w-auto bg-black/30 hover:bg-black/50 border border-gray-600/50 text-gray-200 font-medium px-8 py-4 rounded-xl transition text-center text-base">
                Log In to Account
            </a>
        @endauth
    </div>
</section>

<!-- Features Section -->
<section id="features" class="max-w-7xl mx-auto px-6 py-24 border-t border-gray-800/60">
    <div class="text-center max-w-3xl mx-auto mb-16">
        <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-4">Engineered for Expense Clarity</h2>
        <p class="text-gray-400 text-base">Everything you need to monitor software licenses, streaming services, and utility bills under one unified dashboard.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <div class="bg-black/20 backdrop-blur-md border border-gray-700/50 p-8 rounded-2xl shadow-xl hover:border-teal-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-teal-500/20 flex items-center justify-center text-teal-300 mb-6 font-bold text-xl">
                📊
            </div>
            <h3 class="text-xl font-semibold mb-3 text-white">Centralized Dashboard</h3>
            <p class="text-gray-400 text-sm leading-relaxed">
                View all your monthly and yearly financial footprints at a single glance with smart sorting and category tracking.
            </p>
        </div>

        <!-- Feature 2 -->
        <div class="bg-black/20 backdrop-blur-md border border-gray-700/50 p-8 rounded-2xl shadow-xl hover:border-teal-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-teal-500/20 flex items-center justify-center text-teal-300 mb-6 font-bold text-xl">
                🚨
            </div>
            <h3 class="text-xl font-semibold mb-3 text-white">Accidental Charge Guard</h3>
            <p class="text-gray-400 text-sm leading-relaxed">
                Tag and flag unwanted trial sign-ups instantly so you can cancel them before billing cycles trigger a charge.
            </p>
        </div>

        <!-- Feature 3 -->
        <div class="bg-black/20 backdrop-blur-md border border-gray-700/50 p-8 rounded-2xl shadow-xl hover:border-teal-500/40 transition">
            <div class="w-12 h-12 rounded-xl bg-teal-500/20 flex items-center justify-center text-teal-300 mb-6 font-bold text-xl">
                🔒
            </div>
            <h3 class="text-xl font-semibold mb-3 text-white">Secure Data Isolation</h3>
            <p class="text-gray-400 text-sm leading-relaxed">
                Powered by secure Laravel authentication and isolated relational PostgreSQL architectures ensuring your data remains private.
            </p>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about" class="max-w-6xl mx-auto px-6 py-24 border-t border-gray-800/60">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="text-teal-400 text-xs font-bold tracking-widest uppercase mb-2 block">About SubLeak</span>
            <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-6">Built to eliminate digital subscription fatigue.</h2>
            <p class="text-gray-300 text-base leading-relaxed mb-6">
                In today's ecosystem, modern consumers and professionals juggle dozens of micro-transactions—from cloud compute servers to entertainment streaming. SubLeak was engineered to provide absolute transparency over recurring expenses without bloated interfaces.
            </p>
            <div class="flex items-center gap-6 text-sm text-gray-300 font-medium">
                <div class="flex items-center gap-2">
                    <span class="text-teal-400 font-bold">✓</span> Industry Grade UI
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-teal-400 font-bold">✓</span> Lightning Fast CRUD
                </div>
            </div>
        </div>
        <div class="bg-black/30 border border-gray-700/50 p-8 rounded-3xl shadow-2xl backdrop-blur-md">
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 rounded-xl bg-white/5 border border-white/10">
                    <span class="text-sm font-medium">Active Asset Tracking</span>
                    <span class="text-teal-400 font-bold">100% Secure</span>
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
<section id="contact" class="max-w-4xl mx-auto px-6 py-24 border-t border-gray-800/60 text-center">
    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white mb-4">Have Questions or Feedback?</h2>
    <p class="text-gray-400 text-base mb-8 max-w-xl mx-auto">We'd love to hear how SubLeak is helping you manage your digital overhead. Reach out to our engineering support team.</p>
    <a href="mailto:support@subleak.test" class="inline-block bg-teal-600 hover:bg-teal-500 text-white font-semibold px-8 py-3.5 rounded-xl transition shadow-lg shadow-teal-900/30">
        Contact Support
    </a>
</section>

<!-- Footer -->
<footer class="w-full border-t border-gray-800/80 bg-black/20 py-8">
    <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-400">
        <p>&copy; {{ date('Y') }} SubLeak Engine. All rights reserved.</p>
        <div class="flex gap-6">
            <a href="#home" class="hover:text-white transition">Privacy Policy</a>
            <a href="#home" class="hover:text-white transition">Terms of Service</a>
        </div>
    </div>
</footer>
</body>
</html>
