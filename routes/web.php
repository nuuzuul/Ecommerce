<?php

use App\Http\Controllers\AccountDashboardController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShippingController;
use Illuminate\Support\Facades\Route;

/*
| Halaman publik
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/produk', [CatalogController::class, 'index'])
    ->name('products.index');

Route::get('/produk/{product}', [CatalogController::class, 'show'])
    ->name('products.show');

Route::get(
    '/kategori/{category}',
    [CatalogController::class, 'category']
)->name('categories.show');

Route::view('/tentang', 'static.about')
    ->name('about');

Route::view('/kontak', 'static.contact')
    ->name('contact');

/*
| Pengarah dashboard
*/

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

/*
| Halaman pembeli
*/

Route::middleware([
    'auth',
    'role:pembeli',
])->group(function () {
    Route::get('/akun', AccountDashboardController::class)
        ->name('account.dashboard');

    /*
    | Keranjang
    */

    Route::get(
        '/keranjang',
        [CartController::class, 'index']
    )->name('cart.index');

    Route::post(
        '/keranjang',
        [CartController::class, 'store']
    )->name('cart.store');

    Route::patch(
        '/keranjang/{cartItem}',
        [CartController::class, 'update']
    )->name('cart.update');

    Route::delete(
        '/keranjang/{cartItem}',
        [CartController::class, 'destroy']
    )->name('cart.destroy');

    /*
    | RajaOngkir
    */

    Route::get(
        '/pengiriman/tujuan',
        [ShippingController::class, 'searchDestinations']
    )
        ->middleware('throttle:30,1')
        ->name('shipping.destinations');

    Route::post(
        '/pengiriman/ongkir',
        [ShippingController::class, 'calculateCosts']
    )
        ->middleware('throttle:20,1')
        ->name('shipping.costs');

    /*
    | Checkout
    */

    Route::get(
        '/checkout',
        [CheckoutController::class, 'create']
    )->name('checkout.create');

    Route::post(
        '/checkout',
        [CheckoutController::class, 'store']
    )->name('checkout.store');

    /*
    | Pesanan pembeli
    */

    Route::get(
        '/pesanan',
        [CustomerOrderController::class, 'index']
    )->name('orders.index');

    Route::get(
        '/pesanan/{order}',
        [CustomerOrderController::class, 'show']
    )->name('orders.show');

    Route::post(
        '/pesanan/{order}/bukti-pembayaran',
        [CustomerOrderController::class, 'uploadPaymentProof']
    )->name('orders.payment-proof');
});

/*
| Panel admin
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'role:admin',
    ])
    ->group(function () {
        Route::get('/', AdminDashboardController::class)
            ->name('dashboard');

        Route::resource(
            'categories',
            AdminCategoryController::class
        )->except('show');

        Route::resource(
            'products',
            AdminProductController::class
        );

        Route::get(
            'orders',
            [AdminOrderController::class, 'index']
        )->name('orders.index');

        Route::get(
            'orders/{order}',
            [AdminOrderController::class, 'show']
        )->name('orders.show');

        Route::patch(
            'orders/{order}/status',
            [AdminOrderController::class, 'updateStatus']
        )->name('orders.status');

        Route::patch(
            'orders/{order}/verify-payment',
            [AdminOrderController::class, 'verifyPayment']
        )->name('orders.verify-payment');

        Route::patch(
            'orders/{order}/reject-payment',
            [AdminOrderController::class, 'rejectPayment']
        )->name('orders.reject-payment');
    });

/*
| Halaman yang dapat diakses pengguna terautentikasi
*/

Route::middleware('auth')->group(function () {
    Route::get(
        '/pesanan/{order}/bukti-pembayaran',
        [CustomerOrderController::class, 'paymentProof']
    )->name('orders.payment-proof.show');

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');
});

/*
| Route autentikasi Breeze
*/

require __DIR__ . '/auth.php';