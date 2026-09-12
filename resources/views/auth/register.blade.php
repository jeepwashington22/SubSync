<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SubLeak') }} - Sign Up</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen bg-black text-white font-sans antialiased flex flex-col overflow-hidden">

<!-- Animated Starry Background (matching welcome page) -->
<div class="fixed inset-0 bg-[radial-gradient(ellipse_at_bottom,_#262626_0%,_#000_100%)]"></div>
<canvas id="stars-background" class="stars-canvas" aria-hidden="true"></canvas>
<div class="silk-overlay" aria-hidden="true"></div>

<!-- Top Navigation Header -->
<header class="relative z-10 flex justify-between items-center p-8 w-full">
    <a href="/" class="text-sm text-gray-300 hover:text-white flex items-center gap-2 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to store
    </a>
    <div class="text-sm text-gray-300 flex items-center gap-4">
        Already a member?
        <a href="{{ route('login') }}" class="border border-gray-400 px-5 py-2 rounded-md hover:bg-white hover:text-black transition">
            Log In
        </a>
    </div>
</header>

<!-- Main Register Area -->
<main class="relative z-10 flex-1 flex flex-col justify-center items-center px-4 pb-32">

    <!-- Center Logo -->
    <div class="mb-12">
        <svg class="w-14 h-14 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
        </svg>
    </div>

    <form method="POST" action="{{ route('register') }}" class="w-full max-w-sm">
        @csrf

        <!-- Name -->
        <div class="mb-8">
            <label for="name" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">
                Name
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="w-full bg-transparent border-0 border-b border-gray-500 text-white focus:border-white focus:ring-0 px-0 py-2 transition placeholder-gray-400/50"
                   placeholder="Your name">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400" />
        </div>

        <!-- Email Address -->
        <div class="mb-8">
            <label for="email" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">
                Email Address
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="w-full bg-transparent border-0 border-b border-gray-500 text-white focus:border-white focus:ring-0 px-0 py-2 transition placeholder-gray-400/50"
                   placeholder="you@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <!-- Password -->
        <div class="mb-8">
            <label for="password" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">
                Password
            </label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="w-full bg-transparent border-0 border-b border-gray-500 text-white focus:border-white focus:ring-0 px-0 py-2 transition placeholder-gray-400/50"
                   placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-10">
            <label for="password_confirmation" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">
                Confirm Password
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="w-full bg-transparent border-0 border-b border-gray-500 text-white focus:border-white focus:ring-0 px-0 py-2 transition placeholder-gray-400/50"
                   placeholder="••••••••">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400" />
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-400 text-black font-semibold py-3.5 rounded transition shadow-lg shadow-orange-950/40">
            Sign Up
        </button>
    </form>
</main>
</body>
</html>
