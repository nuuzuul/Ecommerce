<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Kanrejawataa')
    </title>

    <link
        rel="icon"
        type="image/svg+xml"
        href="{{ asset('images/branding/favicon.svg') }}"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])
</head>

<body class="min-h-screen bg-amber-50/40 text-stone-800 antialiased">

    @include('partials.store-navbar')

    <main>
        <div class="mx-auto w-full max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <x-alert type="success">
                    {{ session('success') }}
                </x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error">
                    {{ session('error') }}
                </x-alert>
            @endif

            @if (session('warning'))
                <x-alert type="warning">
                    {{ session('warning') }}
                </x-alert>
            @endif

            @if (session('info'))
                <x-alert type="info">
                    {{ session('info') }}
                </x-alert>
            @endif

        </div>

        @yield('content')
    </main>

    @include('partials.footer')

</body>
</html>