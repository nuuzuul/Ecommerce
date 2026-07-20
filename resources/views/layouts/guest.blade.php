<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Kanrejawataa') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/branding/favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-100 text-stone-800 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <a href="{{ route('home') }}" class="mb-6"><x-application-logo /></a>
        <div class="w-full max-w-md overflow-hidden rounded-3xl border border-amber-100 bg-white p-7 shadow-xl shadow-amber-900/5 sm:p-8">
            {{ $slot }}
        </div>
        <a href="{{ route('home') }}" class="mt-6 text-sm font-bold text-amber-700">← Kembali ke toko</a>
    </div>
</body>
</html>
