@extends('layouts.admin')
@section('title','Dashboard Admin — Kanrejawataa')
@section('page-title','Dashboard')
@section('content')
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach([
        ['Produk',$stats['products'],''],
        ['Pelanggan',$stats['customers'],''],
        ['Total pesanan',$stats['orders'],''],
        ['Total penjualan','Rp '.number_format($stats['revenue'],0,',','.'),''],
        ['Menunggu verifikasi',$stats['waiting_payments'],''],
        ['Stok menipis',$stats['low_stock'],''],
    ] as [$label,$value,$icon])
        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm"><div class="flex items-start justify-between"><div><p class="text-sm text-stone-500">{{ $label }}</p><p class="mt-2 text-2xl font-black text-stone-900">{{ $value }}</p></div><span class="text-3xl">{{ $icon }}</span></div></div>
    @endforeach
</div>
<div class="mt-6 grid gap-6 xl:grid-cols-2">
    <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm"><div class="flex justify-between"><h2 class="text-xl font-black">Pesanan terbaru</h2><a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-amber-700">Lihat semua</a></div><div class="mt-4 divide-y divide-stone-100">@forelse($latestOrders as $order)<a href="{{ route('admin.orders.show',$order) }}" class="flex items-center justify-between gap-3 py-4"><div><b>{{ $order->order_number }}</b><p class="text-sm text-stone-500">{{ $order->user->name }} · {{ $order->ordered_at->format('d M Y') }}</p></div><div class="text-right"><x-status-badge :status="$order->payment_status"/><p class="mt-1 font-bold">Rp {{ number_format($order->total,0,',','.') }}</p></div></a>@empty<p class="py-8 text-center text-stone-500">Belum ada pesanan.</p>@endforelse</div></section>
    <section class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm"><h2 class="text-xl font-black">Produk terlaris</h2><div class="mt-4 divide-y divide-stone-100">@forelse($topProducts as $index=>$item)<div class="flex justify-between gap-3 py-4"><div class="flex gap-3"><span class="grid h-8 w-8 place-items-center rounded-full bg-amber-100 font-black text-amber-800">{{ $index+1 }}</span><div><b>{{ $item->product_name }}</b><p class="text-sm text-stone-500">{{ $item->quantity_sold }} item terjual</p></div></div><b>Rp {{ number_format($item->sales,0,',','.') }}</b></div>@empty<p class="py-8 text-center text-stone-500">Belum ada data penjualan.</p>@endforelse</div></section>
</div>
@endsection
