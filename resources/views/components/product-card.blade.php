@props(['product'])
<article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-amber-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
    <a href="{{ route('products.show', $product) }}" class="aspect-[4/3] overflow-hidden bg-amber-100">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-start justify-between gap-3">
            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">{{ $product->category->name }}</span>
            <span class="text-xs font-semibold {{ $product->total_stock > 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $product->total_stock > 0 ? 'Tersedia' : 'Stok habis' }}</span>
        </div>
        <h3 class="mt-4 text-xl font-black text-stone-900"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
        <p class="mt-2 line-clamp-2 text-sm leading-6 text-stone-500">{{ $product->description }}</p>
        <div class="mt-auto flex items-end justify-between gap-3 pt-5">
            <div><p class="text-xs text-stone-400">Mulai dari</p><p class="text-lg font-black text-amber-700">Rp {{ number_format($product->minimum_price, 0, ',', '.') }}</p></div>
            <a href="{{ route('products.show', $product) }}" class="rounded-xl bg-stone-900 px-4 py-2 text-sm font-bold text-white hover:bg-amber-600">Lihat detail</a>
        </div>
    </div>
</article>
