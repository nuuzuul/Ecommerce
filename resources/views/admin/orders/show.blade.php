@extends('layouts.admin')

@section('title', $order->order_number . ' — Admin')
@section('page-title', 'Detail Pesanan')

@section('content')
    <div class="space-y-6">
        <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-amber-700">
                        {{ $order->order_number }}
                    </p>
                    <h2 class="mt-1 text-2xl font-black text-stone-900">
                        {{ $order->user->name }}
                    </h2>
                    <p class="mt-1 break-words text-sm text-stone-500">
                        {{ $order->user->email }} · {{ $order->recipient_phone }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <x-status-badge :status="$order->payment_status" />
                    <x-status-badge :status="$order->status" />
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="min-w-0 space-y-6">
                <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-black">Item pesanan</h3>

                    <div class="mt-4 divide-y divide-stone-100">
                        @foreach ($order->items as $item)
                            <div class="flex flex-col gap-2 py-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <b>{{ $item->product_name }}</b>
                                    @if ($item->variant_label)
                                        <span class="ml-1 text-sm text-stone-500">
                                            ({{ $item->variant_label }})
                                        </span>
                                    @endif
                                    <p class="text-sm text-stone-500">
                                        {{ $item->quantity }} ×
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                </div>

                                <b class="shrink-0">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </b>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-xl font-black">Pembayaran</h3>
                            <p class="mt-1 text-sm text-stone-600">
                                Metode: <b>{{ $order->payment_method_label }}</b>
                            </p>
                        </div>

                        @if ($order->payment_proof)
                            <a
                                href="{{ route('orders.payment-proof.show', $order) }}"
                                target="_blank"
                                rel="noopener"
                                class="btn-secondary gap-2"
                            >
                                <x-icon name="external-link" />
                                <span>Buka gambar penuh</span>
                            </a>
                        @endif
                    </div>

                    @if ($order->payment_proof)
                        <a
                            href="{{ route('orders.payment-proof.show', $order) }}"
                            target="_blank"
                            rel="noopener"
                            class="mt-4 block overflow-hidden rounded-2xl border border-stone-200 bg-stone-50"
                        >
                            <img
                                src="{{ route('orders.payment-proof.show', $order) }}"
                                alt="Bukti pembayaran {{ $order->order_number }}"
                                class="mx-auto max-h-[28rem] w-full object-contain"
                            >
                        </a>
                    @else
                        <p class="mt-4 rounded-xl bg-stone-100 p-4 text-sm text-stone-500">
                            Pembeli belum mengunggah bukti pembayaran.
                        </p>
                    @endif

                    @if ($order->payment_status === 'menunggu_verifikasi')
                        <div class="mt-5 grid gap-3 lg:grid-cols-[auto_minmax(0,1fr)]">
                            <form
                                method="POST"
                                action="{{ route('admin.orders.verify-payment', $order) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <button class="btn-primary w-full lg:w-auto">
                                    Verifikasi pembayaran
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('admin.orders.reject-payment', $order) }}"
                                class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto]"
                            >
                                @csrf
                                @method('PATCH')

                                <input
                                    name="payment_note"
                                    class="form-input"
                                    placeholder="Alasan penolakan"
                                    required
                                >
                                <button class="rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    @endif
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-3xl bg-stone-950 p-6 text-white shadow-sm">
                    <h3 class="text-xl font-black">Ringkasan</h3>

                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt>Subtotal</dt>
                            <dd>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt>Pengiriman</dt>
                            <dd>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-t border-stone-700 pt-3 text-lg font-black">
                            <dt>Total</dt>
                            <dd>Rp {{ number_format($order->total, 0, ',', '.') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-5 space-y-2 text-sm text-stone-300">
                        <p>
                            <b class="text-white">Metode:</b>
                            {{ $order->delivery_label }}
                        </p>

                        @if ($order->shipping_address)
                            <p class="break-words">
                                <b class="text-white">Alamat:</b>
                                {{ $order->shipping_address }}
                            </p>
                        @else
                            <p class="break-words">
                                <b class="text-white">Lokasi ambil:</b>
                                {{ config('kanrejawataa.pickup_address') }}
                            </p>
                        @endif
                    </div>
                </section>

                <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-black">
                        Perbarui status
                    </h3>

                    @if ($order->status === 'dibatalkan')
                        <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 text-red-600">
                                    <x-icon name="info" />
                                </div>

                                <div>
                                    <p class="font-bold text-red-700">
                                        Pesanan dibatalkan
                                    </p>

                                    <p class="mt-1 text-sm text-red-600">
                                        Pesanan ini sudah dibatalkan dan statusnya tidak
                                        dapat diubah kembali.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif ($order->status === 'selesai')
                        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                            <p class="font-bold text-emerald-700">
                                Pesanan sudah selesai.
                            </p>
                        </div>
                    @else
                        <form
                            method="POST"
                            action="{{ route('admin.orders.status', $order) }}"
                            class="mt-4 space-y-3"
                        >
                            @csrf
                            @method('PATCH')

                            <select
                                name="status"
                                class="form-input"
                            >
                                @php
                                    $availableStatuses = $order->delivery_method === 'pickup'
                                        ? [
                                            'diproses',
                                            'siap_diambil',
                                            'selesai',
                                            'dibatalkan',
                                        ]
                                        : [
                                            'diproses',
                                            'dikirim',
                                            'selesai',
                                            'dibatalkan',
                                        ];
                                @endphp

                                @foreach ($availableStatuses as $status)
                                    <option
                                        value="{{ $status }}"
                                        @selected($order->status === $status)
                                    >
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('status')
                                <p class="form-error">
                                    {{ $message }}
                                </p>
                            @enderror

                            <textarea
                                name="note"
                                rows="3"
                                class="form-input"
                                placeholder="Catatan tracking atau alasan pembatalan"
                            >{{ old('note') }}</textarea>

                            @error('note')
                                <p class="form-error">
                                    {{ $message }}
                                </p>
                            @enderror

                            <button class="btn-primary w-full">
                                Simpan status
                            </button>
                        </form>
                    @endif
                </section>
            </aside>
        </div>

        <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            <h3 class="text-xl font-black">Riwayat status</h3>

            <div class="mt-4 space-y-4">
                @foreach ($order->histories->sortBy('created_at') as $history)
                    <div class="flex gap-3">
                        <span class="mt-1 h-3 w-3 shrink-0 rounded-full bg-amber-500"></span>
                        <div class="min-w-0">
                            <b>{{ ucwords(str_replace('_', ' ', $history->status)) }}</b>
                            <p class="text-sm text-stone-500">
                                {{ $history->created_at->format('d M Y H:i') }}
                                @if ($history->changer)
                                    · {{ $history->changer->name }}
                                @endif
                            </p>
                            @if ($history->note)
                                <p class="break-words text-sm">{{ $history->note }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
