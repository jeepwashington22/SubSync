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

    <!-- Google Sign-Up -->
    <div class="w-full max-w-sm mt-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="flex-1 h-px bg-gray-700"></div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">or</span>
            <div class="flex-1 h-px bg-gray-700"></div>
        </div>
        <a href="{{ route('auth.google.redirect') }}"
           class="w-full flex items-center justify-center gap-3 bg-white hover:bg-gray-100 text-gray-900 font-semibold py-3.5 rounded transition">
            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47a5.57 5.57 0 0 1-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96H1.29v3.09A11.99 11.99 0 0 0 12 24z"/>
                <path fill="#FBBC05" d="M5.27 14.29A7.2 7.2 0 0 1 4.89 12c0-.8.14-1.57.38-2.29V6.62H1.29a11.99 11.99 0 0 0 0 10.76l3.98-3.09z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.69 1.29 6.62l3.98 3.09C6.22 6.86 8.87 4.75 12 4.75z"/>
            </svg>
            Sign up with Google
        </a>
    </div>
</main>
</body>
</html>
