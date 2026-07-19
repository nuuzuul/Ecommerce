<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\AddToCartRequest;
use App\Http\Requests\Customer\UpdateCartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $this->cartFor($request);
        $cart->load('items.variant.product.category');

        return view('cart.index', compact('cart'));
    }

    public function store(AddToCartRequest $request): RedirectResponse
    {
        $variant = ProductVariant::with('product')->findOrFail($request->integer('product_variant_id'));
        abort_unless($variant->product->is_active, 404);

        $cart = $this->cartFor($request);
        $item = $cart->items()->firstOrNew(['product_variant_id' => $variant->id]);
        $newQuantity = ($item->exists ? $item->quantity : 0) + $request->integer('quantity');

        if ($newQuantity > $variant->stock) {
            return back()->withErrors(['quantity' => 'Total jumlah di keranjang melebihi stok.']);
        }

        $item->quantity = $newQuantity;
        $item->save();

        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(UpdateCartRequest $request, CartItem $cartItem): RedirectResponse
    {
        $this->ensureOwner($request, $cartItem);
        $cartItem->update(['quantity' => $request->integer('quantity')]);

        return back()->with('success', 'Jumlah produk diperbarui.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->ensureOwner($request, $cartItem);
        $cartItem->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    private function cartFor(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id]);
    }

    private function ensureOwner(Request $request, CartItem $cartItem): void
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 403);
    }
}
