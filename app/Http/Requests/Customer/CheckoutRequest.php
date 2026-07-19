<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBuyer() === true;
    }

    public function rules(): array
    {
        return [
            'recipient_name' => ['required', 'string', 'max:100'],
            'recipient_phone' => ['required', 'string', 'max:20'],
            'delivery_method' => ['required', Rule::in(['pickup', 'delivery'])],
            'shipping_address' => ['nullable', 'required_if:delivery_method,delivery', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::in(['bank_transfer', 'qris'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
