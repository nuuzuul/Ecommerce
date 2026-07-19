@extends('layouts.store')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-[250px_1fr]">
        @include('partials.account-sidebar')
        <div class="min-w-0">
            @yield('account-content')
        </div>
    </div>
</section>
@endsection
