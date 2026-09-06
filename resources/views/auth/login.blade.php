<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SubLeak') }} - Login</title>

    <!-- This ensures Tailwind CSS is compiled -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-[#4a6c6a] to-[#2d4240] min-h-screen text-white font-sans antialiased flex flex-col">

<!-- Top Navigation Header -->
<header class="flex justify-between items-center p-8 w-full">
    <a href="/" class="text-sm text-gray-300 hover:text-white flex items-center gap-2 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to store
    </a>
    <div class="text-sm text-gray-300 flex items-center gap-4">
        Not a member?
        <a href="{{ route('register') }}" class="border border-gray-400 px-5 py-2 rounded-md hover:bg-white hover:text-[#2d4240] transition">
            Sign Up
        </a>
    </div>
</header>

<!-- Main Login Area -->
<main class="flex-1 flex flex-col justify-center items-center px-4 pb-32">

    <!-- Center Logo (Placeholder shape matching your image) -->
    <div class="mb-12">
        <svg class="w-14 h-14 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
        </svg>
    </div>

    <!-- Validation Errors & Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm">
        @csrf

        <!-- Email Address -->
        <div class="mb-8">
            <label for="email" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">
                Email Address
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="w-full bg-transparent border-0 border-b border-gray-500 text-white focus:border-white focus:ring-0 px-0 py-2 transition placeholder-gray-400/50"
                   placeholder="demo@modnotebooks.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <!-- Password -->
        <div class="mb-10 relative">
            <label for="password" class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">
                Password
            </label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="w-full bg-transparent border-0 border-b border-gray-500 text-white focus:border-white focus:ring-0 px-0 py-2 transition placeholder-gray-400/50"
                   placeholder="••••••••">

            <!-- Forgot Password / "?" Button -->
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="absolute right-0 bottom-2 bg-gray-500/30 hover:bg-gray-400/50 text-gray-300 w-6 h-6 flex items-center justify-center rounded text-xs transition"
                   title="Forgot Password?">
                    ?
                </a>
            @endif
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
        </div>

        <!-- Remember Me (Hidden to maintain a clean UI, but defaults to true for convenience) -->
        <input type="hidden" name="remember" value="on">

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-[#cbd5e1] hover:bg-white text-gray-800 font-semibold py-3.5 rounded transition">
            Login
        </button>
    </form>
</main>
</body>
</html>
