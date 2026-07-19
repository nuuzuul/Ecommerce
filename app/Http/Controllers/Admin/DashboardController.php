<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'products' => Product::count(),
            'customers' => User::whereHas('role', fn ($query) => $query->where('name', 'pembeli'))->count(),
            'orders' => Order::count(),
            'revenue' => (float) Order::where('payment_status', 'sudah_bayar')->sum('total'),
            'waiting_payments' => Order::where('payment_status', 'menunggu_verifikasi')->count(),
            'low_stock' => ProductVariant::where('stock', '<=', 5)->count(),
        ];

        $topProducts = OrderItem::query()
            ->whereHas('order', fn ($query) => $query->where('payment_status', 'sudah_bayar'))
            ->select('product_name', DB::raw('SUM(quantity) as quantity_sold'), DB::raw('SUM(subtotal) as sales'))
            ->groupBy('product_name')
            ->orderByDesc('quantity_sold')
            ->take(5)
            ->get();

        $latestOrders = Order::with('user')->latest('ordered_at')->take(8)->get();

        return view('admin.dashboard.index', compact('stats', 'topProducts', 'latestOrders'));
    }
}
