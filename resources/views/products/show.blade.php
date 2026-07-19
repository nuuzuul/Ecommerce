@extends('layouts.store')
@section('title', $product->name.' — Kanrejawataa')
@section('content')
<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="grid gap-10 lg:grid-cols-2">
        <div class="overflow-hidden rounded-[2rem] border border-amber-100 bg-white shadow-sm"><img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="aspect-[4/3] h-full w-full object-cover"></div>
        <div>
            <a href="{{ route('categories.show', $product->category) }}" class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-sm font-bold text-amber-800">{{ $product->category->name }}</a>
            <h1 class="mt-4 text-4xl font-black text-stone-900">{{ $product->name }}</h1>
            <p class="mt-4 leading-7 text-stone-600">{{ $product->description }}</p>
            <div class="mt-6 rounded-3xl border border-stone-200 bg-white p-5">
                <p class="text-sm font-bold text-stone-500">Harga</p>
                <p class="mt-1 text-3xl font-black text-amber-700">Mulai Rp {{ number_format($product->minimum_price,0,',','.') }}</p>
                <form method="POST" action="{{ route('cart.store') }}" class="mt-6 space-y-4">@csrf
                    <div><label class="form-label">{{ $product->category->uses_variants ? 'Pilih ukuran' : 'Pilihan produk' }}</label><select name="product_variant_id" class="form-input" required>@foreach($product->variants as $variant)<option value="{{ $variant->id }}" @disabled($variant->stock===0)>{{ $variant->label ?: $product->name }} — Rp {{ number_format($variant->price,0,',','.') }} (stok {{ $variant->stock }})</option>@endforeach</select>@error('product_variant_id')<p class="form-error">{{ $message }}</p>@enderror</div>
                    <div><label class="form-label">Jumlah</label><input type="number" name="quantity" min="1" value="1" class="form-input">@error('quantity')<p class="form-error">{{ $message }}</p>@enderror</div>
                    @auth
                        @if(auth()->user()->isBuyer())<button class="btn-primary w-full" @disabled($product->total_stock===0)>Tambah ke keranjang</button>@else<div class="rounded-xl bg-stone-100 p-3 text-sm">Admin tidak dapat berbelanja. Gunakan akun pembeli.</div>@endif
                    @else
                        <a href="{{ route('login') }}" class="btn-primary block text-center">Masuk untuk membeli</a>
                    @endauth
                </form>
            </div>
        </div>
    </div>
</section>
@if($relatedProducts->isNotEmpty())
<section class="bg-white py-14"><div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"><h2 class="text-2xl font-black">Produk terkait</h2><div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@foreach($relatedProducts as $related)<x-product-card :product="$related" />@endforeach</div></div></section>
@endif
@endsection
