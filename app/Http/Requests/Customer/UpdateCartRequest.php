<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBuyer() === true;
    }

    public function rules(): array
    {
        return ['quantity' => ['required', 'integer', 'min:1', 'max:99']];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $item = $this->route('cartItem');
                if ($item && $this->integer('quantity') > $item->variant->stock) {
                    $validator->errors()->add('quantity', 'Jumlah melebihi stok yang tersedia.');
                }
            },
        ];
    }
}
