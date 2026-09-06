<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SubLeak') }} - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-[#2d4240] to-[#1a2625] min-h-screen text-white font-sans antialiased flex flex-col">

<!-- Top Navigation Header -->
<header class="flex justify-between items-center px-8 py-6 border-b border-gray-700/50 backdrop-blur-md bg-black/10">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-teal-500/20 flex items-center justify-center border border-teal-500/30 text-teal-300 font-bold">
            S
        </div>
        <span class="text-lg font-semibold tracking-wide">SubLeak</span>
    </div>
    <div class="flex items-center gap-6">
        <span class="text-sm text-gray-300 hidden sm:inline">{{ Auth::user()->name ?? 'Account' }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-400 hover:text-white transition">
                Logout
            </button>
        </form>
    </div>
</header>

<!-- Main Content Container -->
<main class="flex-1 max-w-6xl w-full mx-auto px-4 py-10">

    <!-- Header Section & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Active Subscriptions</h1>
            <p class="text-sm text-gray-400 mt-1">Manage and track your recurring expenses in one place.</p>
        </div>
        <a href="{{ route('subscriptions.create') }}" class="bg-teal-600 hover:bg-teal-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-teal-900/20 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Subscription
        </a>
    </div>

    <!-- Success Message Notification -->
    @if (session('success'))
        <div class="mb-6 bg-teal-500/10 border border-teal-500/30 text-teal-300 px-4 py-3 rounded-lg text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Table Card Wrapper -->
    <div class="bg-black/20 backdrop-blur-md border border-gray-700/50 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b border-gray-700/50 text-[11px] uppercase tracking-widest text-gray-400 bg-black/10">
                    <th class="py-4 px-6">Service Name</th>
                    <th class="py-4 px-6">Category</th>
                    <th class="py-4 px-6">Price</th>
                    <th class="py-4 px-6">Billing Cycle</th>
                    <th class="py-4 px-6">Next Billing</th>
                    <th class="py-4 px-6 text-right">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                @forelse ($subscriptions as $sub)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="py-4 px-6 font-medium text-white flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-700/40 flex items-center justify-center text-xs text-gray-300 font-bold border border-gray-600/40">
                                {{ strtoupper(substr($sub->name, 0, 1)) }}
                            </div>
                            {{ $sub->name }}
                        </td>
                        <td class="py-4 px-6 text-gray-300">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-medium bg-gray-800/80 text-gray-300 border border-gray-700">
                                        {{ $sub->category ?? 'General' }}
                                    </span>
                        </td>
                        <td class="py-4 px-6 font-semibold text-teal-300">₱{{ number_format($sub->price, 2) }}</td>
                        <td class="py-4 px-6 text-gray-300 capitalize">{{ $sub->billing_cycle }}</td>
                        <td class="py-4 px-6 text-gray-300">{{ \Carbon\Carbon::parse($sub->next_billing_date)->format('M d, Y') }}</td>
                        <td class="py-4 px-6 text-right space-x-3">
                            <a href="{{ route('subscriptions.edit', $sub->id) }}" class="text-gray-400 hover:text-white transition text-xs">Edit</a>
                            <form action="{{ route('subscriptions.destroy', $sub->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this subscription?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 transition text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">
                            <p class="text-sm">No subscriptions added yet.</p>
                            <p class="text-xs text-gray-500 mt-1">Start tracking your digital recurring assets today.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
