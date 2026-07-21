<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\PaymentProofRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->validate([
            'status' => [
                'nullable',
                'in:diproses,siap_diambil,dikirim,selesai,dibatalkan',
            ],
        ])['status'] ?? null;

        $orders = $request->user()
            ->orders()
            ->when(
                $status,
                fn ($query) => $query->where('status', $status)
            )
            ->latest('ordered_at')
            ->paginate(10)
            ->withQueryString();

        return view(
            'orders.index',
            compact('orders', 'status')
        );
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['items', 'histories.changer']);

        return view('orders.show', compact('order'));
    }

    public function uploadPaymentProof(
        PaymentProofRequest $request,
        Order $order
    ): RedirectResponse {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request
            ->file('payment_proof')
            ->store('payment-proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'menunggu_verifikasi',
            'payment_note' => null,
        ]);

        return back()->with(
            'success',
            'Bukti pembayaran berhasil diunggah dan menunggu verifikasi admin.'
        );
    }

    public function paymentProof(
        Request $request,
        Order $order
    ): BinaryFileResponse {
        $isOwner = $order->user_id === $request->user()->id;
        $isAdmin = $request->user()->isAdmin();

        abort_unless($isOwner || $isAdmin, 403);
        abort_unless($order->payment_proof, 404);
        abort_unless(
            Storage::disk('public')->exists($order->payment_proof),
            404
        );

        $path = Storage::disk('public')->path($order->payment_proof);

        return response()->file($path, [
            'Content-Disposition' => 'inline; filename="bukti-pembayaran-' .
                $order->order_number . '"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
