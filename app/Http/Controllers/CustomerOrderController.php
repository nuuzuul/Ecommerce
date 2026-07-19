<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\PaymentProofRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->validate(['status' => ['nullable', 'in:diproses,siap_diambil,dikirim,selesai']])['status'] ?? null;
        $orders = $request->user()->orders()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('ordered_at')
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders', 'status'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        $order->load(['items', 'histories.changer']);

        return view('orders.show', compact('order'));
    }

    public function uploadPaymentProof(PaymentProofRequest $request, Order $order): RedirectResponse
    {
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');
        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'menunggu_verifikasi',
            'payment_note' => null,
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah dan menunggu verifikasi admin.');
    }
}
