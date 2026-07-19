<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create(): View
    {
        $cart = Cart::query()
            ->where('user_id', auth()->id())
            ->with('items.variant.product')
            ->firstOrFail();

        abort_if($cart->items->isEmpty(), 404);

        return view('checkout.create', compact('cart'));
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $order = DB::transaction(function () use ($request): Order {
            $cart = Cart::query()
                ->where('user_id', $request->user()->id)
                ->with('items.variant.product')
                ->lockForUpdate()
                ->firstOrFail();

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Keranjang kosong.']);
            }

            foreach ($cart->items as $item) {
                $item->variant->refresh();
                if ($item->quantity > $item->variant->stock) {
                    throw ValidationException::withMessages(['cart' => "Stok {$item->variant->product->name} tidak mencukupi."]);
                }
            }

            $subtotal = $cart->items->sum(fn ($item) => $item->subtotal);
            $shippingCost = $request->input('delivery_method') === 'delivery'
                ? config('kanrejawataa.delivery_fee')
                : 0;

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'KRJ-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
                'delivery_method' => $request->input('delivery_method'),
                'recipient_name' => $request->input('recipient_name'),
                'recipient_phone' => $request->input('recipient_phone'),
                'shipping_address' => $request->input('delivery_method') === 'delivery' ? $request->input('shipping_address') : null,
                'notes' => $request->input('notes'),
                'payment_method' => $request->input('payment_method'),
                'payment_status' => 'belum_bayar',
                'status' => 'diproses',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
                'ordered_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                $variant = $item->variant;
                $order->items()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_label' => $variant->label,
                    'price' => $variant->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);
                $variant->decrement('stock', $item->quantity);
            }

            $order->histories()->create([
                'changed_by' => $request->user()->id,
                'status' => 'diproses',
                'note' => 'Pesanan dibuat oleh pembeli.',
            ]);

            $cart->items()->delete();

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Checkout berhasil. Silakan unggah bukti pembayaran.');
    }
}
