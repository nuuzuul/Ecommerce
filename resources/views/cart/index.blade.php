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
                        <div
                            x-data="{ openDelete: false }"
                            class="inline-flex"
                        >
                            <button
                                type="button"
                                x-on:click="openDelete = true"
                                class="icon-action icon-action-delete"
                                title="Hapus dari keranjang"
                                aria-label="Hapus dari keranjang"
                            >
                                <x-icon name="trash" />
                            </button>

                            <template x-teleport="body">
                                <div
                                    x-cloak
                                    x-show="openDelete"
                                    x-transition.opacity
                                    x-on:keydown.escape.window="openDelete = false"
                                    class="fixed inset-0 z-[9999] flex items-center justify-center px-4"
                                >
                                    <div
                                        class="absolute inset-0 bg-stone-950/70 backdrop-blur-sm"
                                        x-on:click="openDelete = false"
                                    ></div>

                                    <div
                                        x-show="openDelete"
                                        x-transition
                                        x-on:click.stop
                                        class="relative z-10 w-full max-w-md rounded-3xl bg-white p-6 text-stone-800 shadow-2xl"
                                    >
                                        <div class="flex items-start gap-4">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                                                <x-icon name="trash" />
                                            </div>

                                            <div>
                                                <h2 class="text-xl font-black text-stone-900">
                                                    Hapus dari keranjang?
                                                </h2>

                                                <p class="mt-2 text-sm leading-6 text-stone-600">
                                                    Produk
                                                    <strong>{{ $item->variant->product->name }}</strong>
                                                    akan dihapus dari keranjangmu.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <button
                                                type="button"
                                                x-on:click="openDelete = false"
                                                class="rounded-xl border border-stone-300 px-4 py-2.5 font-bold text-stone-700 transition hover:bg-stone-100"
                                            >
                                                Batal
                                            </button>

                                            <form
                                                method="POST"
                                                action="{{ route('cart.destroy', $item) }}"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="rounded-xl bg-red-600 px-4 py-2.5 font-bold text-white transition hover:bg-red-700"
                                                >
                                                    Ya, hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
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
