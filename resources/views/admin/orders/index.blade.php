@extends('layouts.admin')

@section('title', 'Pesanan — Admin Kanrejawataa')
@section('page-title', 'Kelola Pesanan')

@section('content')
    <form
        method="GET"
        class="grid gap-3 rounded-2xl border border-stone-200 bg-white p-4 md:grid-cols-[minmax(0,1fr)_200px_220px_auto]"
    >
        <input
            name="search"
            value="{{ request('search') }}"
            class="form-input"
            placeholder="Cari nomor order atau pembeli"
        >

        <select name="status" class="form-input">
            <option value="">Semua status</option>
            @foreach (['diproses', 'siap_diambil', 'dikirim', 'selesai', 'dibatalkan'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>
                    {{ ucwords(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>

        <select name="payment_status" class="form-input">
            <option value="">Semua pembayaran</option>
            @foreach (['belum_bayar', 'menunggu_verifikasi', 'sudah_bayar'] as $status)
                <option value="{{ $status }}" @selected(request('payment_status') === $status)>
                    {{ ucwords(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
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
                        <th>Order</th>
                        <th>Pembeli</th>
                        <th>Pengantaran</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>
                                <b>{{ $order->order_number }}</b>
                                <p class="text-xs text-stone-500">
                                    {{ $order->ordered_at->format('d M Y H:i') }}
                                </p>
                            </td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ $order->delivery_label }}</td>
                            <td>
                                <b>Rp {{ number_format($order->total, 0, ',', '.') }}</b>
                            </td>
                            <td>
                                <x-status-badge :status="$order->payment_status" />
                            </td>
                            <td>
                                <x-status-badge :status="$order->status" />
                            </td>
                            <td>
                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="icon-action icon-action-view"
                                    title="Lihat detail pesanan"
                                    aria-label="Lihat detail pesanan"
                                >
                                    <x-icon name="eye" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-stone-500">
                                Belum ada pesanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $orders->links() }}
    </div>
@endsection
