<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Kanrejawataa')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-800 antialiased">
<div x-data="{ sidebar: false }" class="min-h-screen lg:flex">
    @include('partials.admin-sidebar')
    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-20 border-b border-stone-200 bg-white/95 backdrop-blur">
            <div class="flex h-16 items-center justify-between px-4 sm:px-6">
                <button @click="sidebar = true" class="rounded-lg border border-stone-200 p-2 lg:hidden" aria-label="Buka menu">☰</button>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[.2em] text-amber-700">Panel Admin</p>
                    <h1 class="font-bold text-stone-900">@yield('page-title', 'Kanrejawataa')</h1>
                </div>
                <a href="{{ route('home') }}" class="text-sm font-semibold text-amber-700 hover:text-amber-900">Lihat toko →</a>
            </div>
        </header>
        <main class="p-4 sm:p-6 lg:p-8">
            @if (session('success')) <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert> @endif
            @if (session('error')) <x-alert type="error" class="mb-5">{{ session('error') }}</x-alert> @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
