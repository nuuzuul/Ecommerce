<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AccountDashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $orderSummary = $user->orders()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $latestOrders = $user->orders()->latest('ordered_at')->take(5)->get();

        return view('account.dashboard', compact('orderSummary', 'latestOrders'));
    }
}
