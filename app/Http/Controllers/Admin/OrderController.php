<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectPaymentRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => [
                'nullable',
                'in:diproses,siap_diambil,dikirim,selesai,dibatalkan',
            ],
            'payment_status' => [
                'nullable',
                'in:belum_bayar,menunggu_verifikasi,sudah_bayar',
            ],
        ]);

        $orders = Order::query()
            ->with('user')
            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {
                    $query->where(function ($inner) use ($search) {
                        $inner
                            ->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas(
                                'user',
                                fn ($user) => $user->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                            );
                    });
                }
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status)
            )
            ->when(
                $filters['payment_status'] ?? null,
                fn ($query, $status) => $query->where(
                    'payment_status',
                    $status
                )
            )
            ->latest('ordered_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items', 'histories.changer']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order
    ): RedirectResponse {
        $newStatus = $request->input('status');
        $oldStatus = $order->status;

        DB::transaction(function () use (
            $request,
            $order,
            $newStatus,
            $oldStatus
        ): void {
            if (
                $newStatus === 'dibatalkan'
                && $oldStatus !== 'dibatalkan'
            ) {
                $order->load('items');

                foreach ($order->items as $item) {
                    if (! $item->product_variant_id) {
                        continue;
                    }

                    $item->variant()
                        ->lockForUpdate()
                        ->first()
                        ?->increment('stock', $item->quantity);
                }
            }

            $order->update([
                'status' => $newStatus,
            ]);

            $order->histories()->create([
                'changed_by' => $request->user()->id,
                'status' => $newStatus,
                'note' => $request->input('note')
                    ?: (
                        $newStatus === 'dibatalkan'
                            ? 'Pesanan dibatalkan oleh admin.'
                            : null
                    ),
            ]);
        });

        return back()->with(
            'success',
            $newStatus === 'dibatalkan'
                ? 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.'
                : 'Status pesanan berhasil diperbarui.'
        );
    }

    public function verifyPayment(Order $order): RedirectResponse
    {
        abort_unless(
            $order->payment_status === 'menunggu_verifikasi'
                && $order->payment_proof,
            422
        );

        $order->update([
            'payment_status' => 'sudah_bayar',
            'payment_note' => null,
        ]);

        return back()->with('success', 'Pembayaran telah diverifikasi.');
    }

    public function rejectPayment(
        RejectPaymentRequest $request,
        Order $order
    ): RedirectResponse {
        abort_unless(
            $order->payment_status === 'menunggu_verifikasi',
            422
        );

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $order->update([
            'payment_status' => 'belum_bayar',
            'payment_proof' => null,
            'payment_note' => $request->input('payment_note'),
        ]);

        return back()->with(
            'success',
            'Bukti pembayaran ditolak dan pembeli dapat mengunggah ulang.'
        );
    }
}
