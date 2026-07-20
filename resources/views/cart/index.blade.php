@extends('layouts.account')
@section('title','Keranjang — Kanrejawataa')
@section('account-content')
<div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3"><div><p class="text-sm font-bold text-amber-700">Belanjaanmu</p><h1 class="text-3xl font-black">Keranjang</h1></div><a href="{{ route('products.index') }}" class="btn-secondary">Tambah produk</a></div>
    <div class="mt-6 space-y-4">
        @forelse($cart->items as $item)
            <div class="grid items-center gap-4 rounded-2xl border border-stone-200 p-4 sm:grid-cols-[80px_1fr_auto]">
                <img src="{{ $item->variant->product->image_url }}" class="h-20 w-20 rounded-xl object-cover" alt="{{ $item->variant->product->name }}">
                <div><h2 class="font-black">{{ $item->variant->product->name }}</h2>@if($item->variant->label)<p class="text-sm text-stone-500">{{ $item->variant->label }}</p>@endif<p class="mt-1 font-bold text-amber-700">Rp {{ number_format($item->variant->price,0,',','.') }}</p></div>
                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <form method="POST" action="{{ route('cart.update',$item) }}" class="flex items-center gap-2">@csrf @method('PATCH')<input type="number" name="quantity" min="1" max="{{ $item->variant->stock }}" value="{{ $item->quantity }}" class="w-20 rounded-xl border-stone-300"><button class="btn-secondary">Ubah</button></form>
                    <form
                                        method="POST"
                                        action="{{ route('cart.destroy', $item) }}"
                                        onsubmit="return confirm('Hapus produk dari keranjang?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="icon-action icon-action-delete"
                                            title="Hapus dari keranjang"
                                            aria-label="Hapus dari keranjang"
                                        >
                                            <x-icon name="trash" />
                                        </button>
                                    </form>
                </div>
                <div class="sm:col-start-2 sm:col-span-2 sm:text-right"><span class="text-sm text-stone-500">Subtotal:</span> <b>Rp {{ number_format($item->subtotal,0,',','.') }}</b></div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-stone-300 p-10 text-center"><p class="text-4xl">🛒</p><h2 class="mt-3 text-xl font-black">Keranjang masih kosong</h2><p class="mt-2 text-stone-500">Tambahkan kue favoritmu dari katalog.</p></div>
        @endforelse
    </div>
    @if($cart->items->isNotEmpty())
        <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-amber-50 p-5"><div><p class="text-sm text-stone-500">Total sementara</p><p class="text-2xl font-black text-amber-800">Rp {{ number_format($cart->subtotal,0,',','.') }}</p></div><a href="{{ route('checkout.create') }}" class="btn-primary">Lanjut checkout</a></div>
    @endif
</div>
@endsection
