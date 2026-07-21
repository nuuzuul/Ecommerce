@extends('layouts.admin')

@section('title', 'Kategori — Admin Kanrejawataa')
@section('page-title', 'Kelola Kategori')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-black">Daftar kategori</h2>
            <p class="text-sm text-stone-500">
                Kategori menentukan apakah produk memakai ukuran atau tidak.
            </p>
        </div>

        <a href="{{ route('admin.categories.create') }}" class="btn-primary gap-2">
            <x-icon name="plus" />
            <span>Tambah kategori</span>
        </a>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tipe ukuran</th>
                        <th>Produk</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <b>{{ $category->name }}</b>
                                <p class="text-xs text-stone-500">/{{ $category->slug }}</p>
                            </td>
                            <td>
                                {{ $category->uses_variants
                                    ? '500 gram & 1 kg'
                                    : 'Tanpa ukuran' }}
                            </td>
                            <td>{{ $category->products_count }}</td>
                            <td>
                                <span class="font-bold {{ $category->is_active
                                    ? 'text-emerald-600'
                                    : 'text-red-600' }}">
                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a
                                        href="{{ route('admin.categories.edit', $category) }}"
                                        class="icon-action icon-action-edit"
                                        title="Edit kategori"
                                        aria-label="Edit kategori"
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
                                            title="Hapus kategori"
                                            aria-label="Hapus kategori"
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
                                                            Hapus kategori?
                                                        </h2>

                                                        <p class="mt-2 text-sm leading-6 text-stone-600">
                                                            Kategori
                                                            <strong>{{ $category->name }}</strong>
                                                            akan dihapus. Tindakan ini tidak dapat dibatalkan.
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
                                                        action="{{ route('admin.categories.destroy', $category) }}"
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
                            <td colspan="5" class="text-center text-stone-500">
                                Belum ada kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $categories->links() }}
    </div>
@endsection
