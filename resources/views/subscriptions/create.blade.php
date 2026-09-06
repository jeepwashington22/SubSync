<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SubLeak') }} - Add Subscription</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-[#2d4240] to-[#1a2625] min-h-screen text-white font-sans antialiased flex flex-col justify-between">

<!-- Top Navigation Header -->
<header class="flex justify-between items-center px-8 py-6 w-full">
    <a href="{{ route('subscriptions.index') }}" class="text-sm text-gray-300 hover:text-white flex items-center gap-2 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Dashboard
    </a>
</header>

<!-- Main Content Form Area -->
<main class="flex-1 flex flex-col justify-center items-center px-4 py-8">
    <div class="w-full max-w-lg bg-black/20 backdrop-blur-md border border-gray-700/50 p-8 rounded-2xl shadow-2xl">

        <div class="mb-6">
            <h2 class="text-xl font-bold tracking-tight text-white">New Subscription</h2>
            <p class="text-xs text-gray-400 mt-1">Fill out the details below to log a recurring asset.</p>
        </div>

        <form method="POST" action="{{ route('subscriptions.store') }}" class="space-y-5">
            @csrf

            <!-- Name Field -->
            <div>
                <label for="name" class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Service Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full bg-black/30 border border-gray-600/50 rounded-lg text-white focus:border-teal-400 focus:ring-1 focus:ring-teal-400 px-4 py-2.5 transition placeholder-gray-500"
                       placeholder="e.g., Netflix, Spotify, AWS">
                @error('name')
                <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Price and Billing Cycle Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Price (₱)</label>
                    <input id="price" type="number" step="0.01" name="price" value="{{ old('price') }}" required
                           class="w-full bg-black/30 border border-gray-600/50 rounded-lg text-white focus:border-teal-400 focus:ring-1 focus:ring-teal-400 px-4 py-2.5 transition placeholder-gray-500"
                           placeholder="249.00">
                    @error('price')
                    <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="billing_cycle" class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Billing Cycle</label>
                    <select id="billing_cycle" name="billing_cycle" required
                            class="w-full bg-[#1a2625] border border-gray-600/50 rounded-lg text-white focus:border-teal-400 focus:ring-1 focus:ring-teal-400 px-4 py-2.5 transition">
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="weekly">Weekly</option>
                    </select>
                    @error('billing_cycle')
                    <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Next Billing Date & Category Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="next_billing_date" class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Next Billing Date</label>
                    <input id="next_billing_date" type="date" name="next_billing_date" value="{{ old('next_billing_date') }}" required
                           class="w-full bg-black/30 border border-gray-600/50 rounded-lg text-white focus:border-teal-400 focus:ring-1 focus:ring-teal-400 px-4 py-2.5 transition">
                    @error('next_billing_date')
                    <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Category (Optional)</label>
                    <input id="category" type="text" name="category" value="{{ old('category') }}"
                           class="w-full bg-black/30 border border-gray-600/50 rounded-lg text-white focus:border-teal-400 focus:ring-1 focus:ring-teal-400 px-4 py-2.5 transition placeholder-gray-500"
                           placeholder="Entertainment, Utility">
                    @error('category')
                    <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Action Button -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('subscriptions.index') }}" class="px-5 py-2.5 rounded-lg text-sm text-gray-400 hover:text-white transition">Cancel</a>
                <button type="submit" class="bg-teal-600 hover:bg-teal-500 text-white font-medium text-sm px-6 py-2.5 rounded-lg shadow-lg shadow-teal-900/20 transition">
                    Save Subscription
                </button>
            </div>
        </form>
    </div>
</main>

<footer class="py-6 text-center text-xs text-gray-500">
    SubLeak Asset Tracking Engine
</footer>
</body>
</html>
