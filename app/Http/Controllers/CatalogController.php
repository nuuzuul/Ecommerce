<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'in:available'],
            'sort' => ['nullable', 'in:newest,price_asc,price_desc,name'],
        ]);

        $products = Product::query()
            ->active()
            ->with(['category', 'variants'])
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($filters['category'] ?? null, fn ($query, $slug) => $query->whereHas('category', fn ($category) => $category->where('slug', $slug)))
            ->when(($filters['min_price'] ?? null) !== null || ($filters['max_price'] ?? null) !== null, function ($query) use ($filters) {
                $query->whereHas('variants', function ($variant) use ($filters) {
                    $variant
                        ->when(($filters['min_price'] ?? null) !== null, fn ($inner) => $inner->where('price', '>=', $filters['min_price']))
                        ->when(($filters['max_price'] ?? null) !== null, fn ($inner) => $inner->where('price', '<=', $filters['max_price']));
                });
            })
            ->when(($filters['stock'] ?? null) === 'available', fn ($query) => $query->whereHas('variants', fn ($variant) => $variant->where('stock', '>', 0)))
            ->when(($filters['sort'] ?? 'newest') === 'name', fn ($query) => $query->orderBy('name'))
            ->when(($filters['sort'] ?? 'newest') === 'price_asc', fn ($query) => $query->withMin('variants', 'price')->orderBy('variants_min_price'))
            ->when(($filters['sort'] ?? 'newest') === 'price_desc', fn ($query) => $query->withMax('variants', 'price')->orderByDesc('variants_max_price'))
            ->when(($filters['sort'] ?? 'newest') === 'newest', fn ($query) => $query->latest())
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);
        $product->load(['category', 'variants']);

        $relatedProducts = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->with(['category', 'variants'])
            ->take(3)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function category(Category $category, Request $request): View
    {
        abort_unless($category->is_active, 404);
        $request->merge(['category' => $category->slug]);
        return $this->index($request);
    }
}
