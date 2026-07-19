<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:3000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.label' => ['nullable', 'string', 'max:50'],
            'variants.*.weight_grams' => ['nullable', 'integer', 'min:1'],
            'variants.*.price' => ['required', 'numeric', 'min:1000'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $category = Category::find($this->integer('category_id'));
                $variants = collect($this->input('variants', []));

                if (! $category) {
                    return;
                }

                if ($category->uses_variants) {
                    $labels = $variants->pluck('label')->filter()->map(fn ($label) => strtolower(trim((string) $label)));
                    $weights = $variants->pluck('weight_grams')->map(fn ($weight) => (int) $weight);
                    $required = collect(['500 gram', '1 kg']);
                    $requiredWeights = collect([500, 1000]);

                    if ($variants->count() !== 2 || $required->diff($labels)->isNotEmpty() || $requiredWeights->diff($weights)->isNotEmpty()) {
                        $validator->errors()->add('variants', 'Produk kue kering wajib memiliki tepat dua ukuran: 500 gram dan 1 kg.');
                    }
                } elseif ($variants->count() !== 1 || filled($variants->first()['label'] ?? null) || filled($variants->first()['weight_grams'] ?? null)) {
                    $validator->errors()->add('variants', 'Produk kue tradisional hanya menggunakan satu harga dan tidak menampilkan ukuran.');
                }
            },
        ];
    }
}
