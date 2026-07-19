@extends('layouts.store')
@section('title', 'Kanrejawataa — Kue Kering & Kue Tradisional Makassar')
@section('content')
<section class="relative overflow-hidden bg-stone-950 text-white">
    <div class="absolute inset-0 opacity-20 [background-image:radial-gradient(circle_at_20%_20%,#f59e0b_0,transparent_32%),radial-gradient(circle_at_80%_80%,#fbbf24_0,transparent_28%)]"></div>
    <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-28">
        <div>
            <span class="inline-flex rounded-full border border-amber-400/40 bg-amber-400/10 px-4 py-2 text-sm font-bold text-amber-300">Cita rasa Makassar dalam setiap gigitan</span>
            <h1 class="mt-6 text-4xl font-black leading-tight sm:text-5xl lg:text-6xl">Kue istimewa untuk momen yang tak biasa.</h1>
            <p class="mt-5 max-w-xl text-lg leading-8 text-stone-300">Nikmati kue kering premium dan kue tradisional Makassar, dibuat dengan bahan pilihan dan rasa yang akrab di rumah.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('products.index') }}" class="rounded-2xl bg-amber-500 px-6 py-3 font-black text-stone-950 hover:bg-amber-400">Belanja sekarang</a>
                <a href="{{ route('about') }}" class="rounded-2xl border border-stone-600 px-6 py-3 font-bold hover:border-amber-400 hover:text-amber-300">Kenal Kanrejawataa</a>
            </div>
        </div>
        <div class="relative mx-auto w-full max-w-lg">
            <div class="aspect-square rounded-[3rem] bg-gradient-to-br from-amber-300 to-orange-600 p-4 shadow-2xl shadow-amber-900/30 rotate-2">
                <div class="grid h-full place-items-center rounded-[2.5rem] bg-amber-50 text-[10rem] -rotate-2">🍪</div>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="text-center">
        <p class="text-sm font-black uppercase tracking-[.2em] text-amber-700">Pilih sesuai selera</p>
        <h2 class="mt-2 text-3xl font-black text-stone-900 sm:text-4xl">Kategori Kanrejawataa</h2>
    </div>
    <div class="mt-8 grid gap-5 md:grid-cols-2">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category) }}" class="group rounded-3xl border border-amber-100 bg-white p-7 shadow-sm hover:border-amber-300 hover:shadow-lg">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-amber-700">{{ $category->products_count }} produk</p>
                        <h3 class="mt-1 text-2xl font-black text-stone-900">{{ $category->name }}</h3>
                        <p class="mt-2 text-sm leading-6 text-stone-500">{{ $category->description }}</p>
                    </div>
                    <span class="text-4xl transition group-hover:translate-x-1">→</span>
                </div>
            </a>
        @endforeach
    </div>
</section>

<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div><p class="text-sm font-black uppercase tracking-[.2em] text-amber-700">Pilihan favorit</p><h2 class="mt-2 text-3xl font-black text-stone-900">Produk unggulan</h2></div>
            <a href="{{ route('products.index') }}" class="font-bold text-amber-700 hover:text-amber-900">Lihat semua produk →</a>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-stone-300 p-10 text-center text-stone-500">Belum ada produk unggulan.</div>
            @endforelse
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid gap-5 md:grid-cols-3">
        @foreach([['Bahan pilihan','Kami memilih bahan yang segar dan berkualitas.','🌾'],['Rasa khas Makassar','Resep tradisional dipertahankan dengan sentuhan modern.','🏠'],['Ambil atau dikirim','Pilih ambil sendiri atau pengantaran ke alamatmu.','🚚']] as [$title,$desc,$icon])
            <div class="rounded-3xl bg-amber-100/60 p-7"><span class="text-4xl">{{ $icon }}</span><h3 class="mt-4 text-xl font-black">{{ $title }}</h3><p class="mt-2 text-sm leading-6 text-stone-600">{{ $desc }}</p></div>
        @endforeach
    </div>
</section>
@endsection
