@extends('layouts.store')
@section('title','403 — Akses Ditolak')
@section('content')
<section class="mx-auto max-w-3xl px-4 py-24 text-center"><p class="text-8xl font-black text-red-500">403</p><h1 class="mt-4 text-3xl font-black">Akses ditolak</h1><p class="mt-3 text-stone-500">Akunmu tidak memiliki izin untuk membuka halaman tersebut.</p><a href="{{ route('dashboard') }}" class="btn-primary mt-8">Kembali ke dashboard</a></section>
@endsection
