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
            'recipient_name' => [
                'required',
                'string',
                'max:100',
            ],

            'recipient_phone' => [
                'required',
                'string',
                'max:20',
            ],

            'delivery_method' => [
                'required',
                Rule::in([
                    'pickup',
                    'delivery',
                ]),
            ],

            'shipping_address' => [
                'nullable',
                'required_if:delivery_method,delivery',
                'string',
                'max:1000',
            ],

            'destination_token' => [
                'nullable',
                'required_if:delivery_method,delivery',
                'string',
                'max:10000',
            ],

            'shipping_option_token' => [
                'nullable',
                'required_if:delivery_method,delivery',
                'string',
                'max:10000',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'bank_transfer',
                    'qris',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'recipient_name.required' =>
                'Nama penerima wajib diisi.',

            'recipient_phone.required' =>
                'Nomor telepon wajib diisi.',

            'shipping_address.required_if' =>
                'Alamat lengkap wajib diisi untuk pesanan yang dikirim.',

            'destination_token.required_if' =>
                'Pilih lokasi tujuan pengiriman terlebih dahulu.',

            'shipping_option_token.required_if' =>
                'Pilih salah satu layanan pengiriman terlebih dahulu.',

            'payment_method.required' =>
                'Pilih metode pembayaran.',
        ];
    }
}