<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()->where('is_active', true)->withCount(['products' => fn ($query) => $query->where('is_active', true)])->get();
        $featuredProducts = Product::query()
            ->active()
            ->where('is_featured', true)
            ->with(['category', 'variants'])
            ->latest()
            ->take(6)
            ->get();

        return view('home.index', compact('categories', 'featuredProducts'));
    }
}
