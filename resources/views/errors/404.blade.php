@extends('layouts.store')
@section('title','404 — Halaman Tidak Ditemukan')
@section('content')
<section class="mx-auto max-w-3xl px-4 py-24 text-center"><p class="text-8xl font-black text-amber-500">404</p><h1 class="mt-4 text-3xl font-black">Halaman tidak ditemukan</h1><p class="mt-3 text-stone-500">Halaman atau produk yang kamu cari mungkin sudah dipindahkan atau tidak tersedia.</p><div class="mt-8 flex justify-center gap-3"><a href="{{ route('home') }}" class="btn-primary">Kembali ke beranda</a><a href="{{ route('products.index') }}" class="btn-secondary">Lihat produk</a></div></section>
@endsection
