@php
    $selectedCategoryId = (int) old(
        'category_id',
        $product->category_id ?? $categories->first()?->id
    );

    $initialVariants = old(
        'variants',
        isset($product)
            ? $product->variants
                ->map(fn ($variant) => [
                    'label' => $variant->label,
                    'weight_grams' => $variant->weight_grams,
                    'price' => (float) $variant->price,
                    'stock' => $variant->stock,
                ])
                ->values()
                ->all()
            : [
                [
                    'label' => '500 gram',
                    'weight_grams' => 500,
                    'price' => '',
                    'stock' => '',
                ],
                [
                    'label' => '1 kg',
                    'weight_grams' => 1000,
                    'price' => '',
                    'stock' => '',
                ],
            ]
    );

    $categoryMap = $categories
        ->mapWithKeys(fn ($category) => [
            $category->id => [
                'uses_variants' => $category->uses_variants,
            ],
        ])
        ->all();
@endphp
<div x-data="productForm(@js($categoryMap), @js($selectedCategoryId), @js($initialVariants))" class="space-y-6">
    <div class="grid gap-5 md:grid-cols-2">
        <div><label class="form-label">Nama produk</label><input name="name" value="{{ old('name',$product->name ?? '') }}" class="form-input">@error('name')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label">Kategori</label><select name="category_id" x-model.number="categoryId" @change="syncVariants" class="form-input">@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>@error('category_id')<p class="form-error">{{ $message }}</p>@enderror</div>
    </div>
    <div><label class="form-label">Deskripsi produk</label><textarea name="description" rows="5" class="form-input">{{ old('description',$product->description ?? '') }}</textarea>@error('description')<p class="form-error">{{ $message }}</p>@enderror</div>
    <div><label class="form-label">Foto produk</label><input type="file" name="image" accept="image/*" class="form-input">@if(isset($product) && $product->image)<img src="{{ $product->image_url }}" class="mt-3 h-28 w-36 rounded-xl object-cover">@endif @error('image')<p class="form-error">{{ $message }}</p>@enderror</div>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <div><h3 class="text-lg font-black">Harga dan stok</h3><p class="text-sm text-stone-600" x-text="usesVariants ? 'Kue kering wajib memiliki ukuran 500 gram dan 1 kg.' : 'Kue tradisional menggunakan satu harga tanpa ukuran.'"></p></div>
        @error('variants')<p class="form-error mt-2">{{ $message }}</p>@enderror
        <div class="mt-4 space-y-3">
            <template
                x-for="(variant, index) in variants"
                :key="index"
            >
                <div class="grid gap-3 rounded-xl bg-white p-4 sm:grid-cols-4">

                    <template x-if="usesVariants">
                        <div>
                            <label class="form-label">
                                Ukuran
                            </label>

                            <input
                                type="text"
                                :name="`variants[${index}][label]`"
                                x-model="variant.label"
                                class="form-input"
                                readonly
                            >

                            <input
                                type="hidden"
                                :name="`variants[${index}][weight_grams]`"
                                x-model="variant.weight_grams"
                            >
                        </div>
                    </template>

                    <template x-if="! usesVariants">
                        <div>
                            <label class="form-label">
                                Berat kirim (gram)
                            </label>

                            <input
                                type="number"
                                min="1"
                                max="30000"
                                :name="`variants[${index}][weight_grams]`"
                                x-model="variant.weight_grams"
                                class="form-input"
                                placeholder="Contoh: 750"
                            >

                            <input
                                type="hidden"
                                :name="`variants[${index}][label]`"
                                value=""
                            >

                            <p class="mt-1 text-xs text-stone-500">
                                Berat ini hanya digunakan untuk menghitung ongkir.
                            </p>
                        </div>
                    </template>

                    <div class="sm:col-span-2">
                        <label class="form-label">
                            Harga
                        </label>

                        <input
                            type="number"
                            min="1000"
                            :name="`variants[${index}][price]`"
                            x-model="variant.price"
                            class="form-input"
                            placeholder="Contoh: 85000"
                        >
                    </div>

                    <div>
                        <label class="form-label">
                            Stok
                        </label>

                        <input
                            type="number"
                            min="0"
                            :name="`variants[${index}][stock]`"
                            x-model="variant.stock"
                            class="form-input"
                        >
                    </div>
                </div>
            </template>
        </div>
        @foreach($errors->get('variants.*') as $messages) @foreach($messages as $message)<p class="form-error mt-2">{{ $message }}</p>@endforeach @endforeach
    </section>

    <div class="grid gap-3 sm:grid-cols-2">
        <label class="flex items-center gap-3 rounded-2xl border border-stone-200 p-4"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$product->is_featured ?? false)) class="rounded border-stone-300 text-amber-600"><span><b>Produk unggulan</b><small class="block text-stone-500">Tampilkan di halaman beranda.</small></span></label>
        <label class="flex items-center gap-3 rounded-2xl border border-stone-200 p-4"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$product->is_active ?? true)) class="rounded border-stone-300 text-amber-600"><span><b>Produk aktif</b><small class="block text-stone-500">Tampilkan pada katalog publik.</small></span></label>
    </div>
</div>
@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data(
                'productForm',
                (
                    categories,
                    initialCategory,
                    initialVariants
                ) => ({
                    categories,
                    categoryId: initialCategory,
                    variants: initialVariants,

                    get usesVariants() {
                        return Boolean(
                            this.categories[this.categoryId]
                                ?.uses_variants
                        );
                    },

                    syncVariants() {
                        this.variants = this.usesVariants
                            ? [
                                {
                                    label: '500 gram',
                                    weight_grams: 500,
                                    price: '',
                                    stock: '',
                                },
                                {
                                    label: '1 kg',
                                    weight_grams: 1000,
                                    price: '',
                                    stock: '',
                                },
                            ]
                            : [
                                {
                                    label: '',
                                    weight_grams: '',
                                    price: '',
                                    stock: '',
                                },
                            ];
                    },
                })
            );
        });
    </script>
@endonce