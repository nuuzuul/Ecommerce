<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'stock' => ['nullable', 'in:low,out'],
        ]);

        $products = Product::query()
            ->with(['category', 'variants'])
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category_id', $category))
            ->when(($filters['stock'] ?? null) === 'low', fn ($query) => $query->whereHas('variants', fn ($variant) => $variant->whereBetween('stock', [1, 5])))
            ->when(($filters['stock'] ?? null) === 'out', fn ($query) => $query->whereDoesntHave('variants', fn ($variant) => $variant->where('stock', '>', 0)))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = DB::transaction(function () use ($request): Product {
            $data = $request->safe()->except(['variants', 'image']);
            $data['slug'] = $this->uniqueSlug($request->input('name'));
            $data['image'] = $request->hasFile('image')
                ? $request->file('image')->store('products', 'public')
                : null;

            $product = Product::create($data);
            $product->variants()->createMany($request->validated()['variants']);
            return $product;
        });

        return redirect()->route('admin.products.show', $product)->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'variants']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load('variants');
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product): void {
            $data = $request->safe()->except(['variants', 'image']);
            $data['slug'] = $this->uniqueSlug($request->input('name'), $product->id);

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);
            $product->variants()->delete();
            $product->variants()->createMany($request->validated()['variants']);
        });

        return redirect()->route('admin.products.show', $product)->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            $product->update(['is_active' => false]);
            return back()->with('success', 'Produk pernah dipesan sehingga dinonaktifkan, bukan dihapus.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 2;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
