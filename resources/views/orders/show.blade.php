@extends('layouts.account')

@section('title', $order->order_number . ' — Kanrejawataa')

@section('account-content')
    <div class="space-y-6">
        <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-amber-700">Detail pesanan</p>
                    <h1 class="mt-1 break-words text-2xl font-black">
                        {{ $order->order_number }}
                    </h1>
                    <p class="mt-1 text-sm text-stone-500">
                        Dibuat {{ $order->ordered_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 sm:justify-end">
                    <x-status-badge :status="$order->payment_status" />
                    <x-status-badge :status="$order->status" />
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-black">Pembayaran</h2>
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
                        <span>Lihat bukti</span>
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
                        class="mx-auto max-h-80 w-full object-contain"
                    >
                </a>
            @endif

            @if ($order->payment_status !== 'sudah_bayar')
                <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    @if ($order->payment_method === 'bank_transfer')
                        <p class="text-sm text-stone-600">
                            Transfer ke
                            <b>
                                {{ config('kanrejawataa.bank_name') }}
                                {{ config('kanrejawataa.bank_account') }}
                            </b>
                            a.n. <b>{{ config('kanrejawataa.bank_holder') }}</b>.
                        </p>
                    @else
                        <p class="text-sm text-stone-600">
                            Lakukan pembayaran melalui QRIS Kanrejawataa, lalu unggah
                            tangkapan layar atau foto bukti pembayaran.
                        </p>

                        @if (config('kanrejawataa.qris_image'))
                            <img
                                src="{{ asset(config('kanrejawataa.qris_image')) }}"
                                alt="QRIS Kanrejawataa"
                                class="mt-4 h-52 w-52 rounded-2xl bg-white object-contain p-3"
                            >
                        @endif
                    @endif

                    @if ($order->payment_note)
                        <x-alert type="error" class="mt-4">
                            Catatan admin: {{ $order->payment_note }}
                        </x-alert>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('orders.payment-proof', $order) }}"
                        enctype="multipart/form-data"
                        class="mt-5 grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end"
                    >
                        @csrf

                        <div>
                            <label for="payment_proof" class="form-label">
                                Bukti pembayaran
                            </label>
                            <input
                                id="payment_proof"
                                type="file"
                                name="payment_proof"
                                accept="image/*"
                                class="form-input"
                                required
                            >

                            @error('payment_proof')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn-primary">
                            {{ $order->payment_proof ? 'Unggah ulang' : 'Unggah bukti' }}
                        </button>
                    </form>

                    @if ($order->payment_proof)
                        <p class="mt-3 text-sm font-semibold text-amber-800">
                            Bukti telah diunggah. Status:
                            {{ str_replace('_', ' ', $order->payment_status) }}.
                        </p>
                    @endif
                </div>
            @elseif ($order->payment_proof)
                <p class="mt-4 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-700">
                    Pembayaran sudah diverifikasi. Bukti pembayaran tetap dapat dilihat
                    melalui tombol di atas.
                </p>
            @endif
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            <div class="min-w-0 rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black">Item pesanan</h2>

                <div class="mt-4 divide-y divide-stone-100">
                    @foreach ($order->items as $item)
                        <div class="flex flex-col gap-2 py-4 sm:flex-row sm:justify-between">
                            <div>
                                <b>{{ $item->product_name }}</b>
                                @if ($item->variant_label)
                                    <p class="text-sm text-stone-500">
                                        {{ $item->variant_label }}
                                    </p>
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
            </div>

            <aside class="rounded-3xl bg-stone-950 p-6 text-white shadow-sm">
                <h2 class="text-xl font-black">Ringkasan</h2>

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
                        <b class="text-white">Pengantaran:</b>
                        {{ $order->delivery_label }}
                    </p>
                    <p class="break-words">
                        <b class="text-white">Penerima:</b>
                        {{ $order->recipient_name }} · {{ $order->recipient_phone }}
                    </p>
                    @if ($order->shipping_address)
                        <p class="break-words">
                            <b class="text-white">Alamat:</b>
                            {{ $order->shipping_address }}
                        </p>
                    @endif
                </div>
            </aside>
        </section>

        <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black">Tracking pesanan</h2>

            <div class="mt-5 space-y-4">
                @foreach ($order->histories->sortBy('created_at') as $history)
                    <div class="flex gap-3">
                        <span class="mt-1 h-3 w-3 shrink-0 rounded-full bg-amber-500"></span>
                        <div class="min-w-0">
                            <b>{{ ucwords(str_replace('_', ' ', $history->status)) }}</b>
                            <p class="text-sm text-stone-500">
                                {{ $history->created_at->format('d M Y H:i') }}
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
