<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Services\ShippingCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(): View
    {
        $cart = Cart::query()
            ->where('user_id', auth()->id())
            ->with([
                'items.variant.product',
            ])
            ->firstOrFail();

        abort_if(
            $cart->items->isEmpty(),
            404
        );

        return view(
            'checkout.create',
            compact('cart')
        );
    }

    public function store(
        CheckoutRequest $request,
        ShippingCheckoutService $shippingCheckout
    ): RedirectResponse {
        /*
        | Verifikasi ongkir sebelum membuka transaksi database
        */

        $previewCart = Cart::query()
            ->where(
                'user_id',
                $request->user()->id
            )
            ->with([
                'items.variant.product',
            ])
            ->first();

        if (
            ! $previewCart
            || $previewCart->items->isEmpty()
        ) {
            throw ValidationException::withMessages([
                'cart' => 'Keranjang masih kosong.',
            ]);
        }

        $shippingData = null;

        if (
            $request->input('delivery_method')
            === 'delivery'
        ) {
            $shippingData = $shippingCheckout->verify(
                cart: $previewCart,
                destinationToken:
                    $request->input('destination_token'),
                shippingOptionToken:
                    $request->input('shipping_option_token')
            );
        }

        /*
        | Simpan pesanan
        */

        $order = DB::transaction(
            function () use (
                $request,
                $shippingData
            ): Order {
                $cart = Cart::query()
                    ->where(
                        'user_id',
                        $request->user()->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                $cartItems = $cart->items()
                    ->lockForUpdate()
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw ValidationException::withMessages([
                        'cart' =>
                            'Keranjang sudah kosong atau '
                            . 'telah diproses.',
                    ]);
                }

                $orderLines = collect();
                $subtotal = 0;
                $totalWeight = 0;

                foreach ($cartItems as $cartItem) {
                    $variant = ProductVariant::query()
                        ->with('product')
                        ->whereKey(
                            $cartItem->product_variant_id
                        )
                        ->lockForUpdate()
                        ->first();

                    if (! $variant || ! $variant->product) {
                        throw ValidationException::withMessages([
                            'cart' =>
                                'Salah satu produk tidak tersedia.',
                        ]);
                    }

                    $quantity = (int) $cartItem->quantity;

                    if ($quantity <= 0) {
                        throw ValidationException::withMessages([
                            'cart' =>
                                'Jumlah produk tidak valid.',
                        ]);
                    }

                    if ($quantity > $variant->stock) {
                        throw ValidationException::withMessages([
                            'cart' =>
                                'Stok '
                                . $variant->product->name
                                . ' tidak mencukupi.',
                        ]);
                    }

                    $unitWeight = (int) (
                        $variant->weight_grams ?? 0
                    );

                    if ($unitWeight <= 0) {
                        throw ValidationException::withMessages([
                            'cart' =>
                                'Berat produk '
                                . $variant->product->name
                                . ' belum diatur.',
                        ]);
                    }

                    $price = (float) $variant->price;

                    $lineSubtotal = $price
                        * $quantity;

                    $lineWeight = $unitWeight
                        * $quantity;

                    $subtotal += $lineSubtotal;
                    $totalWeight += $lineWeight;

                    $orderLines->push([
                        'variant' => $variant,
                        'quantity' => $quantity,
                        'price' => $price,
                        'unit_weight_grams' =>
                            $unitWeight,
                        'total_weight_grams' =>
                            $lineWeight,
                        'subtotal' => $lineSubtotal,
                    ]);
                }

                $isDelivery =
                    $request->input('delivery_method')
                    === 'delivery';

                if (
                    $isDelivery
                    && (
                        ! $shippingData
                        || (int) $shippingData[
                            'total_weight_grams'
                        ] !== $totalWeight
                    )
                ) {
                    throw ValidationException::withMessages([
                        'shipping_option_token' =>
                            'Berat keranjang telah berubah. '
                            . 'Silakan cek ongkir kembali.',
                    ]);
                }

                $shippingCost = $isDelivery
                    ? (int) $shippingData[
                        'shipping_cost'
                    ]
                    : 0;

                $order = Order::create([
                    'user_id' =>
                        $request->user()->id,

                    'order_number' =>
                        'KRJ-'
                        . now()->format('Ymd')
                        . '-'
                        . strtoupper(
                            Str::random(6)
                        ),

                    'delivery_method' =>
                        $request->input(
                            'delivery_method'
                        ),

                    'recipient_name' =>
                        $request->input(
                            'recipient_name'
                        ),

                    'recipient_phone' =>
                        $request->input(
                            'recipient_phone'
                        ),

                    'shipping_address' =>
                        $isDelivery
                            ? $request->input(
                                'shipping_address'
                            )
                            : null,

                    'destination_id' =>
                        $isDelivery
                            ? $shippingData[
                                'destination_id'
                            ]
                            : null,

                    'destination_label' =>
                        $isDelivery
                            ? $shippingData[
                                'destination_label'
                            ]
                            : null,

                    'destination_province' =>
                        $isDelivery
                            ? $shippingData[
                                'destination_province'
                            ]
                            : null,

                    'destination_city' =>
                        $isDelivery
                            ? $shippingData[
                                'destination_city'
                            ]
                            : null,

                    'destination_district' =>
                        $isDelivery
                            ? $shippingData[
                                'destination_district'
                            ]
                            : null,

                    'destination_subdistrict' =>
                        $isDelivery
                            ? $shippingData[
                                'destination_subdistrict'
                            ]
                            : null,

                    'destination_postal_code' =>
                        $isDelivery
                            ? $shippingData[
                                'destination_postal_code'
                            ]
                            : null,

                    'courier_code' =>
                        $isDelivery
                            ? $shippingData[
                                'courier_code'
                            ]
                            : null,

                    'courier_name' =>
                        $isDelivery
                            ? $shippingData[
                                'courier_name'
                            ]
                            : null,

                    'courier_service' =>
                        $isDelivery
                            ? $shippingData[
                                'courier_service'
                            ]
                            : null,

                    'courier_description' =>
                        $isDelivery
                            ? $shippingData[
                                'courier_description'
                            ]
                            : null,

                    'shipping_etd' =>
                        $isDelivery
                            ? $shippingData[
                                'shipping_etd'
                            ]
                            : null,

                    'total_weight_grams' =>
                        $totalWeight,

                    'notes' =>
                        $request->input('notes'),

                    'payment_method' =>
                        $request->input(
                            'payment_method'
                        ),

                    'payment_status' =>
                        'belum_bayar',

                    'status' =>
                        'diproses',

                    'subtotal' =>
                        $subtotal,

                    'shipping_cost' =>
                        $shippingCost,

                    'total' =>
                        $subtotal + $shippingCost,

                    'ordered_at' =>
                        now(),
                ]);

                foreach ($orderLines as $line) {
                    $variant = $line['variant'];

                    $order->items()->create([
                        'product_id' =>
                            $variant->product_id,

                        'product_variant_id' =>
                            $variant->id,

                        'product_name' =>
                            $variant->product->name,

                        'variant_label' =>
                            $variant->label,

                        'unit_weight_grams' =>
                            $line[
                                'unit_weight_grams'
                            ],

                        'price' =>
                            $line['price'],

                        'quantity' =>
                            $line['quantity'],

                        'total_weight_grams' =>
                            $line[
                                'total_weight_grams'
                            ],

                        'subtotal' =>
                            $line['subtotal'],
                    ]);

                    $variant->decrement(
                        'stock',
                        $line['quantity']
                    );
                }

                $order->histories()->create([
                    'changed_by' =>
                        $request->user()->id,

                    'status' =>
                        'diproses',

                    'note' =>
                        $isDelivery
                            ? 'Pesanan dibuat dengan '
                                . 'pengiriman '
                                . strtoupper(
                                    $shippingData[
                                        'courier_code'
                                    ]
                                )
                                . ' '
                                . $shippingData[
                                    'courier_service'
                                ]
                                . '.'
                            : 'Pesanan dibuat untuk '
                                . 'diambil sendiri.',
                ]);

                $cart->items()->delete();

                return $order;
            }
        );

        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'success',
                'Checkout berhasil. '
                . 'Silakan lakukan pembayaran dan '
                . 'unggah bukti pembayaran.'
            );
    }
}