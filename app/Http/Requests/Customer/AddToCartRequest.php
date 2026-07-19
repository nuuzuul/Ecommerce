<?php

namespace App\Http\Requests\Customer;

use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBuyer() === true;
    }

    public function rules(): array
    {
        return [
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $variant = ProductVariant::find($this->integer('product_variant_id'));
                if ($variant && $this->integer('quantity') > $variant->stock) {
                    $validator->errors()->add('quantity', 'Jumlah melebihi stok yang tersedia.');
                }
            },
        ];
    }
}
