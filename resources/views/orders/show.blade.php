@extends('layouts.account')

@section('title', $order->order_number . ' — Kanrejawataa')

@section('account-content')
    <div class="space-y-6">
        <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-amber-700">Detail pesanan</p>
                    <h1 class="text-2xl font-black">{{ $order->order_number }}</h1>
                    <p class="mt-1 text-sm text-stone-500">
                        Dibuat {{ $order->ordered_at->format('d M Y, H:i') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <x-status-badge :status="$order->payment_status" />
                    <x-status-badge :status="$order->status" />
                </div>
            </div>
        </section>

        @if ($order->payment_status !== 'sudah_bayar')
            <section class="rounded-3xl border border-amber-200 bg-amber-50 p-6">
                <h2 class="text-xl font-black">Pembayaran manual</h2>

                @if ($order->payment_method === 'bank_transfer')
                    <p class="mt-2 text-sm text-stone-600">
                        Transfer ke
                        <b>{{ config('kanrejawataa.bank_name') }} {{ config('kanrejawataa.bank_account') }}</b>
                        a.n. <b>{{ config('kanrejawataa.bank_holder') }}</b>.
                    </p>
                @else
                    <p class="mt-2 text-sm text-stone-600">
                        Lakukan pembayaran melalui QRIS Kanrejawataa, lalu unggah tangkapan layar
                        atau foto bukti pembayaran.
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
                    class="mt-5 flex flex-wrap items-end gap-3"
                >
                    @csrf

                    <div class="min-w-[240px] flex-1">
                        <label for="payment_proof" class="form-label">Bukti pembayaran</label>
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
            </section>
        @endif

        <section class="grid gap-6 xl:grid-cols-[1fr_340px]">
            <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black">Item pesanan</h2>

                <div class="mt-4 divide-y divide-stone-100">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between gap-4 py-4">
                            <div>
                                <b>{{ $item->product_name }}</b>

                                @if ($item->variant_label)
                                    <p class="text-sm text-stone-500">{{ $item->variant_label }}</p>
                                @endif

                                <p class="text-sm text-stone-500">
                                    {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                </p>
                            </div>

                            <b>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</b>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="rounded-3xl bg-stone-950 p-6 text-white">
                <h2 class="text-xl font-black">Ringkasan</h2>

                <dl class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt>Subtotal</dt>
                        <dd>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Pengiriman</dt>
                        <dd>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-stone-700 pt-3 text-lg font-black">
                        <dt>Total</dt>
                        <dd>Rp {{ number_format($order->total, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 text-sm text-stone-300">
                    <p><b class="text-white">Pengantaran:</b> {{ $order->delivery_label }}</p>
                    <p class="mt-2">
                        <b class="text-white">Penerima:</b>
                        {{ $order->recipient_name }} · {{ $order->recipient_phone }}
                    </p>

                    @if ($order->shipping_address)
                        <p class="mt-2">
                            <b class="text-white">Alamat:</b> {{ $order->shipping_address }}
                        </p>
                    @endif
                </div>
            </aside>
        </section>

        <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black">Tracking pesanan</h2>

            <div class="mt-5 space-y-4">
                @foreach ($order->histories->sortBy('created_at') as $history)
                    <div class="flex gap-4">
                        <span class="mt-1 h-3 w-3 shrink-0 rounded-full bg-amber-500"></span>
                        <div>
                            <b>{{ ucwords(str_replace('_', ' ', $history->status)) }}</b>
                            <p class="text-sm text-stone-500">
                                {{ $history->created_at->format('d M Y, H:i') }}
                                @if ($history->changer)
                                    · {{ $history->changer->name }}
                                @endif
                            </p>

                            @if ($history->note)
                                <p class="mt-1 text-sm text-stone-600">{{ $history->note }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
