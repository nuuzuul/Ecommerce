@extends('layouts.admin')

@section('title', 'Produk — Admin Kanrejawataa')
@section('page-title', 'Kelola Produk')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-black">Daftar produk</h2>
            <p class="text-sm text-stone-500">
                Kelola informasi, harga, ukuran, stok, dan foto produk.
            </p>
        </div>

        <a href="{{ route('admin.products.create') }}" class="btn-primary gap-2">
            <x-icon name="plus" />
            <span>Tambah produk</span>
        </a>
    </div>

    <form
        method="GET"
        class="mt-5 grid gap-3 rounded-2xl border border-stone-200 bg-white p-4 md:grid-cols-[minmax(0,1fr)_220px_180px_auto]"
    >
        <input
            name="search"
            value="{{ request('search') }}"
            class="form-input"
            placeholder="Cari nama produk"
        >

        <select name="category" class="form-input">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected((string) request('category') === (string) $category->id)
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="stock" class="form-input">
            <option value="">Semua stok</option>
            <option value="low" @selected(request('stock') === 'low')>
                Stok menipis
            </option>
            <option value="out" @selected(request('stock') === 'out')>
                Stok habis
            </option>
        </select>

        <button class="btn-primary gap-2">
            <x-icon name="search" />
            <span>Filter</span>
        </button>
    </form>

    <div class="mt-6 overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ $product->image_url }}"
                                        alt="{{ $product->name }}"
                                        class="h-12 w-12 rounded-xl object-cover"
                                    >
                                    <div>
                                        <b>{{ $product->name }}</b>
                                        @if ($product->is_featured)
                                            <p class="text-xs font-bold text-amber-700">
                                                Unggulan
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->category->name }}</td>
                            <td>
                                Rp {{ number_format($product->minimum_price, 0, ',', '.') }}
                                @if ($product->variants->count() > 1)
                                    <small class="block text-stone-500">mulai dari</small>
                                @endif
                            </td>
                            <td>{{ $product->total_stock }}</td>
                            <td>
                                <span class="font-bold {{ $product->is_active
                                    ? 'text-emerald-600'
                                    : 'text-red-600' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a
                                        href="{{ route('admin.products.show', $product) }}"
                                        class="icon-action icon-action-view"
                                        title="Lihat produk"
                                        aria-label="Lihat produk"
                                    >
                                        <x-icon name="eye" />
                                    </a>

                                    <a
                                        href="{{ route('admin.products.edit', $product) }}"
                                        class="icon-action icon-action-edit"
                                        title="Edit produk"
                                        aria-label="Edit produk"
                                    >
                                        <x-icon name="edit" />
                                    </a>

                                    <div
                                        x-data="{ openDelete: false }"
                                        class="inline-flex"
                                    >
                                        <button
                                            type="button"
                                            x-on:click="openDelete = true"
                                            class="icon-action icon-action-delete"
                                            title="Hapus produk"
                                            aria-label="Hapus produk"
                                        >
                                            <x-icon name="trash" />
                                        </button>

                                        <div
                                            x-cloak
                                            x-show="openDelete"
                                            x-transition.opacity
                                            x-on:keydown.escape.window="openDelete = false"
                                            class="fixed inset-0 z-50 flex items-center justify-center px-4"
                                        >
                                            <div
                                                class="absolute inset-0 bg-stone-950/60"
                                                x-on:click="openDelete = false"
                                            ></div>

                                            <div
                                                x-show="openDelete"
                                                x-transition
                                                class="relative z-10 w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl"
                                            >
                                                <div class="flex items-start gap-4">
                                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                                                        <x-icon name="trash" />
                                                    </div>

                                                    <div>
                                                        <h2 class="text-xl font-black text-stone-900">
                                                            Hapus produk?
                                                        </h2>

                                                        <p class="mt-2 text-sm leading-6 text-stone-600">
                                                            Produk
                                                            <strong>{{ $product->name }}</strong>
                                                            akan dihapus atau dinonaktifkan.
                                                            Tindakan ini tidak dapat dibatalkan.
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
                                                        action="{{ route('admin.products.destroy', $product) }}"
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
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-stone-500">
                                Belum ada produk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $products->links() }}
    </div>
@endsection
