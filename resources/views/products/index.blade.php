@extends('layouts.store')
@section('title', 'Katalog Produk — Kanrejawataa')
@section('content')
<section class="border-b border-amber-100 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <p class="text-sm font-black uppercase tracking-[.2em] text-amber-700">Katalog</p>
        <h1 class="mt-2 text-4xl font-black text-stone-900">Temukan kue favoritmu</h1>
        <p class="mt-3 text-stone-500">Cari, filter, dan pilih produk berdasarkan kategori, harga, dan ketersediaan.</p>
    </div>
</section>
<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <form method="GET" action="{{ route('products.index') }}" class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2"><label class="form-label">Cari produk</label><input name="search" value="{{ request('search') }}" class="form-input" placeholder="Contoh: nastar"></div>
            <div><label class="form-label">Kategori</label><select name="category" class="form-input"><option value="">Semua</option>@foreach($categories as $category)<option value="{{ $category->slug }}" @selected(request('category')===$category->slug)>{{ $category->name }}</option>@endforeach</select></div>
            <div><label class="form-label">Harga minimum</label><input type="number" name="min_price" value="{{ request('min_price') }}" class="form-input" placeholder="0"></div>
            <div><label class="form-label">Harga maksimum</label><input type="number" name="max_price" value="{{ request('max_price') }}" class="form-input" placeholder="200000"></div>
            <div><label class="form-label">Urutkan</label><select name="sort" class="form-input"><option value="newest">Terbaru</option><option value="price_asc" @selected(request('sort')==='price_asc')>Harga termurah</option><option value="price_desc" @selected(request('sort')==='price_desc')>Harga termahal</option><option value="name" @selected(request('sort')==='name')>Nama A-Z</option></select></div>
        </div>
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <label class="flex items-center gap-2 text-sm font-medium"><input type="checkbox" name="stock" value="available" @checked(request('stock')==='available') class="rounded border-stone-300 text-amber-600"> Hanya stok tersedia</label>
            <div class="flex gap-2"><a href="{{ route('products.index') }}" class="btn-secondary">Reset</a><button class="btn-primary">Terapkan filter</button></div>
        </div>
    </form>

    <div class="mt-8 flex items-center justify-between"><p class="text-sm text-stone-500">Menampilkan {{ $products->total() }} produk</p></div>
    <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($products as $product)<x-product-card :product="$product" />@empty<div class="col-span-full rounded-3xl border border-dashed border-stone-300 bg-white p-12 text-center"><h2 class="text-xl font-black">Produk tidak ditemukan</h2><p class="mt-2 text-stone-500">Coba ubah kata pencarian atau filter yang digunakan.</p></div>@endforelse
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
</section>
@endsection
