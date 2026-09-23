<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SubLeak') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-display{ font-family:'Space Grotesk', ui-sans-serif, sans-serif; }
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
    @include('components.navigation')

    <div class="lg:pl-72">
        <x-topbar subtitle="Account" title="Settings"/>

        <!-- Page Content -->
        <main class="px-5 py-8 sm:px-8">
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
