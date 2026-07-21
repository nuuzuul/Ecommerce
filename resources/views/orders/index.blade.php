@extends('layouts.account')
@section('title','Pesanan Saya — Kanrejawataa')
@section('account-content')
<div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
    <div><p class="text-sm font-bold text-amber-700">Riwayat transaksi</p><h1 class="text-3xl font-black">Pesanan saya</h1></div>
    <div class="mt-5 flex flex-wrap gap-2">
        <a
            href="{{ route('orders.index') }}"
            class="filter-pill {{ ! $status
                ? 'filter-pill-active'
                : '' }}"
        >
            Semua
        </a>

        @foreach ([
            'diproses' => 'Diproses',
            'siap_diambil' => 'Siap diambil',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ] as $value => $label)
            <a
                href="{{ route('orders.index', [
                    'status' => $value,
                ]) }}"
                class="filter-pill {{ $status === $value
                    ? 'filter-pill-active'
                    : '' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>
    <div class="mt-6 space-y-4">@forelse($orders as $order)<a href="{{ route('orders.show',$order) }}" class="block rounded-2xl border border-stone-200 p-5 transition hover:border-amber-300 hover:bg-amber-50/40"><div class="flex flex-wrap items-start justify-between gap-3"><div><p class="font-black">{{ $order->order_number }}</p><p class="mt-1 text-sm text-stone-500">{{ $order->ordered_at->format('d M Y, H:i') }} · {{ $order->delivery_label }}</p></div><div class="flex gap-2"><x-status-badge :status="$order->payment_status"/><x-status-badge :status="$order->status"/></div></div><div class="mt-4 flex items-end justify-between"><span class="text-sm text-stone-500">Total</span><b class="text-xl text-amber-700">Rp {{ number_format($order->total,0,',','.') }}</b></div></a>@empty<div class="rounded-2xl border border-dashed border-stone-300 p-10 text-center"><h2 class="font-black">Belum ada pesanan</h2><p class="mt-2 text-stone-500">Pesanan yang kamu checkout akan muncul di sini.</p></div>@endforelse</div>
    <div class="mt-6">{{ $orders->links() }}</div>
</div>
@endsection
