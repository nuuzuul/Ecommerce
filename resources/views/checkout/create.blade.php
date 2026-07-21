@extends('layouts.account')

@section('title', 'Checkout — Kanrejawataa')

@section('account-content')
    <form
        method="POST"
        action="{{ route('checkout.store') }}"
        class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]"
        x-data="checkoutShipping({
            initialDelivery: @js(old('delivery_method', 'pickup')),
            searchUrl: @js(route('shipping.destinations')),
            costUrl: @js(route('shipping.costs')),
            subtotal: @js((int) $cart->subtotal),
        })"
        x-init="init()"
        x-on:submit="submitting = true"
    >
        @csrf

        <input
            type="hidden"
            name="destination_token"
            :value="destinationToken"
        >

        <input
            type="hidden"
            name="shipping_option_token"
            :value="shippingOptionToken"
        >

        <div class="space-y-6">

            {{-- Data penerima --}}
            <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h1 class="text-2xl font-black">
                    Data penerima
                </h1>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label">
                            Nama penerima
                        </label>

                        <input
                            type="text"
                            name="recipient_name"
                            value="{{ old(
                                'recipient_name',
                                auth()->user()->name
                            ) }}"
                            class="form-input"
                        >

                        @error('recipient_name')
                            <p class="form-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">
                            Nomor telepon
                        </label>

                        <input
                            type="text"
                            name="recipient_phone"
                            value="{{ old(
                                'recipient_phone',
                                auth()->user()->phone
                            ) }}"
                            class="form-input"
                        >

                        @error('recipient_phone')
                            <p class="form-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Metode pengantaran --}}
            <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black">
                    Metode pengantaran
                </h2>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <label class="option-card">
                        <input
                            type="radio"
                            name="delivery_method"
                            value="pickup"
                            x-model="delivery"
                        >

                        <span>
                            <b>Ambil sendiri</b>

                            <small>
                                Ambil di
                                {{ config('kanrejawataa.pickup_address') }}
                            </small>
                        </span>
                    </label>

                    <label class="option-card">
                        <input
                            type="radio"
                            name="delivery_method"
                            value="delivery"
                            x-model="delivery"
                        >

                        <span>
                            <b>Dikirimkan</b>

                            <small>
                                Ongkir dihitung otomatis sesuai lokasi
                            </small>
                        </span>
                    </label>
                </div>

                <div
                    x-cloak
                    x-show="delivery === 'delivery'"
                    class="mt-6 space-y-5"
                >
                    {{-- Alamat lengkap --}}
                    <div>
                        <label class="form-label">
                            Alamat lengkap
                        </label>

                        <textarea
                            name="shipping_address"
                            rows="4"
                            class="form-input"
                            placeholder="Nama jalan, nomor rumah, RT/RW, dan patokan"
                        >{{ old(
                            'shipping_address',
                            auth()->user()->address
                        ) }}</textarea>

                        @error('shipping_address')
                            <p class="form-error">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Pencarian tujuan --}}
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                        <h3 class="font-black text-stone-900">
                            Cari lokasi tujuan
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-stone-600">
                            Masukkan nama kota, kecamatan, kelurahan,
                            atau kode pos.
                        </p>

                        <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                            <input
                                type="text"
                                x-model="searchQuery"
                                x-on:keydown.enter.prevent="searchDestinations"
                                class="form-input flex-1"
                                placeholder="Contoh: Panakkukang Makassar"
                            >

                            <button
                                type="button"
                                x-on:click="searchDestinations"
                                :disabled="searching"
                                class="btn-primary shrink-0 gap-2 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <x-icon name="search" />

                                <span
                                    x-text="searching
                                        ? 'Mencari...'
                                        : 'Cari lokasi'"
                                ></span>
                            </button>
                        </div>

                        {{-- Error --}}
                        <div
                            x-cloak
                            x-show="errorMessage"
                            class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
                        >
                            <span x-text="errorMessage"></span>
                        </div>

                        {{-- Hasil pencarian --}}
                        <div
                            x-cloak
                            x-show="destinations.length > 0"
                            class="mt-4 overflow-hidden rounded-2xl border border-stone-200 bg-white"
                        >
                            <template
                                x-for="destination in destinations"
                                :key="destination.id"
                            >
                                <button
                                    type="button"
                                    x-on:click="selectDestination(destination)"
                                    class="block w-full border-b border-stone-100 px-4 py-3 text-left transition last:border-b-0 hover:bg-amber-50"
                                >
                                    <span
                                        class="block font-bold text-stone-900"
                                        x-text="destination.subdistrict_name
                                            || destination.city_name"
                                    ></span>

                                    <span
                                        class="mt-1 block text-sm leading-5 text-stone-500"
                                        x-text="destination.label"
                                    ></span>
                                </button>
                            </template>
                        </div>

                        {{-- Lokasi terpilih --}}
                        <div
                            x-cloak
                            x-show="selectedDestination"
                            class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4"
                        >
                            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">
                                Lokasi dipilih
                            </p>

                            <p
                                class="mt-1 font-bold text-stone-900"
                                x-text="selectedDestination?.label"
                            ></p>
                        </div>

                        @error('destination_token')
                            <p class="form-error mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Pilihan ongkir --}}
                    <div
                        x-cloak
                        x-show="selectedDestination"
                        class="rounded-2xl border border-stone-200 bg-white p-5"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="font-black text-stone-900">
                                    Pilih layanan pengiriman
                                </h3>

                                <p
                                    x-cloak
                                    x-show="formattedWeight"
                                    class="mt-1 text-sm text-stone-500"
                                >
                                    Berat paket:
                                    <strong x-text="formattedWeight"></strong>
                                </p>
                            </div>

                            <span
                                x-cloak
                                x-show="calculating"
                                class="text-sm font-semibold text-amber-700"
                            >
                                Menghitung ongkir...
                            </span>
                        </div>

                        <div
                            x-cloak
                            x-show="
                                ! calculating
                                && selectedDestination
                                && shippingOptions.length === 0
                                && shippingCalculated
                            "
                            class="mt-4 rounded-xl border border-dashed border-stone-300 p-5 text-center text-sm text-stone-500"
                        >
                            Tidak ada layanan pengiriman yang tersedia.
                        </div>

                        <div class="mt-4 grid gap-3">
                            <template
                                x-for="option in shippingOptions"
                                :key="[
                                    option.courier_code,
                                    option.service,
                                    option.cost
                                ].join('-')"
                            >
                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-2xl border p-4 transition"
                                    :class="
                                        shippingOptionToken
                                            === option.shipping_option_token
                                            ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-100'
                                            : 'border-stone-200 hover:border-amber-300'
                                    "
                                >
                                    <input
                                        type="radio"
                                        class="mt-1 border-stone-300 text-amber-600 focus:ring-amber-500"
                                        :value="option.shipping_option_token"
                                        x-model="shippingOptionToken"
                                        x-on:change="selectShippingOption(option)"
                                    >

                                    <span class="min-w-0 flex-1">
                                        <span class="flex flex-wrap items-center justify-between gap-2">
                                            <b
                                                class="text-stone-900"
                                                x-text="
                                                    option.courier_code
                                                        .toUpperCase()
                                                    + ' '
                                                    + option.service
                                                "
                                            ></b>

                                            <b
                                                class="text-amber-700"
                                                x-text="option.formatted_cost"
                                            ></b>
                                        </span>

                                        <span
                                            class="mt-1 block text-sm text-stone-500"
                                            x-text="option.description"
                                        ></span>

                                        <span
                                            class="mt-1 block text-xs font-semibold text-stone-500"
                                            x-text="
                                                option.etd
                                                    ? 'Estimasi: ' + option.etd
                                                    : 'Estimasi tidak tersedia'
                                            "
                                        ></span>
                                    </span>
                                </label>
                            </template>
                        </div>

                        @error('shipping_option_token')
                            <p class="form-error mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Metode pembayaran --}}
            <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black">
                    Metode pembayaran
                </h2>

                <p class="mt-1 text-sm text-stone-500">
                    Setelah checkout, unggah bukti pembayaran dari
                    halaman detail pesanan.
                </p>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <label class="option-card">
                        <input
                            type="radio"
                            name="payment_method"
                            value="bank_transfer"
                            @checked(
                                old(
                                    'payment_method',
                                    'bank_transfer'
                                ) === 'bank_transfer'
                            )
                        >

                        <span>
                            <b>Transfer bank</b>

                            <small>
                                {{ config('kanrejawataa.bank_name') }}
                                {{ config('kanrejawataa.bank_account') }}
                            </small>
                        </span>
                    </label>

                    <label class="option-card">
                        <input
                            type="radio"
                            name="payment_method"
                            value="qris"
                            @checked(
                                old('payment_method') === 'qris'
                            )
                        >

                        <span>
                            <b>QRIS</b>

                            <small>
                                Pembayaran manual melalui QRIS
                            </small>
                        </span>
                    </label>
                </div>

                <div class="mt-4">
                    <label class="form-label">
                        Catatan pesanan (opsional)
                    </label>

                    <textarea
                        name="notes"
                        rows="3"
                        class="form-input"
                    >{{ old('notes') }}</textarea>
                </div>
            </section>
        </div>

        {{-- Ringkasan pesanan --}}
        <aside class="h-fit rounded-3xl bg-stone-950 p-6 text-white xl:sticky xl:top-24">
            <h2 class="text-xl font-black">
                Ringkasan pesanan
            </h2>

            <div class="mt-5 space-y-3">
                @foreach ($cart->items as $item)
                    <div class="flex justify-between gap-3 text-sm">
                        <span>
                            {{ $item->variant->product->name }}

                            @if ($item->variant->label)
                                ({{ $item->variant->label }})
                            @endif

                            × {{ $item->quantity }}
                        </span>

                        <b>
                            Rp {{ number_format(
                                $item->subtotal,
                                0,
                                ',',
                                '.'
                            ) }}
                        </b>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 space-y-3 border-t border-stone-700 pt-4">
                <div class="flex justify-between">
                    <span>Subtotal</span>

                    <b>
                        Rp {{ number_format(
                            $cart->subtotal,
                            0,
                            ',',
                            '.'
                        ) }}
                    </b>
                </div>

                <div
                    x-cloak
                    x-show="delivery === 'delivery'"
                    class="flex justify-between"
                >
                    <span>Ongkir</span>

                    <b x-text="formatRupiah(shippingCost)"></b>
                </div>

                <div class="flex justify-between border-t border-stone-700 pt-3 text-lg">
                    <span class="font-bold">
                        Total
                    </span>

                    <b
                        class="text-amber-400"
                        x-text="formatRupiah(grandTotal)"
                    ></b>
                </div>
            </div>

            <p
                x-cloak
                x-show="
                    delivery === 'delivery'
                    && ! shippingOptionToken
                "
                class="mt-4 rounded-xl border border-amber-700 bg-amber-950 px-4 py-3 text-sm text-amber-200"
            >
                Cari lokasi dan pilih layanan pengiriman terlebih dahulu.
            </p>

            <button
                type="submit"
                :disabled="
                    submitting
                    || (
                        delivery === 'delivery'
                        && ! shippingOptionToken
                    )
                "
                class="mt-6 w-full rounded-2xl bg-amber-500 px-5 py-3 font-black text-stone-950 transition hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <span
                    x-text="submitting
                        ? 'Memproses...'
                        : 'Buat pesanan'"
                ></span>
            </button>
        </aside>
    </form>

    @once
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('checkoutShipping', (config) => ({
                    delivery: config.initialDelivery,
                    searchUrl: config.searchUrl,
                    costUrl: config.costUrl,
                    subtotal: Number(config.subtotal),

                    searchQuery: '',
                    searching: false,
                    calculating: false,
                    submitting: false,
                    shippingCalculated: false,

                    destinations: [],
                    shippingOptions: [],

                    selectedDestination: null,
                    destinationToken: '',
                    shippingOptionToken: '',

                    shippingCost: 0,
                    formattedWeight: '',
                    errorMessage: '',

                    init() {
                        this.$watch('delivery', (value) => {
                            if (value === 'pickup') {
                                this.resetShipping();
                            }
                        });
                    },

                    get grandTotal() {
                        if (this.delivery === 'pickup') {
                            return this.subtotal;
                        }

                        return this.subtotal
                            + Number(this.shippingCost);
                    },

                    async searchDestinations() {
                        const keyword = this.searchQuery.trim();

                        this.errorMessage = '';
                        this.destinations = [];

                        if (keyword.length < 3) {
                            this.errorMessage =
                                'Masukkan minimal 3 karakter.';

                            return;
                        }

                        this.searching = true;

                        try {
                            const params = new URLSearchParams({
                                search: keyword,
                                limit: '10',
                            });

                            const response = await this.requestJson(
                                `${this.searchUrl}?${params.toString()}`
                            );

                            this.destinations = response.data ?? [];

                            if (this.destinations.length === 0) {
                                this.errorMessage =
                                    'Lokasi tidak ditemukan.';
                            }
                        } catch (error) {
                            this.errorMessage = error.message;
                        } finally {
                            this.searching = false;
                        }
                    },

                    async selectDestination(destination) {
                        this.selectedDestination = destination;
                        this.destinationToken =
                            destination.destination_token;

                        this.searchQuery = destination.label;
                        this.destinations = [];

                        this.shippingOptionToken = '';
                        this.shippingOptions = [];
                        this.shippingCost = 0;
                        this.shippingCalculated = false;
                        this.errorMessage = '';

                        await this.calculateShippingCosts();
                    },

                    async calculateShippingCosts() {
                        if (! this.destinationToken) {
                            return;
                        }

                        this.calculating = true;
                        this.shippingCalculated = false;

                        try {
                            const csrfToken = document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                ?.getAttribute('content');

                            const response = await this.requestJson(
                                this.costUrl,
                                {
                                    method: 'POST',

                                    headers: {
                                        'Content-Type':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                            csrfToken,
                                    },

                                    body: JSON.stringify({
                                        destination_token:
                                            this.destinationToken,
                                    }),
                                }
                            );

                            this.shippingOptions =
                                response.data ?? [];

                            this.formattedWeight =
                                response.formatted_weight ?? '';

                            if (
                                this.shippingOptions.length === 0
                            ) {
                                this.errorMessage =
                                    response.message
                                    ?? 'Layanan pengiriman tidak tersedia.';
                            }
                        } catch (error) {
                            this.errorMessage = error.message;
                        } finally {
                            this.calculating = false;
                            this.shippingCalculated = true;
                        }
                    },

                    selectShippingOption(option) {
                        this.shippingOptionToken =
                            option.shipping_option_token;

                        this.shippingCost =
                            Number(option.cost ?? 0);
                    },

                    resetShipping() {
                        this.searchQuery = '';
                        this.destinations = [];
                        this.shippingOptions = [];

                        this.selectedDestination = null;
                        this.destinationToken = '';
                        this.shippingOptionToken = '';

                        this.shippingCost = 0;
                        this.formattedWeight = '';
                        this.errorMessage = '';

                        this.searching = false;
                        this.calculating = false;
                        this.shippingCalculated = false;
                    },

                    formatRupiah(value) {
                        return new Intl.NumberFormat(
                            'id-ID',
                            {
                                style: 'currency',
                                currency: 'IDR',
                                maximumFractionDigits: 0,
                            }
                        ).format(Number(value ?? 0));
                    },

                    async requestJson(url, options = {}) {
                        const headers = {
                            Accept: 'application/json',
                            ...(options.headers ?? {}),
                        };

                        const response = await fetch(url, {
                            ...options,
                            headers,
                        });

                        const data = await response
                            .json()
                            .catch(() => ({}));

                        if (! response.ok) {
                            const validationErrors = data.errors
                                ? Object
                                    .values(data.errors)
                                    .flat()
                                : [];

                            throw new Error(
                                validationErrors[0]
                                ?? data.message
                                ?? 'Permintaan gagal diproses.'
                            );
                        }

                        return data;
                    },
                }));
            });
        </script>
    @endonce
@endsection