<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PaymentProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        return $this->user()?->isBuyer() === true && $order?->user_id === $this->user()->id;
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->route('order')?->payment_status === 'sudah_bayar') {
                    $validator->errors()->add('payment_proof', 'Pembayaran pesanan ini sudah diverifikasi.');
                }
            },
        ];
    }

    public function rules(): array
    {
        return [
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ];
    }
}
